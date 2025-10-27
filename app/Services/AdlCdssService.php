<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class AdlCdssService
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

    // Loads and correctly merges all YAML rule files from the private directory.
    private function loadRules()
    {
        $this->rules = [];
        $rulesDirectory = storage_path('app/private/adl_rules');

        if (!File::isDirectory($rulesDirectory)) {
            return;
        }

        $allRules = [];
        $files = File::files($rulesDirectory);

        foreach ($files as $file) {
            if (in_array($file->getExtension(), ['yaml', 'yml'])) {
                try {
                    $parsedYaml = Yaml::parseFile($file->getPathname());
                    if (is_array($parsedYaml)) {
                        // This loop correctly merges rule categories from different files.
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

    // Analyzes all the assessment fields from the form data.
    public function analyzeFindings($findingsData)
    {
        $alerts = [];
        $keysToAnalyze = [
            'mobility_assessment',
            'hygiene_assessment',
            'toileting_assessment',
            'feeding_assessment',
            'hydration_assessment',
            'sleep_pattern_assessment',
            'pain_level_assessment',
        ];

        foreach ($keysToAnalyze as $key) {
            $finding = $findingsData[$key] ?? '';
            $ruleSet = $this->rules[$key] ?? [];
            $alert = $this->runAnalysis($finding, $ruleSet);
            if ($alert) {
                $alerts[$key] = $alert;
            }
        }
        return $alerts;
    }

    // Turns a string into an array of clean, unique words.
    private function sanitizeAndSplit($text)
    {
        $lowerText = strtolower($text);
        $noPunctuation = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $lowerText);
        // array_filter removes any empty strings that might result from splitting.
        return array_unique(array_filter(preg_split('/\s+/', trim($noPunctuation))));
    }

    // Analyzes the nurse's input to find the best matching alert.
    private function runAnalysis($finding, $rules)
    {
        if (empty(trim($finding))) {
            return null;
        }

        // 1. Sanitize and Tokenize the nurse's input string into an array of words.
        $findingWords = $this->sanitizeAndSplit($finding);
        $matchedRules = [];

        // Loop through every rule for this category.
        foreach ($rules as $rule) {
            $currentRuleScore = 0;
            if (!isset($rule['keywords']) || !is_array($rule['keywords'])) {
                continue;
            }

            // 2. Word-Set Matching and Scoring
            foreach ($rule['keywords'] as $keywordPhrase) {
                // Sanitize and Tokenize the keyword phrase from the YAML file.
                $keywordWords = $this->sanitizeAndSplit($keywordPhrase);

                if (empty($keywordWords))
                    continue;

                // Check if all words from the keyword phrase are present in the input.
                $isMatch = empty(array_diff($keywordWords, $findingWords));

                if ($isMatch) {
                    // Calculate a score. More specific keywords (more words) get a higher score.
                    $phraseScore = count($keywordWords) * 10;
                    foreach ($keywordWords as $word) {
                        $phraseScore += strlen($word);
                    }
                    $currentRuleScore += $phraseScore;
                }
            }

            // If the rule matched at least one keyword, save it for scoring.
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
            return ['alert' => 'Normal Findings', 'severity' => self::NONE];
        }

        // 3. Severity-First Prioritization
        // Sort the list of all matched rules to find the single best one.
        usort($matchedRules, function ($a, $b) {
            // First, sort by severity (highest number = higher priority).
            if ($a['severity'] !== $b['severity']) {
                return $b['severity'] <=> $a['severity'];
            }
            // If severity is the same, sort by score (higher score = better match).
            return $b['score'] <=> $a['score'];
        });

        // The best match is now the first item in the sorted list.
        $bestMatch = $matchedRules[0];
        return [
            'alert' => $bestMatch['alert'],
            'severity' => $bestMatch['severity_str']
        ];
    }

    // Assigns a number to each severity level for sorting.
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