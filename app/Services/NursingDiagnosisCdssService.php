<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;
use App\Models\Patient;
use Exception;
use Illuminate\Support\Facades\Log;

class NursingDiagnosisCdssService extends BaseCdssService
{
    private $physicalExamCdssService;
    private $labValuesCdssService;
    private $vitalCdssService;
    private $intakeAndOutputCdssService;
    private $actOfDailyLivingCdssService;

    private static $adpieRulesCache = [];

    public function __construct(
        PhysicalExamCdssService $physicalExamCdssService,
        LabValuesCdssService $labValuesCdssService,
        VitalCdssService $vitalCdssService,
        IntakeAndOutputCdssService $intakeAndOutputCdssService,
        ActOfDailyLivingCdssService $actOfDailyLivingCdssService
    ) {
        parent::__construct(); // Initialize base translation/NLP engine
        $this->physicalExamCdssService = $physicalExamCdssService;
        $this->labValuesCdssService = $labValuesCdssService;
        $this->vitalCdssService = $vitalCdssService;
        $this->intakeAndOutputCdssService = $intakeAndOutputCdssService;
        $this->actOfDailyLivingCdssService = $actOfDailyLivingCdssService;
    }

    private function getRulesForComponent(string $componentName): array
    {
        if (isset(self::$adpieRulesCache[$componentName])) return self::$adpieRulesCache[$componentName];
        $rulesDirectory = storage_path('app/private/adpie/' . $componentName . '/rules');
        if (!File::isDirectory($rulesDirectory)) return [];

        $files = File::files($rulesDirectory);
        $mergedRules = [];
        foreach ($files as $file) {
            if (!in_array(pathinfo($file->getPathname(), PATHINFO_EXTENSION), ['yaml', 'yml'])) continue;
            try {
                $parsedYaml = Yaml::parseFile($file->getPathname());
                if (is_array($parsedYaml)) {
                    $stepName = $file->getFilenameWithoutExtension();
                    $mergedRules[$stepName] = $parsedYaml[$stepName] ?? $parsedYaml;
                }
            } catch (Exception $e) {
                Log::error("Failed to parse ADPIE YAML: " . $file->getPathname());
            }
        }
        self::$adpieRulesCache[$componentName] = $mergedRules;
        return $mergedRules;
    }

    /**
     * UPGRADED: Uses BaseCdssService's robust runAnalysis for ADPIE steps.
     */
    private function runAnalysisStep(string $componentName, string $stepName, string $finding)
    {
        $componentRules = $this->getRulesForComponent($componentName);
        $rules = $componentRules[$stepName] ?? [];
        if (empty($rules)) return null;

        // Use the strong analysis engine (Translation + Stemming + Scoring)
        $result = $this->runAnalysis($finding, $rules);

        if (!$result || $result['severity'] === self::NONE) return null;

        return (object) [
            'level' => strtolower($result['severity']),
            'message' => $result['alert'], // Already translated by runAnalysis
            'raw_message' => $result['alert']
        ];
    }

    public function generateNursingDiagnosisRules(string $componentName, array $componentData, array $nurseInput, Patient $patient)
    {
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
            case 'vital-signs':
                $result = $this->vitalCdssService->analyzeVitalsForAlerts($componentData);
                if ($result['severity'] !== self::NONE) {
                    $allAlerts[] = ['source' => 'Vital Signs', 'field' => 'summary', 'alert' => $result['alert']];
                }
                break;
            case 'intake-and-output':
                $result = $this->intakeAndOutputCdssService->analyzeIntakeOutput($componentData);
                if ($result['severity'] !== self::NONE) {
                    $allAlerts[] = ['source' => 'Intake and Output', 'field' => 'summary', 'alert' => $result['alert']];
                }
                break;
            case 'act-of-daily-living':
                $results = $this->actOfDailyLivingCdssService->analyzeFindings($componentData);
                foreach ($results as $key => $result) {
                    if ($result['severity'] !== self::NONE) {
                        $allAlerts[] = ['source' => 'Act of Daily Living', 'field' => $key, 'alert' => $result['alert']];
                    }
                }
                break;
            case 'lab-values':
                $ageGroup = $this->labValuesCdssService->getAgeGroup($patient);
                $results = $this->labValuesCdssService->runLabCdss((object) $componentData, $ageGroup);
                foreach ($results as $cat => $group) {
                    foreach ($group as $res) {
                        $allAlerts[] = ['source' => 'Lab Values', 'field' => $cat, 'alert' => $res['text']];
                    }
                }
                break;
        }

        if (empty($allAlerts)) {
            $allAlerts[] = ['source' => $componentName, 'field' => 'general', 'alert' => $this->translateFinalAlert('No Findings')];
        }

        return ['alerts' => $allAlerts, 'rule_file_path' => null];
    }

    public function analyzeDiagnosis($componentName, $finding) { return $this->runAnalysisStep($componentName, 'diagnosis', $finding); }
    public function analyzePlanning($componentName, $finding) { return $this->runAnalysisStep($componentName, 'planning', $finding); }
    public function analyzeIntervention($componentName, $finding) { return $this->runAnalysisStep($componentName, 'intervention', $finding); }
    public function analyzeEvaluation($componentName, $finding) { return $this->runAnalysisStep($componentName, 'evaluation', $finding); }
}
