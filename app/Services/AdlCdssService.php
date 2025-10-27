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

    // Load rules logic remains mostly the same, ensuring correct merging.
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

    // The main analysis function remains the entry point.
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


    //---------------------
    public function analyzeSingleFinding($fieldName, $findingText)
    {
        //  Defensive check in case the controller fix failed or data is corrupted.
        if (!is_string($findingText)) {
            $findingText = '';
        }
        // Find the rules for the specific field.
        $ruleSet = $this->rules[$fieldName] ?? [];
        // Run the existing analysis logic on the single piece of data.
        return $this->runAnalysis($findingText, $ruleSet);
    }
    //---------------------

    private function tokenize($text)
    {
        // Use a more inclusive regex for word boundaries, but keep the core logic.
        $lowerText = strtolower($text);
        // Retain hyphens as a single token, e.g., 'g-tube'
        $noPunctuation = preg_replace('/[^\p{L}\p{N}\s-]/u', ' ', $lowerText);
        // Split by any whitespace and filter out empty tokens.
        return array_unique(array_filter(preg_split('/\s+/', trim($noPunctuation))));

        // 💡 REAL IMPROVEMENT: Integrate a PHP stemming library here
    }

    /**
     * Checks if a phrase exists as a whole in the original, unsanitized input.
     * This is crucial for high-confidence matches like "turning blue".
     */
    private function checkExactPhraseMatch($phrase, $finding)
    {
        // Normalize for case and multiple spaces
        $normalizedFinding = preg_replace('/\s+/', ' ', strtolower(trim($finding)));
        $normalizedPhrase = preg_replace('/\s+/', ' ', strtolower(trim($phrase)));

        // Check for the exact phrase presence
        return strpos($normalizedFinding, $normalizedPhrase) !== false;
    }

    /**
     * Main analysis logic with refined scoring and negation check.
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

            // Skip if rule structure is incomplete
            if (!isset($rule['keywords']) || !is_array($rule['keywords'])) {
                continue;
            }

            // --- 1. Scoring & Matching ---
            foreach ($rule['keywords'] as $keywordPhrase) {
                // Option 1: High-Confidence Exact Phrase Match
                if ($this->checkExactPhraseMatch($keywordPhrase, $finding)) {
                    // Assign a high, guaranteed match score for exact phrase.
                    // This is more specific than word-set matching.
                    $phraseScore = 100 + (count(explode(' ', $keywordPhrase)) * 5); // Base 100 + length bonus
                    $ruleScore += $phraseScore;

                } else {
                    // Option 2: Word-Set Matching (as in original)
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

            // --- 2. Negation Check (CRITICAL Improvement) ---
            // If the rule has a score, check for potential negation.
            if ($ruleScore > 0) {
                // Simplistic negation: check if common negation words are present.
                // A better approach would be to check if the negation word is near the matched word.
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
                    // Reduce the score drastically for a potential negated finding.
                    // NEGATED CRITICAL AND WARNING alerts.
                    if ($ruleSeverityStr === self::SEVERITY['CRITICAL']['str'] || $ruleSeverityStr === self::SEVERITY['WARNING']['str']) {
                        $ruleScore = 0; // Completely ignore CRITICAL if negated.
                    } else {
                        $ruleScore *= 0.1; // Greatly reduce score for WARNING/INFO.
                    }
                }
            }


            // If the rule matched with a significant score (can be reduced by negation), save it.
            if ($ruleScore > 0) {
                $matchedRules[] = [
                    'score' => $ruleScore,
                    'severity' => $this->getSeverityValue($ruleSeverityStr),
                    'alert' => $rule['alert'],
                    'severity_str' => $ruleSeverityStr
                ];
            }
        }

        // If no rules matched, check for the explicit "Normal" finding.
        if (empty($matchedRules)) {
            // You can add a default "Normal" check here, or rely on the final rule in YAML.
            if ($this->checkExactPhraseMatch('normal', $finding) || $this->checkExactPhraseMatch('no concerns', $finding)) {
                return ['alert' => 'Normal Findings / No Concerns', 'severity' => self::SEVERITY['NONE']['str']];
            }
            // If still no match and not an explicit "normal", return a generic 'None'.
            return ['alert' => 'No Clinical Alert Detected', 'severity' => self::SEVERITY['NONE']['str']];
        }

        // --- 3. Severity-First Prioritization (Logic is good, kept) ---
        usort($matchedRules, function ($a, $b) {
            // First, sort by severity (highest number = higher priority).
            if ($a['severity'] !== $b['severity']) {
                return $b['severity'] <=> $a['severity'];
            }
            // If severity is the same, sort by score (higher score = better match).
            return $b['score'] <=> $a['score'];
        });

        $bestMatch = $matchedRules[0];
        return [
            'alert' => $bestMatch['alert'],
            'severity' => $bestMatch['severity_str']
        ];
    }

    // Utility function for severity value is now based on the constant array.
    private function getSeverityValue($severityStr)
    {
        return self::SEVERITY[strtoupper($severityStr)]['value'] ?? 0;
    }
}