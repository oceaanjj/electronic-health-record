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

    // Define severity scores for ranking
    private const SEVERITY_SCORES = [
        'critical' => 3,
        'warning' => 2,
        'info' => 1,
    ];

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
                        $stepName = $file->getFilenameWithoutExtension();

                        // Handle YAML that is just a list OR has a top-level key
                        if (isset($parsedYaml[$stepName]) && is_array($parsedYaml[$stepName])) {
                            $mergedRules[$stepName] = $parsedYaml[$stepName];
                        } else {
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
            'level' => 'recommendation', // This 'level' is just for the JS display
            'message' => $messageHtml,       // For the UI
            'raw_message' => $plainTextMessage // For the database
        ];
    }

    /**
     * --- MODIFIED ALGORITHM ---
     * This function now returns the *entire rule object* for all matches,
     * not just the alert string. This is needed for ranking.
     */
    private function runAdpieAnalysis(string $finding, array $rules): array
    {
        $findingLower = strtolower(trim($finding));
        $matchedRules = []; // We will return all matching rules

        if (empty($rules)) {
            return [];
        }

        foreach ($rules as $rule) {
            // Fix for "Undefined array key 'keywords'"
            if (!is_array($rule) || !isset($rule['keywords'])) {
                continue; // Skip this invalid rule
            }

            $matchType = $rule['match_type'] ?? 'all';
            $negate = $rule['negate'] ?? false;
            $match = false;

            if ($matchType === 'any') {
                $match = false;
                foreach ($rule['keywords'] as $keyword) {
                    if (str_contains($findingLower, strtolower($keyword))) {
                        $match = true;
                        break;
                    }
                }
            } else {
                $match = true;
                foreach ($rule['keywords'] as $keyword) {
                    if (!str_contains($findingLower, strtolower($keyword))) {
                        $match = false;
                        break;
                    }
                }
            }

            if ($negate) {
                $match = !$match;
            }

            if ($match) {
                $matchedRules[] = $rule; // Add the entire rule object
            }
        }
        return $matchedRules;
    }

    /**
     * --- NEW FUNCTION ---
     * This function scores, ranks, and filters the matched rules
     * to prevent "Alert Fatigue".
     */
    private function rankAndFilterAlerts(array $matchedRules, int $limit = 3): array
    {
        usort($matchedRules, function ($a, $b) {
            // 1. Prioritize by Severity (Critical > Warning > Info)
            $severityA = self::SEVERITY_SCORES[strtolower($a['severity'] ?? 'info')] ?? 0;
            $severityB = self::SEVERITY_SCORES[strtolower($b['severity'] ?? 'info')] ?? 0;

            if ($severityA !== $severityB) {
                return $severityB <=> $severityA; // Sort descending by severity
            }

            // 2. Prioritize by Specificity (More keywords is better)
            $keywordCountA = count($a['keywords']);
            $keywordCountB = count($b['keywords']);

            if ($keywordCountA !== $keywordCountB) {
                return $keywordCountB <=> $keywordCountA; // Sort descending by keyword count
            }

            // 3. De-prioritize 'negate' rules (they are less specific)
            $negateA = $a['negate'] ?? false;
            $negateB = $b['negate'] ?? false;

            return $negateA <=> $negateB; // Sort ascending (false comes first)
        });

        // Get the top N rules (default 3)
        $topRules = array_slice($matchedRules, 0, $limit);

        // Return just the alert strings for these top rules
        return array_column($topRules, 'alert');
    }

    // (This function is for your on-submit logic, it remains unchanged)
    public function generateNursingDiagnosisRules(string $componentName, array $componentData, array $nurseInput, Patient $patient)
    {
        // ... (Your existing code is correct)
        $allAlerts = [];
        switch ($componentName) {
            case 'physical-exam':
                $componentAlerts = $this->physicalExamCdssService->analyzeFindings($componentData);
                foreach ($componentAlerts as $key => $alert) {
                    if ($alert !== 'No Findings' && $alert !== null) {
                        $allAlerts[] = ['source' => 'Physical Exam', 'field' => $key, 'alert' => $alert];
                    }
                }
                break;
            // ... (rest of your cases) ...
        }

        if (empty($allAlerts)) {
            $allAlerts[] = ['source' => $componentName, 'field' => 'general', 'alert' => 'No Findings'];
        }

        return [
            'alerts' => $allAlerts,
            'rule_file_path' => null
        ];
    }

    //
    // --- UPDATED REAL-TIME ANALYSIS METHODS ---
    // They now find all matches, rank/filter them, and then create the alert.
    //

    public function analyzeDiagnosis(string $componentName, string $finding)
    {
        $componentRules = $this->getRulesForComponent($componentName);
        $rules = $componentRules['diagnosis'] ?? [];

        $matchedRules = $this->runAdpieAnalysis($finding, $rules);
        $rankedAlerts = $this->rankAndFilterAlerts($matchedRules); // <-- NEW STEP

        return $this->createAlert($rankedAlerts);
    }

    public function analyzePlanning(string $componentName, string $finding)
    {
        $componentRules = $this->getRulesForComponent($componentName);
        $rules = $componentRules['planning'] ?? [];

        $matchedRules = $this->runAdpieAnalysis($finding, $rules);
        $rankedAlerts = $this->rankAndFilterAlerts($matchedRules); // <-- NEW STEP

        return $this->createAlert($rankedAlerts);
    }

    public function analyzeIntervention(string $componentName, string $finding)
    {
        $componentRules = $this->getRulesForComponent($componentName);
        $rules = $componentRules['intervention'] ?? [];

        $matchedRules = $this->runAdpieAnalysis($finding, $rules);
        $rankedAlerts = $this->rankAndFilterAlerts($matchedRules); // <-- NEW STEP

        return $this->createAlert($rankedAlerts);
    }

    public function analyzeEvaluation(string $componentName, string $finding)
    {
        $componentRules = $this->getRulesForComponent($componentName);
        $rules = $componentRules['evaluation'] ?? [];

        $matchedRules = $this->runAdpieAnalysis($finding, $rules);
        $rankedAlerts = $this->rankAndFilterAlerts($matchedRules); // <-- NEW STEP

        return $this->createAlert($rankedAlerts);
    }
}