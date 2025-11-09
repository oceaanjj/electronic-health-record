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

    /**
     * @var array This will act as a cache for loaded rules.
     * e.g., $adpieRules['physical-exam']['diagnosis']
     */
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

        // --- We no longer load rules on construction ---
        $this->adpieRules = [];
    }

    /**
     * Loads and caches rules for a specific component.
     */
    private function getRulesForComponent(string $componentName)
    {
        // 1. Check if rules are already loaded and cached
        if (isset($this->adpieRules[$componentName])) {
            return $this->adpieRules[$componentName];
        }

        // 2. Build the dynamic path based on the component name
        $rulesDirectory = storage_path('app/private/adpie/' . $componentName . '/rules');

        if (!File::isDirectory($rulesDirectory)) {
            error_log("ADPIE rules directory not found for component: " . $componentName);
            $this->adpieRules[$componentName] = []; // Cache empty array to prevent re-reads
            return [];
        }

        $files = File::files($rulesDirectory);
        $mergedRules = [];

        foreach ($files as $file) {
            if (in_array($file->getExtension(), ['yaml', 'yml'])) {
                try {
                    $parsedYaml = Yaml::parseFile($file->getPathname());
                    if (is_array($parsedYaml)) {
                        // We merge based on the file name (e.g., diagnosis.yaml, planning.yaml)
                        $stepName = $file->getFilenameWithoutExtension(); // "diagnosis", "planning", etc.
                        $mergedRules[$stepName] = $parsedYaml[$stepName] ?? $parsedYaml;
                    }
                } catch (\Exception $e) {
                    error_log("Failed to parse ADPIE YAML file: " . $file->getPathname() . " - " . $e->getMessage());
                }
            }
        }

        // 3. Cache the merged rules for this component and return them
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

        return (object) [
            'level' => 'recommendation', // Or 'warning', 'info'
            'message' => $messageHtml
        ];
    }

    /**
     * Runs analysis for a single ADPIE finding against its rules.
     */
    private function runAdpieAnalysis(string $finding, array $rules): array
    {
        $findingLower = strtolower(trim($finding));
        $recommendations = [];

        if (empty($rules)) {
            return [];
        }

        foreach ($rules as $rule) {
            $match = true;
            $allKeywordsFound = true;
            foreach ($rule['keywords'] as $keyword) {
                if (!str_contains($findingLower, strtolower($keyword))) {
                    $allKeywordsFound = false;
                    break;
                }
            }

            if (isset($rule['negate']) && $rule['negate'] === true) {
                $match = !$allKeywordsFound;
            } else {
                $match = $allKeywordsFound;
            }

            if ($match) {
                $recommendations[] = $rule['alert'];
            }
        }
        return $recommendations;
    }

    /**
     * Generates comprehensive nursing diagnosis rules (This is the 'on-submit' logic)
     * This function is already component-aware.
     */
    public function generateNursingDiagnosisRules(string $componentName, array $componentData, array $nurseInput, Patient $patient)
    {
        $allAlerts = [];

        // 1. Get alerts from the component's CDSS service
        switch ($componentName) {
            case 'physical-exam':
                $componentAlerts = $this->physicalExamCdssService->analyzeFindings($componentData);
                foreach ($componentAlerts as $key => $alert) {
                    if ($alert !== 'No Findings') {
                        $allAlerts[] = ['source' => 'Physical Exam', 'field' => $key, 'alert' => $alert];
                    }
                }
                break;
            case 'lab-values':
                $ageGroup = $patient->getAgeGroup(); // Assuming a method to get age group
                foreach ($componentData as $param => $value) {
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

        // 2. Combine with nurse's ADPIE input
        $combinedRules = [
            'component' => $componentName,
            'patient_id' => $patient->id,
            'component_alerts' => $allAlerts,
            'nurse_input' => $nurseInput,
            'generated_at' => now()->toDateTimeString(),
        ];

        // 3. Save the combined rules to a YAML file
        $directory = storage_path('app/private/adpie/' . $componentName);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        $filename = $directory . '/nursing_diagnosis_rules_' . $patient->id . '_' . now()->format('YmdHis') . '.yaml';
        File::put($filename, Yaml::dump($combinedRules, 4, 2));

        return [
            'alerts' => $allAlerts,
            'rule_file_path' => $filename
        ];
    }

    //
    // --- UPDATED REAL-TIME ANALYSIS METHODS ---
    //

    /**
     * Analyzes Step 1: Diagnosis
     */
    public function analyzeDiagnosis(string $componentName, string $finding)
    {
        $componentRules = $this->getRulesForComponent($componentName);
        $rules = $componentRules['diagnosis'] ?? [];
        $recommendations = $this->runAdpieAnalysis($finding, $rules);
        return $this->createAlert($recommendations);
    }

    /**
     * Analyzes Step 2: Planning
     */
    public function analyzePlanning(string $componentName, string $finding)
    {
        $componentRules = $this->getRulesForComponent($componentName);
        $rules = $componentRules['planning'] ?? [];
        $recommendations = $this->runAdpieAnalysis($finding, $rules);
        return $this->createAlert($recommendations);
    }

    /**
     * Analyzes Step 3: Intervention
     */
    public function analyzeIntervention(string $componentName, string $finding)
    {
        $componentRules = $this->getRulesForComponent($componentName);
        $rules = $componentRules['intervention'] ?? [];
        $recommendations = $this->runAdpieAnalysis($finding, $rules);
        return $this->createAlert($recommendations);
    }

    /**
     * Analyzes Step 4: Evaluation
     */
    public function analyzeEvaluation(string $componentName, string $finding)
    {
        $componentRules = $this->getRulesForComponent($componentName);
        $rules = $componentRules['evaluation'] ?? [];
        $recommendations = $this->runAdpieAnalysis($finding, $rules);
        return $this->createAlert($recommendations);
    }
}