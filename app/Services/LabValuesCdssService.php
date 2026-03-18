<?php

namespace App\Services;

use Carbon\Carbon; 
use Illuminate\Support\Facades\Log; 

class LabValuesCdssService extends BaseCdssService
{
    public function __construct()
    {
        parent::__construct();
        $this->shouldTranslate = false;
    }

    /**
     * HUMAN PLAUSIBILITY LIMITS (Sanity Checks)
     * Values beyond these are likely typos/data entry errors.
     */
    private $plausibilityLimits = [
        'wbc' => ['max' => 500, 'min' => 0],
        'hgb' => ['max' => 30, 'min' => 1],
        'platelets' => ['max' => 3000, 'min' => 0],
        'neutrophils' => ['max' => 100, 'min' => 0],
        'lymphocytes' => ['max' => 100, 'min' => 0],
    ];

    private $wbcRules = [
        ['ageGroup' => 'neonate', 'min' => 9.0, 'max' => 34.0, 'alert' => 'Normal WBC.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => null, 'max' => 8.9, 'alert' => 'Leukopenia (Neonate): Risk of sepsis.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'neonate', 'min' => 34.1, 'max' => null, 'alert' => 'Leukocytosis (Neonate): Possible infection/stress.', 'severity' => self::WARNING],
        ['ageGroup' => 'infant', 'min' => 6.0, 'max' => 17.5, 'alert' => 'Normal WBC.', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => null, 'max' => 5.9, 'alert' => 'Leukopenia: Risk of infection.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 4.0, 'max' => 15.5, 'alert' => 'Normal WBC.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 3.9, 'alert' => 'Leukopenia: Risk of infection.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'adult', 'min' => 4.0, 'max' => 11.0, 'alert' => 'Normal WBC.', 'severity' => self::NONE],
        ['ageGroup' => 'adult', 'min' => null, 'max' => 3.9, 'alert' => 'Leukopenia: Significant risk of infection.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'adult', 'min' => 11.1, 'max' => null, 'alert' => 'Leukocytosis: Possible infection or inflammation.', 'severity' => self::WARNING],
    ];

    private $hgbRules = [
        ['ageGroup' => 'neonate', 'min' => 13.5, 'max' => 24.0, 'alert' => 'Normal Hgb.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => null, 'max' => 13.4, 'alert' => 'Anemia (Neonate).', 'severity' => self::WARNING],
        ['ageGroup' => 'infant', 'min' => 9.0, 'max' => 14.0, 'alert' => 'Normal Hgb.', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => null, 'max' => 8.9, 'alert' => 'Anemia: Check for iron deficiency.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'adult', 'min' => 12.0, 'max' => 17.5, 'alert' => 'Normal Hgb.', 'severity' => self::NONE],
        ['ageGroup' => 'adult', 'min' => null, 'max' => 11.9, 'alert' => 'Anemia.', 'severity' => self::WARNING],
    ];

    private $plateletRules = [
        ['ageGroup' => 'neonate', 'min' => 84, 'max' => 478, 'alert' => 'Normal Platelets.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => null, 'max' => 83, 'alert' => 'Thrombocytopenia (Neonate): Risk of bleeding.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'all', 'min' => 150, 'max' => 450, 'alert' => 'Normal Platelets.', 'severity' => self::NONE],
        ['ageGroup' => 'all', 'min' => null, 'max' => 149, 'alert' => 'Thrombocytopenia: Risk of bleeding.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'all', 'min' => 451, 'max' => null, 'alert' => 'Thrombocytosis.', 'severity' => self::WARNING],
    ];

    private function detectCorrelatedAlerts($labs)
    {
        $alerts = [];
        $wbc = (float)($labs->wbc_result ?? 0);
        $neut = (float)($labs->neutrophils_result ?? 0);
        $lymph = (float)($labs->lymphocytes_result ?? 0);
        $hgb = (float)($labs->hgb_result ?? 0);
        $mcv = (float)($labs->mcv_result ?? 0);

        if ($wbc > 11 && $neut > 75) $alerts[] = ['text' => 'Pattern suggests Acute Bacterial Infection (High WBC + High Neutrophils).', 'severity' => self::WARNING];
        if ($wbc > 11 && $lymph > 50) $alerts[] = ['text' => 'Pattern suggests Viral Infection (High WBC + High Lymphocytes).', 'severity' => self::WARNING];
        if ($hgb < 11 && $mcv < 80) $alerts[] = ['text' => 'Pattern suggests Microcytic Anemia (Possible Iron Deficiency).', 'severity' => self::WARNING];
        if ($wbc < 4 || $wbc > 30) $alerts[] = ['text' => 'CRITICAL: Extreme WBC count. High risk for sepsis or hematologic crisis.', 'severity' => self::CRITICAL];

        return $alerts;
    }

    /**
     * Checks if a value is physiologically possible.
     */
    protected function isPlausible($param, $value)
    {
        if (!isset($this->plausibilityLimits[$param])) return true;
        $limits = $this->plausibilityLimits[$param];
        return ($value >= $limits['min'] && $value <= $limits['max']);
    }

    public function checkLabResult($param, $value, $ageGroup)
    {
        if ($value === null || $value === '' || $value === 'N/A' || !is_numeric($value)) {
            return ['alert' => 'No findings.', 'severity' => self::NONE];
        }

        $numericValue = (float) $value;

        // --- NEW: PLAUSIBILITY CHECK ---
        if (!$this->isPlausible($param, $numericValue)) {
            $msg = "POSSIBLE TYPO: The value ($numericValue) for $param seems physiologically impossible. Please verify your data entry.";
            return ['alert' => $msg, 'severity' => self::WARNING];
        }

        $rules = match ($param) {
            'wbc' => $this->wbcRules,
            'hgb' => $this->hgbRules,
            'platelets' => $this->plateletRules,
            default => []
        };

        foreach ($rules as $rule) {
            if ($rule['ageGroup'] !== 'all' && $rule['ageGroup'] !== $ageGroup) continue;
            $minOk = is_null($rule['min']) || $numericValue >= $rule['min'];
            $maxOk = is_null($rule['max']) || $numericValue <= $rule['max'];
            if ($minOk && $maxOk) return ['alert' => $rule['alert'], 'severity' => $rule['severity']];
        }

        return ['alert' => 'No findings.', 'severity' => self::NONE];
    }

    public function runLabCdss($labValue, $ageGroup)
    {
        $finalAlerts = [];
        $params = [
            'wbc' => 'wbc_result', 'hgb' => 'hgb_result', 'platelets' => 'platelets_result'
        ];

        foreach ($params as $param => $field) {
            $val = $labValue->$field ?? null;
            $result = $this->checkLabResult($param, $val, $ageGroup);
            if ($result['severity'] !== self::NONE) {
                $finalAlerts[$param . '_alerts'][] = [
                    'text' => $result['alert'],
                    'severity' => $result['severity'],
                ];
            }
        }

        // Only run correlations if values are plausible
        $plausibleWbc = $this->isPlausible('wbc', (float)($labValue->wbc_result ?? 0));
        if ($plausibleWbc) {
            $correlated = $this->detectCorrelatedAlerts($labValue);
            foreach ($correlated as $c) {
                $finalAlerts['correlation_alerts'][] = [
                    'text' => $c['text'],
                    'severity' => $c['severity'],
                ];
            }
        }

        return $finalAlerts;
    }

    public function getAgeGroup(\App\Models\Patient $patient): string
    {
        if (empty($patient->date_of_birth)) return 'adult';
        $dob = Carbon::parse($patient->date_of_birth);
        $ageInYears = $dob->diffInYears(Carbon::now());
        if ($dob->diffInDays(Carbon::now()) <= 30) return 'neonate';
        if ($ageInYears < 2) return 'infant';
        if ($ageInYears < 12) return 'child';
        if ($ageInYears <= 18) return 'adolescent';
        return 'adult';
    }
}
