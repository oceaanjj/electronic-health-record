<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class AdlCdssService
{
    // New class to encapsulate severity values for cleaner logic
    public const SEVERITY = [
        'CRITICAL' => ['value' => 4, 'str' => 'CRITICAL'],
        'WARNING' => ['value' => 3, 'str' => 'WARNING'],
        'INFO' => ['value' => 2, 'str' => 'INFO'],
        'NONE' => ['value' => 1, 'str' => 'NONE'],
    ];

    private $rules;

    public function __construct()
    {
        $this->loadRules();
    }

    /**
     * Loads and correctly merges all YAML rule files from the private directory.
     * (Existing logic retained for rule loading)
     */
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
                        foreach ($parsedYaml as $key => $value) {
                            if (!isset($allRules[$key])) {
                                $allRules[$key] = [];
                            }
                            // array_merge works fine here for sequential arrays of rules
                            $allRules[$key] = array_merge($allRules[$key], $value);
                        }
                    }
                } catch (\Exception $e) {
                    // Log the error without throwing an exception that crashes the application
                    error_log("Failed to parse YAML file: " . $file->getPathname() . " - " . $e->getMessage());
                }
            }
        }
        $this->rules = $allRules;
    }

    /**
     * Analyzes all the assessment fields from the form data (used on form submission).
     */
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
            // Ensure finding is a string for analysis
            $finding = (string) ($findingsData[$key] ?? '');
            $ruleSet = $this->rules[$key] ?? [];
            $alert = $this->runAnalysis($finding, $ruleSet);
            if ($alert) {
                $alerts[$key] = $alert;
            }
        }
        return $alerts;
    }

    /**
     * Analyzes a single finding (used for real-time AJAX requests).
     */
    public function analyzeSingleFinding($fieldName, $findingText)
    {
        // Defensive check: force input to string if it somehow wasn't (e.g., null)
        if (!is_string($findingText)) {
            $findingText = '';
        }

        $ruleSet = $this->rules[$fieldName] ?? [];
        return $this->runAnalysis($findingText, $ruleSet);
    }

    //---------------------
    // CORE ALGORITHM METHODS
    //---------------------

    /**
     * Converts a string into an array of clean, unique words (tokens).
     */
    private function tokenize($text)
    {
        $lowerText = strtolower($text);
        // Retain hyphens as a single token, e.g., 'g-tube'
        $noPunctuation = preg_replace('/[^\p{L}\p{N}\s-]/u', ' ', $lowerText);
        // Split by any whitespace and filter out empty tokens.
        return array_unique(array_filter(preg_split('/\s+/', trim($noPunctuation))));
    }

    /**
     * Checks if a phrase exists as a whole in the original, unsanitized input.
     * Used for high-confidence exact matches.
     */
    private function checkExactPhraseMatch($phrase, $finding)
    {
        $normalizedFinding = preg_replace('/\s+/', ' ', strtolower(trim($finding)));
        $normalizedPhrase = preg_replace('/\s+/', ' ', strtolower(trim($phrase)));

        // Check for the exact phrase presence
        return strpos($normalizedFinding, $normalizedPhrase) !== false;
    }

    /**
     * Analyzes the nurse's input against the rule set to find the best matching alert.
     */
    private function runAnalysis($finding, $rules)
    {
        if (empty(trim($finding))) {
            return ['alert' => 'No Finding Entered', 'severity' => self::SEVERITY['NONE']['str']];
        }

        $findingWords = $this->tokenize($finding);
        $matchedRules = [];

        foreach ($rules as $rule) {
            $ruleScore = 0;
            $ruleSeverityStr = strtoupper($rule['severity'] ?? 'INFO');

            if (!isset($rule['keywords']) || !is_array($rule['keywords'])) {
                continue;
            }

            // --- 1. Scoring & Matching ---
            foreach ($rule['keywords'] as $keywordPhrase) {
                // Option 1: High-Confidence Exact Phrase Match (via strpos)
                if ($this->checkExactPhraseMatch($keywordPhrase, $finding)) {
                    // Assign a high, guaranteed match score for exact phrase.
                    $phraseScore = 100 + (count(explode(' ', $keywordPhrase)) * 5); // Base 100 + length bonus
                    $ruleScore += $phraseScore;

                } else {
                    // Option 2: Word-Set Matching (via token comparison)
                    $keywordWords = $this->tokenize($keywordPhrase);
                    if (empty($keywordWords))
                        continue;

                    // Check if ALL words from the keyword are present in the finding's tokenized words
                    $isMatch = empty(array_diff($keywordWords, $findingWords));

                    if ($isMatch) {
                        // Keep the scoring logic but give a smaller bonus than exact phrase match
                        $phraseScore = count($keywordWords) * 10;
                        foreach ($keywordWords as $word) {
                            $phraseScore += strlen($word);
                        }
                        $ruleScore += $phraseScore;
                    }
                }
            }

            // --- 2. Negation Check ---
            // If the rule has a score, check for potential negation.
            if ($ruleScore > 0) {
                $negationWords = ['no', 'denies', 'without', 'not'];
                $isNegated = false;

                foreach ($rule['keywords'] as $keywordPhrase) {
                    foreach ($negationWords as $negation) {
                        // Check for simple patterns like "no choking" or "denies choking"
                        if (strpos(strtolower($finding), $negation . ' ' . strtolower($keywordPhrase)) !== false) {
                            $isNegated = true;
                            break 2; // Exit both inner loops
                        }
                    }
                }

                if ($isNegated) {
                    // Setting score to 0 completely disables the rule if negated, 
                    // regardless of severity (the cleanest approach).
                    $ruleScore = 0;
                }
            }


            // If the rule matched with a significant score, save it.
            if ($ruleScore > 0) {
                $matchedRules[] = [
                    'score' => $ruleScore,
                    'severity' => $this->getSeverityValue($ruleSeverityStr),
                    'alert' => $rule['alert'],
                    'severity_str' => $ruleSeverityStr
                ];
            }
        }

        // --- 3. Final Result Selection ---

        // If no rules matched, check for the explicit "Normal" finding.
        if (empty($matchedRules)) {
            if ($this->checkExactPhraseMatch('normal', $finding) || $this->checkExactPhraseMatch('no concerns', $finding)) {
                return ['alert' => 'Normal Findings / No Concerns', 'severity' => self::SEVERITY['NONE']['str']];
            }
            // If still no match and not an explicit "normal", return a generic 'None'.
            return ['alert' => 'No Clinical Alert Detected', 'severity' => self::SEVERITY['NONE']['str']];
        }

        // Severity-First Prioritization
        usort($matchedRules, function ($a, $b) {
            // 1. Sort by severity (highest value first).
            if ($a['severity'] !== $b['severity']) {
                return $b['severity'] <=> $a['severity'];
            }
            // 2. If severity is the same, sort by score (highest score first).
            return $b['score'] <=> $a['score'];
        });

        $bestMatch = $matchedRules[0];
        return [
            'alert' => $bestMatch['alert'],
            'severity' => $bestMatch['severity_str']
        ];
    }

    /**
     * Assigns a number to each severity level for sorting.
     */
    private function getSeverityValue($severityStr)
    {
        return self::SEVERITY[strtoupper($severityStr)]['value'] ?? 0;
    }
}