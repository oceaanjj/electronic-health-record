<?php

namespace App\Services;

class CdssService extends BaseCdssService
{
    /**
     * @param string $rulesDirectoryName The name of the subdirectory within storage/app/private
     * where the YAML rules files are located.
     */
    public function __construct(string $rulesDirectoryName)
    {
        parent::__construct($rulesDirectoryName);
        $this->shouldTranslate = false;
    }

    /**
     * Analyzes a set of patient findings against the loaded rules.
     * Dynamic and checks every key in the provided data.
     */
    public function analyzeFindings(array $findingsData)
    {
        $alerts = [];
        foreach ($findingsData as $key => $finding) {
            $ruleSet = $this->rules[$key] ?? [];
            $alert = $this->runAnalysis($finding ?? '', $ruleSet);

            if ($alert) {
                $alerts[$key] = $alert;
            }
        }
        return $alerts;
    }
}
