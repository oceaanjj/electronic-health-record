<?php

namespace App\Services;

class ActOfDailyLivingCdssService extends BaseCdssService
{
    public function __construct()
    {
        parent::__construct('adl_rules');
    }

    /**
     * Analyzes all the assessment fields from the act of daily living form data.
     */
    public function analyzeFindings($findingsData)
    {
        $alerts = [];
        $keysToAnalyze = [
            'mobility_assessment', 'hygiene_assessment', 'toileting_assessment',
            'feeding_assessment', 'hydration_assessment', 'sleep_pattern_assessment',
            'pain_level_assessment',
        ];

        foreach ($keysToAnalyze as $key) {
            $finding = $findingsData[$key] ?? '';
            $ruleSet = $this->rules[$key] ?? [];
            $result = $this->runAnalysis($finding, $ruleSet);

            if ($result) {
                // Translate the individual field result back to source language
                $alerts[$key] = [
                    'alert' => $this->translateFinalAlert($result['alert']),
                    'severity' => $result['severity']
                ];
            }
        }
        return $alerts;
    }

    public function analyzeSingleFinding($fieldName, $findingText)
    {
        $ruleSet = $this->rules[$fieldName] ?? [];
        $result = $this->runAnalysis($findingText, $ruleSet);
        if ($result) {
            return [
                'alert' => $this->translateFinalAlert($result['alert']),
                'severity' => $result['severity']
            ];
        }
        return ['alert' => $this->translateFinalAlert('No Findings'), 'severity' => self::NONE];
    }
}
