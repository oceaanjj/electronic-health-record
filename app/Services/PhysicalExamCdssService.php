<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class PhysicalExamCdssService
{
    const CRITICAL = 'CRITICAL';
    const WARNING = 'WARNING';
    const INFO = 'INFO';
    const NONE = 'NONE';

    private $rules;

    public function __construct()
    {
        $this->loadRules();
    }

    /**
     * Loads and correctly merges all YAML rule files from the physical_exam directory.
     */
    private function loadRules()
    {
        $this->rules = [];
        $rulesDirectory = storage_path('app/private/physical_exam'); // Path to your directory

        if (!File::isDirectory($rulesDirectory)) {
            error_log("CDSS rules directory not found at: " . $rulesDirectory);
            return;
        }

        $allRules = [];
        $files = File::files($rulesDirectory);

        foreach ($files as $file) {
            // Process only files with .yaml or .yml extensions
            if (in_array($file->getExtension(), ['yaml', 'yml'])) {
                try {
                    $parsedYaml = Yaml::parseFile($file->getPathname());
                    if (is_array($parsedYaml)) {
                        // This loop correctly merges rule categories from different files.
                        // For example, if two files define rules for 'skin_condition',
                        // they will be merged into a single list.
                        foreach ($parsedYaml as $key => $value) {
                            if (!isset($allRules[$key])) {
                                $allRules[$key] = [];
                            }
                            $allRules[$key] = array_merge($allRules[$key], $value);
                        }
                    }
                } catch (\Exception $e) {
                    error_log("Failed to parse YAML file: " . $file->getPathname() . " - " . $e->getMessage());
                }
            }
        }
        $this->rules = $allRules;
    }


    /**
     * Analyzes all the assessment fields from the physical exam form data.
     *
     * @param array $findingsData The request data from the form.
     * @return array The generated alerts.
     */
    public function analyzeFindings($findingsData)
    {
        $alerts = [];
        // These keys MUST match the 'name' attributes of the textareas in your form
        // and the top-level keys in your YAML files.
        $keysToAnalyze = [
            'general_appearance',
            'skin_condition',
            'eye_condition',
            'oral_condition',
            'cardiovascular',
            'abdomen_condition',
            'extremities',
            'neurological',
        ];

        foreach ($keysToAnalyze as $key) {
            $finding = $findingsData[$key] ?? '';
            $ruleSet = $this->rules[$key] ?? [];
            $alert = $this->runAnalysis($finding, $ruleSet);

            if ($alert) {
                $alerts[$key . '_alert'] = $alert['alert'];
                $alerts[$key . '_condition_alert'] = $alert['alert'];
            } else {
                $alerts[$key . '_alert'] = 'No Findings';
            }
            $alerts[$key] = $alert['alert'];
        }
        return $alerts;
    }

    public function analyzeSingleFinding($fieldName, $findingText)
    {
        // Find the rules for the specific field.
        $ruleSet = $this->rules[$fieldName] ?? [];

        // Run the existing analysis logic on the single piece of data.
        return $this->runAnalysis($findingText, $ruleSet);
    }


    /**
     * Turns a string into an array of clean, unique words for matching.
     *
     * @param string $text The input string.
     * @return array An array of sanitized words.
     */
    private function sanitizeAndSplit($text)
    {
        $lowerText = strtolower($text);
        // Removes punctuation but keeps letters, numbers, spaces, and hyphens.
        $noPunctuation = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $lowerText);
        // Splits the string by spaces and removes any empty array elements.
        return array_unique(array_filter(preg_split('/\s+/', trim($noPunctuation))));
    }

    /**
     * Analyzes a single finding against a set of rules to find the best matching alert.
     *
     * @param string $finding The text input from a single textarea.
     * @param array $rules The set of rules for that specific category.
     * @return array|null The best matching alert or null if input is empty.
     */
    private function runAnalysis($finding, $rules)
    {
        // Don't process empty or whitespace-only fields.
        if (empty(trim($finding))) {
            return null;
        }

        $findingWords = $this->sanitizeAndSplit($finding);
        $matchedRules = [];

        foreach ((array) $rules as $rule) {
            $currentRuleScore = 0;
            if (!isset($rule['keywords']) || !is_array($rule['keywords'])) {
                continue; // Skip malformed rules.
            }

            // --- Word-Set Matching and Scoring ---
            foreach ($rule['keywords'] as $keywordPhrase) {
                $keywordWords = $this->sanitizeAndSplit($keywordPhrase);
                if (empty($keywordWords)) {
                    continue;
                }

                // Check if all words from the keyword phrase are present in the user's input.
                $isMatch = empty(array_diff($keywordWords, $findingWords));

                if ($isMatch) {
                    // A score is calculated to prioritize more specific keywords.
                    // A longer phrase (more words) gets a higher base score.
                    $phraseScore = count($keywordWords) * 10;
                    // The length of each word adds to the score for further specificity.
                    foreach ($keywordWords as $word) {
                        $phraseScore += strlen($word);
                    }
                    $currentRuleScore += $phraseScore;
                }
            }

            // If the rule's keywords generated a score, add it to our list of potential matches.
            if ($currentRuleScore > 0) {
                $matchedRules[] = [
                    'score' => $currentRuleScore,
                    'severity' => $this->getSeverityValue($rule['severity']),
                    'alert' => $rule['alert'],
                    'severity_str' => strtoupper($rule['severity'])
                ];
            }
        }

        // If no rules matched at all, return a default "Normal Findings" alert.
        if (empty($matchedRules)) {
            return ['alert' => 'No Findings', 'severity' => self::NONE];
        }

        // --- Severity-First Prioritization ---
        // Sort the list of all matched rules to find the single best one.
        usort($matchedRules, function ($a, $b) {
            // First, sort by severity (highest number = higher priority).
            if ($a['severity'] !== $b['severity']) {
                return $b['severity'] <=> $a['severity'];
            }
            // If severity is the same, sort by score (higher score = better, more specific match).
            return $b['score'] <=> $a['score'];
        });

        // The best match is now the first item in the sorted list.
        $bestMatch = $matchedRules[0];
        return [
            'alert' => $bestMatch['alert'],
            'severity' => $bestMatch['severity_str']
        ];
    }

    /**
     * Assigns a numeric value to each severity level for sorting purposes.
     *
     * @param string $severityStr The severity string (e.g., 'critical').
     * @return int The corresponding numeric value.
     */
    private function getSeverityValue($severityStr)
    {
        switch (strtolower($severityStr)) {
            case 'critical':
                return 4;
            case 'warning':
                return 3;
            case 'info':
                return 2;
            case 'none':
                return 1;
            default:
                return 0;
        }
    }
}