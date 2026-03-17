<?php

namespace App\Services;

class PhysicalExamCdssService extends BaseCdssService
{
    public function __construct()
    {
        parent::__construct('physical_exam');
    }

    /**
     * Analyzes all the assessment fields from the physical exam form data.
     */
    public function analyzeFindings($findingsData)
    {
        $alerts = [];
        $keysToAnalyze = [
            'general_appearance', 'skin_condition', 'eye_condition', 'oral_condition',
            'cardiovascular', 'abdomen_condition', 'extremities', 'neurological',
        ];

        foreach ($keysToAnalyze as $key) {
            $finding = $findingsData[$key] ?? '';
            $ruleSet = $this->rules[$key] ?? [];
            $result = $this->runAnalysis($finding, $ruleSet);

            // Translate each individual field alert back to source language
            if ($result) {
                $alerts[$key . '_alert'] = $this->translateFinalAlert($result['alert']);
            } else {
                $alerts[$key . '_alert'] = $this->translateFinalAlert('No Findings');
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
