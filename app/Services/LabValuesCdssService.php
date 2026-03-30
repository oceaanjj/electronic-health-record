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
     */
    private $plausibilityLimits = [
        'wbc' => ['max' => 500, 'min' => 0],
        'rbc' => ['max' => 15, 'min' => 0],
        'hgb' => ['max' => 30, 'min' => 1],
        'hct' => ['max' => 80, 'min' => 5],
        'platelets' => ['max' => 3000, 'min' => 0],
        'mcv' => ['max' => 150, 'min' => 40],
        'mch' => ['max' => 60, 'min' => 10],
        'mchc' => ['max' => 50, 'min' => 20],
        'rdw' => ['max' => 40, 'min' => 8],
        'neutrophils' => ['max' => 100, 'min' => 0],
        'lymphocytes' => ['max' => 100, 'min' => 0],
        'monocytes' => ['max' => 100, 'min' => 0],
        'eosinophils' => ['max' => 100, 'min' => 0],
        'basophils' => ['max' => 100, 'min' => 0],
    ];

    private $wbcRules = [
        ['ageGroup' => 'neonate', 'min' => 9.0, 'max' => 30.0, 'alert' => 'Normal WBC (Neonate).', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => null, 'max' => 8.9, 'alert' => 'Leukopenia (Neonate): Risk of sepsis.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'neonate', 'min' => 30.1, 'max' => null, 'alert' => 'Leukocytosis (Neonate): Possible infection/stress.', 'severity' => self::WARNING],
        
        ['ageGroup' => 'infant', 'min' => 6.0, 'max' => 17.5, 'alert' => 'Normal WBC (Infant).', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => null, 'max' => 5.9, 'alert' => 'Leukopenia: Risk of infection.', 'severity' => self::WARNING],
        ['ageGroup' => 'infant', 'min' => 17.6, 'max' => null, 'alert' => 'Leukocytosis: Possible infection.', 'severity' => self::WARNING],

        ['ageGroup' => 'child', 'min' => 5.0, 'max' => 15.5, 'alert' => 'Normal WBC (Child).', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 4.9, 'alert' => 'Leukopenia: Risk of infection.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'child', 'min' => 15.6, 'max' => null, 'alert' => 'Leukocytosis: Possible infection.', 'severity' => self::WARNING],

        ['ageGroup' => 'adult', 'min' => 4.5, 'max' => 11.0, 'alert' => 'Normal WBC.', 'severity' => self::NONE],
        ['ageGroup' => 'adult', 'min' => null, 'max' => 4.4, 'alert' => 'Leukopenia: Significant risk of infection.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'adult', 'min' => 11.1, 'max' => null, 'alert' => 'Leukocytosis: Possible infection or inflammation.', 'severity' => self::WARNING],
    ];

    private $rbcRules = [
        ['ageGroup' => 'neonate', 'min' => 3.9, 'max' => 5.9, 'alert' => 'Normal RBC.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => null, 'max' => 3.8, 'alert' => 'Low RBC (Neonate).', 'severity' => self::WARNING],
        ['ageGroup' => 'infant', 'min' => 3.1, 'max' => 5.5, 'alert' => 'Normal RBC.', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => null, 'max' => 3.0, 'alert' => 'Low RBC (Infant).', 'severity' => self::WARNING],
        ['ageGroup' => 'adult', 'min' => 3.9, 'max' => 5.7, 'alert' => 'Normal RBC.', 'severity' => self::NONE],
        ['ageGroup' => 'adult', 'min' => null, 'max' => 3.8, 'alert' => 'Low RBC: Potential anemia.', 'severity' => self::WARNING],
        ['ageGroup' => 'adult', 'min' => 5.8, 'max' => null, 'alert' => 'High RBC: Erythrocytosis.', 'severity' => self::WARNING],
    ];

    private $hgbRules = [
        ['ageGroup' => 'neonate', 'min' => 13.5, 'max' => 24.0, 'alert' => 'Normal Hgb.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => null, 'max' => 13.4, 'alert' => 'Anemia (Neonate).', 'severity' => self::WARNING],
        ['ageGroup' => 'infant', 'min' => 9.0, 'max' => 14.0, 'alert' => 'Normal Hgb.', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => null, 'max' => 8.9, 'alert' => 'Anemia: Check for iron deficiency.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'adult', 'min' => 12.0, 'max' => 17.5, 'alert' => 'Normal Hgb.', 'severity' => self::NONE],
        ['ageGroup' => 'adult', 'min' => null, 'max' => 11.9, 'alert' => 'Anemia.', 'severity' => self::WARNING],
        ['ageGroup' => 'adult', 'min' => null, 'max' => 7.0, 'alert' => 'CRITICAL ANEMIA: Transfusion may be required.', 'severity' => self::CRITICAL],
    ];

    private $hctRules = [
        ['ageGroup' => 'neonate', 'min' => 42, 'max' => 65, 'alert' => 'Normal Hematocrit.', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => 28, 'max' => 41, 'alert' => 'Normal Hematocrit.', 'severity' => self::NONE],
        ['ageGroup' => 'adult', 'min' => 36, 'max' => 50, 'alert' => 'Normal Hematocrit.', 'severity' => self::NONE],
        ['ageGroup' => 'all', 'min' => null, 'max' => 25, 'alert' => 'Low Hematocrit: Significant anemia.', 'severity' => self::CRITICAL],
    ];

    private $plateletRules = [
        ['ageGroup' => 'neonate', 'min' => 150, 'max' => 450, 'alert' => 'Normal Platelets.', 'severity' => self::NONE],
        ['ageGroup' => 'all', 'min' => 150, 'max' => 450, 'alert' => 'Normal Platelets.', 'severity' => self::NONE],
        ['ageGroup' => 'all', 'min' => null, 'max' => 149, 'alert' => 'Thrombocytopenia: Risk of bleeding.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'all', 'min' => null, 'max' => 50, 'alert' => 'CRITICAL THROMBOCYTOPENIA: Severe bleeding risk.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'all', 'min' => 451, 'max' => null, 'alert' => 'Thrombocytosis.', 'severity' => self::WARNING],
    ];

    private $mcvRules = [
        ['ageGroup' => 'neonate', 'min' => 88, 'max' => 123, 'alert' => 'Normal MCV.', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => 70, 'max' => 112, 'alert' => 'Normal MCV.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => 75, 'max' => 90, 'alert' => 'Normal MCV.', 'severity' => self::NONE],
        ['ageGroup' => 'adult', 'min' => 80, 'max' => 100, 'alert' => 'Normal MCV.', 'severity' => self::NONE],
        ['ageGroup' => 'adult', 'min' => null, 'max' => 79, 'alert' => 'Microcytosis: Possible iron deficiency.', 'severity' => self::WARNING],
        ['ageGroup' => 'adult', 'min' => 100.1, 'max' => null, 'alert' => 'Macrocytosis: Possible B12/Folate deficiency.', 'severity' => self::WARNING],
    ];

    private $mchRules = [
        ['ageGroup' => 'all', 'min' => 27, 'max' => 33, 'alert' => 'Normal MCH.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => 31, 'max' => 37, 'alert' => 'Normal MCH.', 'severity' => self::NONE],
        ['ageGroup' => 'all', 'min' => null, 'max' => 26, 'alert' => 'Low MCH (Hypochromic).', 'severity' => self::WARNING],
    ];

    private $mchcRules = [
        ['ageGroup' => 'all', 'min' => 32, 'max' => 36, 'alert' => 'Normal MCHC.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => 28, 'max' => 36, 'alert' => 'Normal MCHC.', 'severity' => self::NONE],
    ];

    private $rdwRules = [
        ['ageGroup' => 'all', 'min' => 11.5, 'max' => 14.5, 'alert' => 'Normal RDW.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => 14, 'max' => 19, 'alert' => 'Normal RDW.', 'severity' => self::NONE],
        ['ageGroup' => 'all', 'min' => 14.6, 'max' => null, 'alert' => 'High RDW: High variation in RBC size.', 'severity' => self::WARNING],
    ];

    private $neutrophilRules = [
        ['ageGroup' => 'adult', 'min' => 40, 'max' => 75, 'alert' => 'Normal Neutrophils.', 'severity' => self::NONE],
        ['ageGroup' => 'adult', 'min' => 75.1, 'max' => null, 'alert' => 'Neutrophilia: Suggests bacterial infection.', 'severity' => self::WARNING],
        ['ageGroup' => 'adult', 'min' => null, 'max' => 39, 'alert' => 'Neutropenia: Risk of infection.', 'severity' => self::WARNING],
    ];

    private $lymphocyteRules = [
        ['ageGroup' => 'adult', 'min' => 20, 'max' => 45, 'alert' => 'Normal Lymphocytes.', 'severity' => self::NONE],
        ['ageGroup' => 'adult', 'min' => 45.1, 'max' => null, 'alert' => 'Lymphocytosis: Suggests viral infection.', 'severity' => self::WARNING],
        ['ageGroup' => 'adult', 'min' => null, 'max' => 19, 'alert' => 'Lymphocytopenia.', 'severity' => self::WARNING],
    ];

    private $monocyteRules = [
        ['ageGroup' => 'all', 'min' => 2, 'max' => 10, 'alert' => 'Normal Monocytes.', 'severity' => self::NONE],
        ['ageGroup' => 'all', 'min' => 10.1, 'max' => null, 'alert' => 'Monocytosis: Chronic inflammation/infection.', 'severity' => self::WARNING],
    ];

    private $eosinophilRules = [
        ['ageGroup' => 'all', 'min' => 1, 'max' => 6, 'alert' => 'Normal Eosinophils.', 'severity' => self::NONE],
        ['ageGroup' => 'all', 'min' => 6.1, 'max' => null, 'alert' => 'Eosinophilia: Allergic reaction or parasites.', 'severity' => self::WARNING],
    ];

    private $basophilRules = [
        ['ageGroup' => 'all', 'min' => 0, 'max' => 1, 'alert' => 'Normal Basophils.', 'severity' => self::NONE],
        ['ageGroup' => 'all', 'min' => 1.1, 'max' => null, 'alert' => 'Basophilia.', 'severity' => self::WARNING],
    ];

    private function detectCorrelatedAlerts($labs)
    {
        $alerts = [];
        $wbc = (float)($labs->wbc_result ?? 0);
        $neut = (float)($labs->neutrophils_result ?? 0);
        $lymph = (float)($labs->lymphocytes_result ?? 0);
        $hgb = (float)($labs->hgb_result ?? 0);
        $mcv = (float)($labs->mcv_result ?? 0);
        $rdw = (float)($labs->rdw_result ?? 0);

        if ($wbc > 11 && $neut > 75) $alerts[] = ['text' => 'Pattern suggests Acute Bacterial Infection (High WBC + High Neutrophils).', 'severity' => self::WARNING];
        if ($wbc > 11 && $lymph > 50) $alerts[] = ['text' => 'Pattern suggests Viral Infection (High WBC + High Lymphocytes).', 'severity' => self::WARNING];
        if ($hgb < 11 && $mcv < 80) $alerts[] = ['text' => 'Pattern suggests Microcytic Anemia (Possible Iron Deficiency).', 'severity' => self::WARNING];
        if ($hgb < 11 && $mcv > 100) $alerts[] = ['text' => 'Pattern suggests Macrocytic Anemia (Possible B12/Folate deficiency).', 'severity' => self::WARNING];
        if ($rdw > 15 && $hgb < 11) $alerts[] = ['text' => 'Pattern suggests nutritional deficiency anemia (High RDW + Low Hgb).', 'severity' => self::WARNING];
        if ($wbc < 4 || $wbc > 30) $alerts[] = ['text' => 'CRITICAL: Extreme WBC count. High risk for sepsis or hematologic crisis.', 'severity' => self::CRITICAL];

        return $alerts;
    }

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

        if (!$this->isPlausible($param, $numericValue)) {
            $msg = "POSSIBLE TYPO: The value ($numericValue) for $param seems physiologically impossible. Please verify.";
            return ['alert' => $msg, 'severity' => self::WARNING];
        }

        $rules = match ($param) {
            'wbc' => $this->wbcRules,
            'rbc' => $this->rbcRules,
            'hgb' => $this->hgbRules,
            'hct' => $this->hctRules,
            'platelets' => $this->plateletRules,
            'mcv' => $this->mcvRules,
            'mch' => $this->mchRules,
            'mchc' => $this->mchcRules,
            'rdw' => $this->rdwRules,
            'neutrophils' => $this->neutrophilRules,
            'lymphocytes' => $this->lymphocyteRules,
            'monocytes' => $this->monocyteRules,
            'eosinophils' => $this->eosinophilRules,
            'basophils' => $this->basophilRules,
            default => []
        };

        // --- SMART FALLBACK ---
        // 1. Check if the specific age group exists in these rules
        $hasSpecificGroup = false;
        foreach ($rules as $rule) {
            if ($rule['ageGroup'] === $ageGroup) {
                $hasSpecificGroup = true;
                break;
            }
        }

        // 2. If no specific rule for Adolescent/Child, fall back to Adult ranges
        if (!$hasSpecificGroup && ($ageGroup === 'adolescent' || $ageGroup === 'child')) {
            $ageGroup = 'adult';
        }

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
            'wbc' => 'wbc_result', 'rbc' => 'rbc_result', 'hgb' => 'hgb_result', 
            'hct' => 'hct_result', 'platelets' => 'platelets_result', 'mcv' => 'mcv_result',
            'mch' => 'mch_result', 'mchc' => 'mchc_result', 'rdw' => 'rdw_result',
            'neutrophils' => 'neutrophils_result', 'lymphocytes' => 'lymphocytes_result',
            'monocytes' => 'monocytes_result', 'eosinophils' => 'eosinophils_result',
            'basophils' => 'basophils_result'
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
        if (empty($patient->birthdate)) {
             // Fallback to age column if birthdate is empty
             $age = $patient->age ?? 0;
             if ($age < 1) return 'infant';
             if ($age < 12) return 'child';
             if ($age <= 18) return 'adolescent';
             return 'adult';
        }
        $dob = Carbon::parse($patient->birthdate);
        $now = Carbon::now();
        $ageInYears = $dob->diffInYears($now);
        $ageInDays = $dob->diffInDays($now);

        if ($ageInDays <= 30) return 'neonate';
        if ($ageInYears < 2) return 'infant';
        if ($ageInYears < 12) return 'child';
        if ($ageInYears <= 18) return 'adolescent';
        return 'adult';
    }
}
