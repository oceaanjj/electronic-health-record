<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;
use App\Services\PhysicalExamCdssService;
use App\Services\LabValuesCdssService;
use App\Services\VitalCdssService;
use App\Services\IntakeAndOutputCdssService;
use App\Services\ActOfDailyLivingCdssService;
use App\Models\Patient; // Assuming Patient model is needed to get ageGroup for LabValues

class NursingDiagnosisCdssService
{
    private $physicalExamCdssService;
    private $labValuesCdssService;
    private $vitalCdssService;
    private $intakeAndOutputCdssService;
    private $actOfDailyLivingCdssService;
    private $adpieRules; // New property for ADPIE rules

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
        $this->loadAdpieRules(); // Load ADPIE rules on construction
    }

    /**
     * Loads and merges all YAML rule files for ADPIE steps.
     */
    private function loadAdpieRules()
    {
        $this->adpieRules = [];
        $rulesDirectory = storage_path('app/private/adpie/nursing-diagnosis/rules');

        if (!File::isDirectory($rulesDirectory)) {
            error_log("ADPIE rules directory not found at: " . $rulesDirectory);
            return;
        }

        $files = File::files($rulesDirectory);

        foreach ($files as $file) {
            if (in_array($file->getExtension(), ['yaml', 'yml'])) {
                try {
                    $parsedYaml = Yaml::parseFile($file->getPathname());
                    if (is_array($parsedYaml)) {
                        $this->adpieRules = array_merge($this->adpieRules, $parsedYaml);
                    }
                } catch (\Exception $e) {
                    error_log("Failed to parse ADPIE YAML file: " . $file->getPathname() . " - " . $e->getMessage());
                }
            }
        }
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
     *
     * @param string $finding The text input from the nurse.
     * @param array $rules The set of rules for that specific ADPIE step.
     * @return array An array of recommendations.
     */
    private function runAdpieAnalysis(string $finding, array $rules): array
    {
        $findingLower = strtolower(trim($finding));
        $recommendations = [];

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
                // If negate is true, the rule matches if keywords are NOT found
                $match = !$allKeywordsFound;
            } else {
                // Otherwise, the rule matches if keywords ARE found
                $match = $allKeywordsFound;
            }

            if ($match) {
                $recommendations[] = $rule['alert'];
            }
        }
        return $recommendations;
    }

    /**
     * Generates comprehensive nursing diagnosis rules based on component alerts and nurse input.
     *
     * @param string $componentName The name of the component (e.g., 'physical-exam').
     * @param array $componentData Data from the component (e.g., physical exam findings, lab values).
     * @param array $nurseInput ADPIE input from the nurse for the specific component.
     * @param Patient $patient The patient model instance.
     * @return array An array containing the generated alerts and the path to the saved rule file.
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
                // Lab values require specific parameter and age group
                $ageGroup = $patient->getAgeGroup(); // Assuming a method to get age group from patient
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
                // Handle unknown component or log an error
                break;
        }

        // 2. Combine with nurse's ADPIE input
        $combinedRules = [
            'component' => $componentName,
            'patient_id' => $patient->id,
            'component_alerts' => $allAlerts,
            'nurse_input' => $nurseInput, // This will contain diagnosis, planning, intervention, evaluation
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

    /**
     * Analyzes Step 1: Diagnosis
     */
    public function analyzeDiagnosis($finding)
    {
        $rules = $this->adpieRules['diagnosis'] ?? [];
        $recommendations = $this->runAdpieAnalysis($finding, $rules);
        return $this->createAlert($recommendations);
    }

    /**
     * Analyzes Step 2: Planning
     */
    public function analyzePlanning($finding)
    {
        $rules = $this->adpieRules['planning'] ?? [];
        $recommendations = $this->runAdpieAnalysis($finding, $rules);
        return $this->createAlert($recommendations);
    }

    /**
     * Analyzes Step 3: Intervention
     */
    public function analyzeIntervention($finding)
    {
        $rules = $this->adpieRules['intervention'] ?? [];
        $recommendations = $this->runAdpieAnalysis($finding, $rules);
        return $this->createAlert($recommendations);
    }

    /**
     * Analyzes Step 4: Evaluation
     */
    public function analyzeEvaluation($finding)
    {
        $rules = $this->adpieRules['evaluation'] ?? [];
        $recommendations = $this->runAdpieAnalysis($finding, $rules);
        return $this->createAlert($recommendations);
    }
}