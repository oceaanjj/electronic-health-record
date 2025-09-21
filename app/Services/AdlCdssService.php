<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class AdlCdssService
{

    const CRITICAL = 'critical';
    const WARNING = 'warning';
    const INFO = 'info';
    const NONE = 'none';

    private $rules;

    public function __construct()
    {
        $this->loadRules();
    }

    /**
     * Loads the CDSS rules from multiple external YAML files in a directory.
     *
     * @return void
     */
    private function loadRules()
    {
        $rulesDirectory = storage_path('app/private/adl_rules');

        if (!File::isDirectory($rulesDirectory)) {
            $this->rules = [];
            return;
        }

        $allRules = [];
        $files = File::files($rulesDirectory);

        foreach ($files as $file) {
            if (in_array($file->getExtension(), ['yaml', 'yml'])) {
                try {
                    $rules = Yaml::parseFile($file->getPathname());
                    if (is_array($rules)) {
                        $allRules = array_merge($allRules, $rules);
                    }
                } catch (\Exception $e) {
                    // Log the error for debugging
                    error_log("Failed to parse YAML file: " . $file->getPathname() . " - " . $e->getMessage());
                }
            }
        }

        $this->rules = $allRules;
    }

    /**
     * Analyze findings and return an array of alerts.
     *
     * @param array $findingsData
     * @return array
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
            $finding = $this->sanitizeString($findingsData[$key] ?? '');
            $ruleSet = $this->rules[$key] ?? [];

            $alert = $this->runAnalysis($finding, $ruleSet);

            if ($alert) {
                $alerts[$key] = $alert;
            }
        }

        return $alerts;
    }

    /**
     * Helper method to run analysis for a specific finding.
     *
     * @param string $finding
     * @param array $rules
     * @return array
     */
    private function runAnalysis($finding, $rules)
    {
        // The finding is already sanitized. If it's empty, we don't need to check rules.
        if (empty($finding)) {
            return null; // Return null if the input is empty
        }

        foreach ($rules as $rule) {
            if ($this->matchesKeywords($finding, $rule['keywords'])) {
                return [
                    'alert' => $rule['alert'],
                    'severity' => strtoupper($rule['severity'])
                ];
            }
        }
        return [
            'alert' => 'No significant findings.',
            'severity' => self::NONE
        ];
    }


    /**
     * Helper method for case-insensitive keyword matching.
     *
     * @param string $text
     * @param array $keywords
     * @return bool
     */
    private function matchesKeywords($text, $keywords)
    {
        $text = strtolower($text);
        foreach ($keywords as $keyword) {
            if (strpos($text, strtolower($keyword)) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Sanitizes a string by removing leading/trailing whitespace and newlines.
     *
     * @param string $text
     * @return string
     */
    private function sanitizeString($text)
    {
        return trim($text);
    }
}
