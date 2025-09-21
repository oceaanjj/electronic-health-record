<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class CdssService
{
    const CRITICAL = 'critical';
    const WARNING = 'warning';
    const INFO = 'info';
    const NONE = 'none';

    private $rules = [];
    private $rulesDirectoryName;

    /**
     * @param string $rulesDirectoryName The name of the subdirectory within storage/app/private
     * where the YAML rules files are located.
     */
    public function __construct(string $rulesDirectoryName)
    {
        $this->rulesDirectoryName = $rulesDirectoryName;
        $this->loadRules();
    }

    // Loads the CDSS rules from multiple external YAML files in a specific directory.
    private function loadRules()
    {
        $rulesDirectory = storage_path('app/private/' . $this->rulesDirectoryName);

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
     * Analyzes a set of patient findings against the loaded rules.
     * The method is now dynamic and checks every key in the provided data.
     *
     * @param array $findingsData An associative array of patient findings (e.g., ['mobility_assessment' => '...']).
     * @return array An associative array of alerts, keyed by the finding name.
     */
    public function analyzeFindings(array $findingsData)
    {
        $alerts = [];
        foreach ($findingsData as $key => $finding) {
            $sanitizedFinding = $this->sanitizeString($finding ?? '');
            $ruleSet = $this->rules[$key] ?? [];

            $alert = $this->runAnalysis($sanitizedFinding, $ruleSet);

            if ($alert) {
                $alerts[$key] = $alert;
            }
        }

        return $alerts;
    }

    /**
     * Helper method to run analysis for a specific finding.
     *
     * @param string $finding The patient finding to analyze.
     * @param array $rules The ruleset for the specific finding.
     * @return array|null An alert array with 'alert' and 'severity' keys, or null if no rule matches.
     */
    private function runAnalysis($finding, $rules)
    {
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
     * @param string $text The text to check for keywords.
     * @param array $keywords An array of keywords to search for.
     * @return bool True if a keyword is found, false otherwise.
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
     * @param string $text The text to sanitize.
     * @return string The sanitized string.
     */
    private function sanitizeString($text)
    {
        return trim($text);
    }
}
