<?php


// Kulang pa ng rules and many keywords, ai lang din yung rules and threshold (keywords or numbers)
// 
namespace App\Services;

class IntakeAndOutputCdssService
{
    const CRITICAL = 'critical';
    const WARNING = 'warning';
    const INFO = 'info';
    const NONE = 'none';

    private $oralIntakeRules = [
        [
            'condition' => 'low',
            'threshold' => 1000,
            'alert' => 'WARNING: Low oral intake detected. Patient may be at risk for dehydration.',
            'severity' => self::WARNING
        ],
        [
            'condition' => 'very_low',
            'threshold' => 500,
            'alert' => 'CRITICAL: Very low oral intake. Immediate assessment required for dehydration.',
            'severity' => self::CRITICAL
        ],
        [
            'condition' => 'excessive',
            'threshold' => 3000,
            'alert' => 'INFO: High oral intake. Monitor for fluid overload.',
            'severity' => self::INFO
        ]
    ];

    private $ivFluidsVolumeRules = [
        [
            'condition' => 'high_volume',
            'threshold' => 2000,
            'alert' => 'WARNING: Large volume of IV fluids administered. Monitor for fluid overload and edema.',
            'severity' => self::WARNING
        ],
        [
            'condition' => 'very_high_volume',
            'threshold' => 3000,
            'alert' => 'CRITICAL: Very high IV fluid volume. Assess for signs of fluid overload, pulmonary edema.',
            'severity' => self::CRITICAL
        ]
    ];

    private $urineOutputRules = [
        [
            'condition' => 'oliguria',
            'threshold' => 400,
            'alert' => 'WARNING: Oliguria detected (urine output < 400ml). Assess kidney function and hydration.',
            'severity' => self::WARNING
        ],
        [
            'condition' => 'severe_oliguria',
            'threshold' => 200,
            'alert' => 'CRITICAL: Severe oliguria (urine output < 200ml). Immediate renal assessment required.',
            'severity' => self::CRITICAL
        ],
        [
            'condition' => 'polyuria',
            'threshold' => 3000,
            'alert' => 'INFO: Polyuria detected (urine output > 3000ml). Monitor for diabetes insipidus or diuretic effect.',
            'severity' => self::INFO
        ]
    ];

    public function analyzeIntakeOutput($intakeData)
    {
        $alerts = [];

        // Analyze oral intake
        if (!empty($intakeData['oral_intake'])) {
            $oralAlert = $this->analyzeOral($intakeData['oral_intake']);
            if ($oralAlert) {
                $alerts[] = $oralAlert;
            }
        }

        // Analyze IV fluids
        if (!empty($intakeData['iv_fluids_volume'])) {
            $ivAlert = $this->analyzeIVFluids($intakeData['iv_fluids_volume'], $intakeData['iv_fluids_type'] ?? null);
            if ($ivAlert) {
                $alerts[] = $ivAlert;
            }
        }

        // Analyze urine output
        if (!empty($intakeData['urine_output'])) {
            $urineAlert = $this->analyzeOutput($intakeData['urine_output']);
            if ($urineAlert) {
                $alerts[] = $urineAlert;
            }
        }

        // Analyze fluid balance (total intake vs output)
        if (!empty($intakeData['oral_intake']) || !empty($intakeData['iv_fluids_volume']) || !empty($intakeData['urine_output'])) {
            $balanceAlert = $this->analyzeFluidBalance($intakeData);
            if ($balanceAlert) {
                $alerts[] = $balanceAlert;
            }
        }
        return implode("\n", $alerts);
    }

    private function analyzeOral($oralIntake)
    {
        foreach ($this->oralIntakeRules as $rule) {
            if ($rule['condition'] === 'very_low' && $oralIntake < $rule['threshold']) {
                return $rule['alert'];
            } elseif ($rule['condition'] === 'low' && $oralIntake < $rule['threshold']) {
                return $rule['alert'];
            } elseif ($rule['condition'] === 'excessive' && $oralIntake > $rule['threshold']) {
                return $rule['alert'];
            }
        }
        return null;
    }

    private function analyzeIVFluids($ivVolume, $ivType = null)
    {
        foreach ($this->ivFluidsVolumeRules as $rule) {
            if ($rule['condition'] === 'very_high_volume' && $ivVolume > $rule['threshold']) {
                return $rule['alert'];
            } elseif ($rule['condition'] === 'high_volume' && $ivVolume > $rule['threshold']) {
                return $rule['alert'];
            }
        }
        return null;
    }

    private function analyzeOutput($urineOutput)
    {
        foreach ($this->urineOutputRules as $rule) {
            if ($rule['condition'] === 'severe_oliguria' && $urineOutput < $rule['threshold']) {
                return $rule['alert'];
            } elseif ($rule['condition'] === 'oliguria' && $urineOutput < $rule['threshold']) {
                return $rule['alert'];
            } elseif ($rule['condition'] === 'polyuria' && $urineOutput > $rule['threshold']) {
                return $rule['alert'];
            }
        }
        return null;
    }

    private function analyzeFluidBalance($data)
    {
        $totalIntake = ($data['oral_intake'] ?? 0) + ($data['iv_fluids_volume'] ?? 0);
        $totalOutput = $data['urine_output'] ?? 0;
        
        $balance = $totalIntake - $totalOutput;

        // Positive balance (retention)
        if ($balance > 1500) {
            return 'CRITICAL: Positive fluid balance > 1500ml. Risk of fluid overload. Assess for edema and respiratory distress.';
        } elseif ($balance > 1000) {
            return 'WARNING: Positive fluid balance > 1000ml. Monitor for signs of fluid retention.';
        }

        // Negative balance (deficit)
        if ($balance < -1000) {
            return 'CRITICAL: Negative fluid balance > 1000ml. Risk of dehydration. Increase fluid intake.';
        } elseif ($balance < -500) {
            return 'WARNING: Negative fluid balance > 500ml. Monitor hydration status.';
        }

        return null;
    }
}
