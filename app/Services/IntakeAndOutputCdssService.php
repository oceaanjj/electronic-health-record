<?php

namespace App\Services;

class IntakeAndOutputCdssService extends BaseCdssService
{
    public function __construct()
    {
        parent::__construct();
        $this->shouldTranslate = false;
    }

    private $oralIntakeRules = [
        ['condition' => 'low', 'threshold' => 1000, 'alert' => 'WARNING: Low oral intake detected. Patient may be at risk for dehydration.', 'severity' => self::WARNING],
        ['condition' => 'very_low', 'threshold' => 500, 'alert' => 'CRITICAL: Very low oral intake. Immediate assessment required for dehydration.', 'severity' => self::CRITICAL],
        ['condition' => 'excessive', 'threshold' => 3000, 'alert' => 'INFO: High oral intake. Monitor for fluid overload.', 'severity' => self::INFO]
    ];

    private $ivFluidsVolumeRules = [
        ['condition' => 'high_volume', 'threshold' => 2000, 'alert' => 'WARNING: Large volume of IV fluids administered. Monitor for fluid overload and edema.', 'severity' => self::WARNING],
        ['condition' => 'very_high_volume', 'threshold' => 3000, 'alert' => 'CRITICAL: Very high IV fluid volume. Assess for signs of fluid overload, pulmonary edema.', 'severity' => self::CRITICAL]
    ];

    private $urineOutputRules = [
        ['condition' => 'oliguria', 'threshold' => 400, 'alert' => 'WARNING: Oliguria detected (urine output < 400ml). Assess kidney function and hydration.', 'severity' => self::WARNING],
        ['condition' => 'severe_oliguria', 'threshold' => 200, 'alert' => 'CRITICAL: Severe oliguria (urine output < 200ml). Immediate renal assessment required.', 'severity' => self::CRITICAL],
        ['condition' => 'polyuria', 'threshold' => 3000, 'alert' => 'INFO: Polyuria detected (urine output > 3000ml). Monitor for diabetes insipidus or diuretic effect.', 'severity' => self::INFO]
    ];

    public function analyzeIntakeOutput($intakeData)
    {
        $allAlerts = [];

        // 1. Analyze oral intake
        if (!empty($intakeData['oral_intake'])) {
            $val = $intakeData['oral_intake'];
            if (is_numeric($val)) {
                foreach ($this->oralIntakeRules as $rule) {
                    if ($rule['condition'] === 'very_low' && $val < $rule['threshold']) $allAlerts[] = $rule;
                    elseif ($rule['condition'] === 'low' && $val < $rule['threshold']) $allAlerts[] = $rule;
                    elseif ($rule['condition'] === 'excessive' && $val > $rule['threshold']) $allAlerts[] = $rule;
                }
            } else {
                $res = $this->runAnalysis($val, []); // Check for Red Flags in notes
                if ($res) $allAlerts[] = $res;
            }
        }

        // 2. Analyze IV fluids
        if (!empty($intakeData['iv_fluids_volume']) && is_numeric($intakeData['iv_fluids_volume'])) {
            $val = $intakeData['iv_fluids_volume'];
            foreach ($this->ivFluidsVolumeRules as $rule) {
                if ($rule['condition'] === 'very_high_volume' && $val > $rule['threshold']) $allAlerts[] = $rule;
                elseif ($rule['condition'] === 'high_volume' && $val > $rule['threshold']) $allAlerts[] = $rule;
            }
        }

        // 3. Analyze urine output
        if (!empty($intakeData['urine_output']) && is_numeric($intakeData['urine_output'])) {
            $val = $intakeData['urine_output'];
            foreach ($this->urineOutputRules as $rule) {
                if ($rule['condition'] === 'severe_oliguria' && $val < $rule['threshold']) $allAlerts[] = $rule;
                elseif ($rule['condition'] === 'oliguria' && $val < $rule['threshold']) $allAlerts[] = $rule;
                elseif ($rule['condition'] === 'polyuria' && $val > $rule['threshold']) $allAlerts[] = $rule;
            }
        }

        // 4. Fluid Balance
        $balanceAlert = $this->analyzeFluidBalance($intakeData);
        if ($balanceAlert) $allAlerts[] = $balanceAlert;

        if (empty($allAlerts)) return ['alert' => 'No Findings', 'severity' => self::NONE];

        // Sort and translate the summary
        usort($allAlerts, fn($a, $b) => $this->getSeverityValue($b['severity']) <=> $this->getSeverityValue($a['severity']));
        
        $summary = implode('; ', array_unique(array_column($allAlerts, 'alert')));
        return [
            'alert' => $summary,
            'severity' => $allAlerts[0]['severity']
        ];
    }

    private function analyzeFluidBalance($data)
    {
        $totalIntake = ($data['oral_intake'] ?? 0) + ($data['iv_fluids_volume'] ?? 0);
        $totalOutput = $data['urine_output'] ?? 0;
        $balance = $totalIntake - $totalOutput;

        if ($balance > 1500) return ['alert' => 'CRITICAL: Positive fluid balance > 1500ml. Risk of fluid overload. Assess for edema and respiratory distress.', 'severity' => self::CRITICAL];
        if ($balance < -1000) return ['alert' => 'CRITICAL: Negative fluid balance > 1000ml. Risk of dehydration. Increase fluid intake.', 'severity' => self::CRITICAL];
        
        return null;
    }
}
