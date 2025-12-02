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
use Exception;
use Illuminate_Support_Arr;
use Illuminate\Support\Facades\Log; // Use the Laravel Log facade

class NursingDiagnosisCdssService
{
    private $physicalExamCdssService;
    private $labValuesCdssService;
    private $vitalCdssService;
    private $intakeAndOutputCdssService;
    private $actOfDailyLivingCdssService;

    // Static cache for rules
    private static $adpieRulesCache = [];

    // Define severity scores for ranking
    private const SEVERITY_SCORES = [
        'critical' => 3,
        'warning' => 2,
        'info' => 1,
        'low' => 0,
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
    }

    /**
     * Retrieves and caches ADPIE rules for a component.
     */
    private function getRulesForComponent(string $componentName): array
    {
        if (isset(self::$adpieRulesCache[$componentName])) {
            return self::$adpieRulesCache[$componentName];
        }

        $rulesDirectory = storage_path('app/private/adpie/' . $componentName . '/rules');

        if (!File::isDirectory($rulesDirectory)) {
            Log::warning("ADPIE rules directory not found for component: " . $componentName);
            self::$adpieRulesCache[$componentName] = [];
            return [];
        }

        $files = File::files($rulesDirectory);
        $mergedRules = [];

        foreach ($files as $file) {
            if (!in_array(pathinfo($file->getPathname(), PATHINFO_EXTENSION), ['yaml', 'yml'])) {
                continue;
            }

            try {
                $parsedYaml = Yaml::parseFile($file->getPathname());
                if (is_array($parsedYaml) && !empty($parsedYaml)) {
                    $stepName = $file->getFilenameWithoutExtension();

                    // Check if the YAML file itself contains a top-level key (like 'diagnosis:')
                    if (isset($parsedYaml[$stepName]) && is_array($parsedYaml[$stepName])) {
                        // Use the content under that key
                        $mergedRules[$stepName] = $parsedYaml[$stepName];
                    } else if (is_array(reset($parsedYaml))) {
                        // If it's a simple list of rules (array of arrays)
                        $mergedRules[$stepName] = $parsedYaml;
                    } else {
                        // Handle other valid YAML structures if necessary, or log a warning
                        Log::warning("Unexpected YAML structure in file: " . $file->getPathname());
                    }
                }
            } catch (Exception $e) {
                Log::error("Failed to parse ADPIE YAML file: " . $file->getPathname(), ['error' => $e->getMessage()]);
            }
        }

        self::$adpieRulesCache[$componentName] = $mergedRules;
        return $mergedRules;
    }

    /**
     * Converts ranked alert strings into a structured alert object.
     */
    private function createAlert(array $recommendations, string $highestSeverity = 'recommendation')
    {
        if (empty($recommendations)) {
            return null;
        }

        $messageHtml = '<ul class="list-disc list-inside text-left">';
        foreach ($recommendations as $rec) {
            $messageHtml .= '<li>' . htmlspecialchars((string) $rec) . '</li>';
        }
        $messageHtml .= '</ul>';

        $plainTextMessage = implode('. ', $recommendations);

        return (object) [
            'level' => strtolower($highestSeverity),
            'message' => $messageHtml,
            'raw_message' => $plainTextMessage
        ];
    }

    /**
     * Finds all rules that match the finding.
     */
    private function runAdpieAnalysis(string $finding, array $rules): array
    {
        $findingLower = strtolower(trim($finding));
        $matchedRules = [];

        if (empty($rules)) {
            return [];
        }

        foreach ($rules as $rule) {
            // Robust validation of rule structure
            if (!is_array($rule) || !isset($rule['keywords']) || !is_array($rule['keywords'])) {
                Log::warning('Invalid rule structure encountered.', ['rule' => $rule]);
                continue;
            }

            $keywords = array_map('strtolower', array_map('trim', $rule['keywords']));
            $matchType = $rule['match_type'] ?? 'all';
            $negate = (bool) ($rule['negate'] ?? false);
            $match = false;

            if ($matchType === 'any') {
                $match = false;
                foreach ($keywords as $keyword) {
                    if (str_contains($findingLower, $keyword)) {
                        $match = true;
                        break;
                    }
                }
            } else { // 'all' (default)
                $match = true;
                foreach ($keywords as $keyword) {
                    if (!str_contains($findingLower, $keyword)) {
                        $match = false;
                        break;
                    }
                }
            }

            // Apply negation logic
            if ($negate) {
                $match = !$match;
            }

            if ($match) {
                // Attach a computed score for secondary ranking
                $rule['computed_specificity'] = count($keywords);
                $matchedRules[] = $rule;
            }
        }
        return $matchedRules;
    }

    /**
     * Scores, ranks, and filters the matched rules.
     */
    private function rankAndFilterAlerts(array $matchedRules, int $limit = 3): array
    {
        $highestSeverity = 'info';

        // 1. Calculate a Composite Score for each rule
        $scoredRules = array_map(function ($rule) use (&$highestSeverity) {

            // Ensure keys exist to prevent warnings
            $severityName = strtolower($rule['severity'] ?? 'low');
            $negate = (bool) ($rule['negate'] ?? false);
            $keywords = $rule['keywords'] ?? []; // Safety check

            // --- Scoring components ---
            $severityScore = self::SEVERITY_SCORES[$severityName] ?? 0;
            $specificityScore = (int) ($rule['computed_specificity'] ?? count($keywords));
            $deprioritizeScore = (int) ($rule['deprioritize_score'] ?? 0);
            $negatePenalty = $negate ? 0.5 : 0; // Small penalty for negated rules

            // High factor (100) ensures severity dominates ranking
            $finalScore = ($severityScore * 100)
                + $specificityScore
                - $deprioritizeScore
                - $negatePenalty;

            $rule['final_rank_score'] = $finalScore;

            // Track the highest severity
            if ($severityScore > (self::SEVERITY_SCORES[$highestSeverity] ?? 0)) {
                $highestSeverity = $severityName;
            }

            return $rule;
        }, $matchedRules);


        // 2. Sort by the calculated final score
        usort($scoredRules, function ($a, $b) {
            $scoreA = $a['final_rank_score'] ?? 0;
            $scoreB = $b['final_rank_score'] ?? 0;

            return $scoreB <=> $scoreA; // Sort descending
        });

        // 3. Get the top N rules
        $topRules = array_slice($scoredRules, 0, $limit);

        // 4. Return the alerts and severity
        return [
            'alerts' => array_column($topRules, 'alert'),
            'highest_severity' => $highestSeverity
        ];
    }

    /**
     * General purpose analysis for any ADPIE step.
     */
    private function runAnalysisStep(string $componentName, string $stepName, string $finding)
    {
        $componentRules = $this->getRulesForComponent($componentName);
        $rules = $componentRules[$stepName] ?? [];

        if (empty($rules)) {
            Log::info("No rules found for component/step: {$componentName}/{$stepName}");
            return null;
        }

        $matchedRules = $this->runAdpieAnalysis($finding, $rules);

        if (empty($matchedRules)) {
            return null;
        }

        $rankedData = $this->rankAndFilterAlerts($matchedRules);

        return $this->createAlert($rankedData['alerts'], $rankedData['highest_severity']);
    }


    // --- 7. REFACTORED PUBLIC METHODS ---

    public function generateNursingDiagnosisRules(string $componentName, array $componentData, array $nurseInput, Patient $patient)
    {
        // Existing logic for 'physical-exam' and other components remains here.
        // It relies on the *other* CDSS services (PhysicalExamCdssService, etc.)
        // which should ideally return structured alerts, not just strings,
        // so you can rank them globally. This is a potential future improvement.

        // For now, assume the original logic handles this:
        $allAlerts = [];
        switch ($componentName) {
            case 'physical-exam':
                // Assuming $this->physicalExamCdssService->analyzeFindings() returns
                // an array of findings/alerts that need to be processed/formatted here.
                $componentAlerts = $this->physicalExamCdssService->analyzeFindings($componentData);
                foreach ($componentAlerts as $key => $alert) {
                    if ($alert !== 'No Findings' && $alert !== null) {
                        $allAlerts[] = ['source' => 'Physical Exam', 'field' => $key, 'alert' => $alert];
                    }
                }
                break;
            case 'vital-signs':
                foreach ($componentData as $finding) {
                    $allAlerts[] = ['source' => 'Vital Signs', 'field' => 'summary', 'alert' => $finding];
                }
                break;
            case 'intake-and-output':
                $result = $this->intakeAndOutputCdssService->analyzeIntakeOutput($componentData);
                if ($result['severity'] !== IntakeAndOutputCdssService::NONE) {
                    $allAlerts[] = ['source' => 'Intake and Output', 'field' => 'summary', 'alert' => $result['alert']];
                }
                break;
            case 'act-of-daily-living':
                $results = $this->actOfDailyLivingCdssService->analyzeFindings($componentData);
                foreach ($results as $key => $result) {
                    if ($result['severity'] !== ActOfDailyLivingCdssService::NONE) {
                        $allAlerts[] = ['source' => 'Act of Daily Living', 'field' => $key, 'alert' => $result['alert']];
                    }
                }
                break;
            case 'lab-values':
                $ageGroup = $this->labValuesCdssService->getAgeGroup($patient);
                $results = $this->labValuesCdssService->runLabCdss((object) $componentData, $ageGroup);
                foreach ($results as $key => $result) {
                    if ($result[0]['severity'] !== LabValuesCdssService::NONE) {
                        $allAlerts[] = ['source' => 'Lab Values', 'field' => $key, 'alert' => $result[0]['text']];
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

    public function analyzeDiagnosis(string $componentName, string $finding)
    {
        return $this->runAnalysisStep($componentName, 'diagnosis', $finding);
    }

    public function analyzePlanning(string $componentName, string $finding)
    {
        return $this->runAnalysisStep($componentName, 'planning', $finding);
    }

    public function analyzeIntervention(string $componentName, string $finding)
    {
        return $this->runAnalysisStep($componentName, 'intervention', $finding);
    }

    public function analyzeEvaluation(string $componentName, string $finding)
    {
        return $this->runAnalysisStep($componentName, 'evaluation', $finding);
    }
}