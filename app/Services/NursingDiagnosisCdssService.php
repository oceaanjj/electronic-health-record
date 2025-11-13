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
use Illuminate\Support\Arr; // New helper for array operations

class NursingDiagnosisCdssService
{
    // --- 1. CORE IMPROVEMENTS: Dependency Management & Type Safety ---
    // Injecting a simple Rule Engine abstraction would be ideal,
    // but for now, let's focus on cleaner code within this class.

    private $physicalExamCdssService;
    private $labValuesCdssService;
    private $vitalCdssService;
    private $intakeAndOutputCdssService;
    private $actOfDailyLivingCdssService;

    // Use static property for rules cache to avoid recalculation if the service
    // is instantiated multiple times in a single request (though less common with Laravel's service container).
    private static $adpieRulesCache = [];

    // Define severity scores for ranking with explicit data types.
    private const SEVERITY_SCORES = [
        'critical' => 3,
        'warning' => 2,
        'info' => 1,
        'low' => 0, // Added a 'low' or default score
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
        // Initialization of static cache is not needed here.
    }

    // --- 2. IMPROVEMENT: Rule Loading (Caching, Error Handling) ---
    /**
     * Retrieves and caches ADPIE rules for a component.
     * @param string $componentName e.g., 'physical-exam'
     * @return array
     */
    private function getRulesForComponent(string $componentName): array
    {
        if (isset(self::$adpieRulesCache[$componentName])) {
            return self::$adpieRulesCache[$componentName];
        }

        $rulesDirectory = storage_path('app/private/adpie/' . $componentName . '/rules');

        if (!File::isDirectory($rulesDirectory)) {
            // Use Laravel's built-in logging instead of error_log
            \Log::warning("ADPIE rules directory not found for component: " . $componentName);
            self::$adpieRulesCache[$componentName] = [];
            return [];
        }

        $files = File::files($rulesDirectory);
        $mergedRules = [];

        foreach ($files as $file) {
            // Use `pathinfo` for robust extension checking
            if (!in_array(pathinfo($file->getPathname(), PATHINFO_EXTENSION), ['yaml', 'yml'])) {
                continue;
            }

            try {
                $parsedYaml = Yaml::parseFile($file->getPathname());
                if (is_array($parsedYaml) && !empty($parsedYaml)) {
                    $stepName = $file->getFilenameWithoutExtension();

                    // Use Laravel Arr::wrap for consistent handling of lists vs keyed arrays
                    $rulesList = Arr::wrap($parsedYaml);

                    // Simple check to see if the first element is an array (list of rules)
                    if (is_array(reset($rulesList)) && !is_numeric(key(reset($rulesList)))) {
                        // If it's a top-level key matching the filename, use its content
                        $mergedRules[$stepName] = $rulesList[$stepName] ?? $rulesList;
                    } else {
                        // Otherwise, assume the entire file content is the rule set for this step
                        $mergedRules[$stepName] = $rulesList;
                    }
                }
            } catch (Exception $e) {
                \Log::error("Failed to parse ADPIE YAML file: " . $file->getPathname(), ['error' => $e->getMessage()]);
            }
        }

        self::$adpieRulesCache[$componentName] = $mergedRules;
        return $mergedRules;
    }

    // --- 3. IMPROVEMENT: Alert Generation (Clearer Separation) ---
    /**
     * Converts ranked alert strings into a structured alert object for the front-end.
     * @param array $recommendations Array of alert strings.
     * @param string $highestSeverity Highest severity found in the ranked list (e.g., 'critical', 'warning')
     * @return object|null
     */
    private function createAlert(array $recommendations, string $highestSeverity = 'recommendation')
    {
        if (empty($recommendations)) {
            return null;
        }

        $messageHtml = '<ul class="list-disc list-inside text-left">';
        foreach ($recommendations as $rec) {
            // Ensure the recommendation is a string before escaping
            $messageHtml .= '<li>' . htmlspecialchars((string) $rec) . '</li>';
        }
        $messageHtml .= '</ul>';

        // Join recommendations with a period for clearer raw message separation
        $plainTextMessage = implode('. ', $recommendations);

        return (object) [
            // Use the highest severity for a more accurate visual cue
            'level' => strtolower($highestSeverity),
            'message' => $messageHtml,
            'raw_message' => $plainTextMessage
        ];
    }

    // --- 4. IMPROVEMENT: Rule Matching (Performance & Clarity) ---
    /**
     * Finds all rules that match the finding.
     * @param string $finding The input text to analyze.
     * @param array $rules The rule set for a specific ADPIE step.
     * @return array
     */
    private function runAdpieAnalysis(string $finding, array $rules): array
    {
        // One-time lowercasing of the finding for efficiency
        $findingLower = strtolower(trim($finding));
        $matchedRules = [];

        if (empty($rules)) {
            return [];
        }

        foreach ($rules as $rule) {
            // Robust validation of rule structure
            if (!is_array($rule) || !isset($rule['keywords']) || !is_array($rule['keywords'])) {
                \Log::warning('Invalid rule structure encountered.', ['rule' => $rule]);
                continue;
            }

            // Pre-process keywords: trim and lowercase them once
            $keywords = array_map('strtolower', array_map('trim', $rule['keywords']));

            $matchType = $rule['match_type'] ?? 'all';
            $negate = $rule['negate'] ?? false;
            $match = false;

            // Using a simple, consistent logic: $match is set to true if the conditions pass,
            // and the final $negate check handles inversion.

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
                    // Fail fast: if any keyword is missing, the 'all' match fails.
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
                // Attach a computed score (based on keywords) for secondary ranking stability
                $rule['computed_specificity'] = count($keywords);
                $matchedRules[] = $rule;
            }
        }
        return $matchedRules;
    }

    // --- 5. IMPROVEMENT: Ranking and Filtering (Robustness & Scoring) ---
    /**
     * Scores, ranks, and filters the matched rules.
     * @param array $matchedRules Array of matching rule objects.
     * @param int $limit Max number of alerts to return.
     * @return array Array containing the top N alert strings and the highest severity.
     */
    private function rankAndFilterAlerts(array $matchedRules, int $limit = 3): array
    {
        $highestSeverity = 'info';

        usort($matchedRules, function (array $a, array $b) use (&$highestSeverity) {
            // 1. Prioritize by Severity (Critical > Warning > Info > Low)
            $severityA = self::SEVERITY_SCORES[strtolower($a['severity'] ?? 'low')] ?? 0;
            $severityB = self::SEVERITY_SCORES[strtolower($b['severity'] ?? 'low')] ?? 0;

            // Track the highest severity
            $currentHighest = max($severityA, $severityB);
            $severityName = array_search($currentHighest, self::SEVERITY_SCORES);
            if ($severityName && self::SEVERITY_SCORES[$highestSeverity] < $currentHighest) {
                $highestSeverity = $severityName;
            }

            if ($severityA !== $severityB) {
                return $severityB <=> $severityA; // Sort descending by severity
            }

            // 2. Prioritize by Specificity (More keywords/computed_specificity is better)
            // Use the computed specificity for consistency
            $specificityA = $a['computed_specificity'] ?? count($a['keywords'] ?? []);
            $specificityB = $b['computed_specificity'] ?? count($b['keywords'] ?? []);

            if ($specificityA !== $specificityB) {
                return $specificityB <=> $specificityA; // Sort descending by specificity
            }

            // 3. De-prioritize 'negate' rules (they are less specific and should be weighted lower)
            $negateA = (bool) ($a['negate'] ?? false);
            $negateB = (bool) ($b['negate'] ?? false);

            return $negateA <=> $negateB; // Sort ascending (false (0) comes before true (1))
        });

        // Get the top N rules
        $topRules = array_slice($matchedRules, 0, $limit);

        // Return just the alert strings and the highest severity
        return [
            'alerts' => array_column($topRules, 'alert'),
            'highest_severity' => $highestSeverity
        ];
    }

    // --- 6. IMPROVEMENT: Centralized Analysis Method for ADPIE Steps ---
    /**
     * General purpose analysis for any ADPIE step.
     * @param string $componentName
     * @param string $stepName 'diagnosis', 'planning', 'intervention', 'evaluation'
     * @param string $finding The input text to analyze.
     * @return object|null The structured alert object or null.
     */
    private function runAnalysisStep(string $componentName, string $stepName, string $finding)
    {
        $componentRules = $this->getRulesForComponent($componentName);
        $rules = $componentRules[$stepName] ?? [];

        $matchedRules = $this->runAdpieAnalysis($finding, $rules);

        // Return null immediately if no matches
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