<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;
use App\Services\PhysicalExamCdssService;
use App\Services\LabValuesCdssService;
use App\Services\VitalCdssService;
use App\Services\IntakeAndOutputCdssService;
use App\Services\ActOfDailyLivingCdssService;
use App\Models\Patient;

class NursingDiagnosisCdssService
{
    private $physicalExamCdssService;
    private $labValuesCdssService;
    private $vitalCdssService;
    private $intakeAndOutputCdssService;
    private $actOfDailyLivingCdssService;
    private $adpieRules;

    public function __construct(
        PhysicalExamCdssService $physicalExamCdssService,
        LabValuesCdssService $labValuesCdssService,
        VitalCdssService $vitalCdssService,
        IntakeAndOutputCdssService $intakeAndOutputCdssService,
        ActOfDailyLivingCdssService $actOfDailyLivingCdssService
    ) {
        $this->physicalExamCdssService = $physicalExamCdssService;
        $this->labValuesCdssService = $labValuesCdssService;
        $this->vitalCdssService = $vitalCdssService;
        $this->intakeAndOutputCdssService = $intakeAndOutputCdssService;
        $this->actOfDailyLivingCdssService = $actOfDailyLivingCdssService;
        $this->adpieRules = [];
    }

    /**
     * Loads and caches rules for a specific component.
     * This version is robust and handles YAML with or without a top-level key.
     */
    private function getRulesForComponent(string $componentName)
    {
        if (isset($this->adpieRules[$componentName])) {
            return $this->adpieRules[$componentName];
        }

        $rulesDirectory = storage_path('app/private/adpie/' . $componentName . '/rules');

        if (!File::isDirectory($rulesDirectory)) {
            error_log("ADPIE rules directory not found for component: " . $componentName);
            $this->adpieRules[$componentName] = [];
            return [];
        }

        $files = File::files($rulesDirectory);
        $mergedRules = [];

        foreach ($files as $file) {
            if (in_array($file->getExtension(), ['yaml', 'yml'])) {
                try {
                    $parsedYaml = Yaml::parseFile($file->getPathname());
                    if (is_array($parsedYaml)) {
                        $stepName = $file->getFilenameWithoutExtension(); // e.g., "diagnosis"

                        // Check if the YAML file has a top-level key (e.g., "diagnosis:")
                        if (isset($parsedYaml[$stepName]) && is_array($parsedYaml[$stepName])) {
                            // Case 1: YAML has top-level key (e.g., diagnosis: - keywords: [...])
                            $mergedRules[$stepName] = $parsedYaml[$stepName];
                        } else {
                            // Case 2: YAML is just a list (e.g., - keywords: [...])
                            $mergedRules[$stepName] = $parsedYaml;
                        }
                    }
                } catch (\Exception $e) {
                    error_log("Failed to parse ADPIE YAML file: " . $file->getPathname() . " - " . $e->getMessage());
                }
            }
        }

        $this->adpieRules[$componentName] = $mergedRules;
        return $mergedRules;
    }


    // --- HELPER FUNCTION ---
    private function createAlert($recommendations)
    {
        if (empty($recommendations)) {
            return null;
        }

        $messageHtml = '<ul class="list-disc list-inside text-left">';
        foreach ($recommendations as $rec) {
            $messageHtml .= '<li>' . htmlspecialchars($rec) . '</li>';
        }
        $messageHtml .= '</ul>';

        $plainTextMessage = implode(' ', $recommendations);

        return (object) [
            'level' => 'recommendation',
            'message' => $messageHtml,       // For the UI
            'raw_message' => $plainTextMessage // For the database
        ];
    }

    /**
     * --- THIS IS THE NEW, BETTER ALGORITHM ---
     * Runs analysis for a single ADPIE finding against its rules.
     * Supports 'match_type: any' (OR) and 'match_type: all' (AND).
     */
    private function runAdpieAnalysis(string $finding, array $rules): array
    {
        $findingLower = strtolower(trim($finding));
        $recommendations = [];

        if (empty($rules)) {
            return [];
        }

        foreach ($rules as $rule) {
            // Add a check to ensure $rule is a valid array with 'keywords'
            if (!is_array($rule) || !isset($rule['keywords'])) {
                continue; // Skip this invalid rule
            }

            // Default to 'all' (AND) if 'match_type' isn't specified
            $matchType = $rule['match_type'] ?? 'all';
            $negate = $rule['negate'] ?? false;
            $match = false;

            if ($matchType === 'any') {
                // --- "OR" LOGIC ---
                // If *any* keyword is found, the rule matches.
                $match = false; // Start with no match
                foreach ($rule['keywords'] as $keyword) {
                    if (str_contains($findingLower, strtolower($keyword))) {
                        $match = true; // Found one!
                        break; // Stop searching
                    }
                }
            } else {
                // --- "AND" LOGIC (Your old logic) ---
                // *All* keywords must be found.
                $match = true; // Start assuming it matches
                foreach ($rule['keywords'] as $keyword) {
                    if (!str_contains($findingLower, strtolower($keyword))) {
                        $match = false; // Missing one!
                        break; // Stop searching
                    }
                }
            }

            // Apply negation if set
            if ($negate) {
                $match = !$match;
            }

            if ($match) {
                $recommendations[] = $rule['alert'];
            }
        }
        return $recommendations;
    }

    /**
     * Generates comprehensive nursing diagnosis rules (This is the 'on-submit' logic)
     */
    public function generateNursingDiagnosisRules(string $componentName, array $componentData, array $nurseInput, Patient $patient)
    {
        $allAlerts = [];

        // 1. Get alerts from the component's CDSS service
        switch ($componentName) {
            case 'physical-exam':
                $componentAlerts = $this->physicalExamCdssService->analyzeFindings($componentData);
                foreach ($componentAlerts as $key => $alert) {
                    // --- FIX: Check for "No Findings" and skip ---
                    if ($alert !== 'No Findings' && $alert !== null) {
                        $allAlerts[] = ['source' => 'Physical Exam', 'field' => $key, 'alert' => $alert];
                    }
                }
                break;
            case 'lab-values':
                $ageGroup = $patient->getAgeGroup(); // Assuming a method to get age group
                foreach ($componentData as $param => $value) {
                    if ($value === null)
                        continue; // Skip null values
                    $alert = $this->labValuesCdssService->checkLabResult($param, $value, $ageGroup);
                    if ($alert['severity'] !== LabValuesCdssService::NONE) {
                        $allAlerts[] = ['source' => 'Lab Values', 'field' => $param, 'alert' => $alert['alert'], 'severity' => $alert['severity']];
                    }
                }
                break;
            case 'vital-signs':
                $alert = $this->vitalCdssService->analyzeVitalsForAlerts($componentData);
                if ($alert['severity'] !== VitalCdssService::NONE) {
                    $allAlerts[] = ['source' => 'Vital Signs', 'alert' => $alert['alert'], 'severity' => $alert['severity']];
                }
                break;
            case 'intake-and-output':
                $alert = $this->intakeAndOutputCdssService->analyzeIntakeOutput($componentData);
                if ($alert['severity'] !== IntakeAndOutputCdssService::NONE) {
                    $allAlerts[] = ['source' => 'Intake and Output', 'alert' => $alert['alert'], 'severity' => $alert['severity']];
                }
                break;
            case 'act-of-daily-living':
                $componentAlerts = $this->actOfDailyLivingCdssService->analyzeFindings($componentData);
                foreach ($componentAlerts as $key => $alert) {
                    if ($alert && $alert['severity'] !== ActOfDailyLivingCdssService::NONE) {
                        $allAlerts[] = ['source' => 'Act of Daily Living', 'field' => $key, 'alert' => $alert['alert'], 'severity' => $alert['severity']];
                    }
                }
                break;
            default:
                break;
        }

        // --- FIX: Handle the case where there are NO alerts ---
        if (empty($allAlerts)) {
            $allAlerts[] = ['source' => $componentName, 'field' => 'general', 'alert' => 'No Findings'];
        }
        // --- END OF FIX ---

        return [
            'alerts' => $allAlerts,
            'rule_file_path' => null
        ];
    }

    //
    // --- UPDATED REAL-TIME ANALYSIS METHODS ---
    //

    public function analyzeDiagnosis(string $componentName, string $finding)
    {
        $componentRules = $this->getRulesForComponent($componentName);
        $rules = $componentRules['diagnosis'] ?? [];
        $recommendations = $this->runAdpieAnalysis($finding, $rules);
        return $this->createAlert($recommendations);
    }

    public function analyzePlanning(string $componentName, string $finding)
    {
        $componentRules = $this->getRulesForComponent($componentName);
        $rules = $componentRules['planning'] ?? [];
        $recommendations = $this->runAdpieAnalysis($finding, $rules);
        return $this->createAlert($recommendations);
    }

    public function analyzeIntervention(string $componentName, string $finding)
    {
        $componentRules = $this->getRulesForComponent($componentName);
        $rules = $componentRules['intervention'] ?? [];
        $recommendations = $this->runAdpieAnalysis($finding, $rules);
        return $this->createAlert($recommendations);
    }

    public function analyzeEvaluation(string $componentName, string $finding)
    {
        $componentRules = $this->getRulesForComponent($componentName);
        $rules = $componentRules['evaluation'] ?? [];
        $recommendations = $this->runAdpieAnalysis($finding, $rules);
        return $this->createAlert($recommendations);
    }
}