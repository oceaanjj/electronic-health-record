<?php
namespace App\Services;

class VitalCdssService
{
    const CRITICAL = 'CRITICAL';
    const WARNING = 'WARNING';
    const INFO = 'info';
    const NONE = 'NONE';

    private $temperatureRules = [
        ['min' => 40, 'max' => null, 'alert' => 'Hyperpyrexia: Immediate intervention required!', 'severity' => self::CRITICAL],
        ['min' => 38, 'max' => 39.9, 'alert' => 'Fever: Monitor closely.', 'severity' => self::WARNING],
        ['min' => 36, 'max' => 37.9, 'alert' => 'Normal temperature.', 'severity' => self::NONE],
        ['min' => 37, 'max' => 37.5, 'alert' => 'Slightly elevated temperature.', 'severity' => self::WARNING],
        ['min' => null, 'max' => 35.9, 'alert' => 'Hypothermia: Immediate warming required!', 'severity' => self::CRITICAL],
    ];

    private $hrRules = [
        ['min' => 180, 'max' => null, 'alert' => 'Severe tachycardia: Immediate intervention required!', 'severity' => self::CRITICAL],
        ['min' => 150, 'max' => 179, 'alert' => 'Tachycardia: Urgent evaluation needed.', 'severity' => self::CRITICAL],
        ['min' => 130, 'max' => 149, 'alert' => 'High heart rate: Monitor closely.', 'severity' => self::WARNING],
        ['min' => 101, 'max' => 129, 'alert' => 'Mildly elevated heart rate.', 'severity' => self::INFO],
        ['min' => 60, 'max' => 100, 'alert' => 'Normal heart rate.', 'severity' => self::NONE],
        ['min' => 50, 'max' => 59, 'alert' => 'Slightly low heart rate.', 'severity' => self::INFO],
        ['min' => 30, 'max' => 49, 'alert' => 'Bradycardia: Monitor patient closely.', 'severity' => self::WARNING],
        ['min' => null, 'max' => 29, 'alert' => 'Severe bradycardia: Immediate intervention required!', 'severity' => self::CRITICAL],
    ];

    private $rrRules = [
        ['min' => 40, 'max' => null, 'alert' => 'Severe tachypnea: Immediate intervention required!', 'severity' => self::CRITICAL],
        ['min' => 30, 'max' => 39, 'alert' => 'Tachypnea: Urgent evaluation needed.', 'severity' => self::CRITICAL],
        ['min' => 25, 'max' => 29, 'alert' => 'Slightly elevated respiratory rate.', 'severity' => self::WARNING],
        ['min' => 20, 'max' => 24, 'alert' => 'Mildly elevated respiratory rate.', 'severity' => self::INFO],
        ['min' => 12, 'max' => 19, 'alert' => 'Normal respiratory rate.', 'severity' => self::NONE],
        ['min' => 10, 'max' => 11, 'alert' => 'Slightly low respiratory rate.', 'severity' => self::INFO],
        ['min' => 6, 'max' => 9, 'alert' => 'Bradypnea: Monitor closely.', 'severity' => self::WARNING],
        ['min' => null, 'max' => 5, 'alert' => 'Severe bradypnea: Immediate intervention required!', 'severity' => self::CRITICAL],
    ];

    private $bpRules = [
        ['min' => null, 'max' => 69, 'alert' => 'Severe hypotension: Immediate intervention required!', 'severity' => self::CRITICAL],
        ['min' => 70, 'max' => 89, 'alert' => 'Hypotension: Check for shock.', 'severity' => self::CRITICAL],
        ['min' => 90, 'max' => 99, 'alert' => 'Low-normal BP: Monitor patient closely.', 'severity' => self::INFO],
        ['min' => 100, 'max' => 119, 'alert' => 'Normal BP.', 'severity' => self::NONE],
        ['min' => 120, 'max' => 129, 'alert' => 'Prehypertension: Monitor patient.', 'severity' => self::WARNING],
        ['min' => 130, 'max' => 139, 'alert' => 'Borderline high BP: Consider lifestyle measures.', 'severity' => self::INFO],
        ['min' => 140, 'max' => 159, 'alert' => 'Hypertension: Evaluate and monitor.', 'severity' => self::WARNING],
        ['min' => 160, 'max' => null, 'alert' => 'Severe hypertension: Immediate medical attention required!', 'severity' => self::CRITICAL],
    ];

    private $spo2Rules = [
        ['min' => 97, 'max' => null, 'alert' => 'Normal SpO₂.', 'severity' => self::NONE],
        ['min' => 95, 'max' => 96, 'alert' => 'Slightly low SpO₂: Monitor patient.', 'severity' => self::INFO],
        ['min' => 90, 'max' => 94, 'alert' => 'Mild hypoxia: Give supplemental oxygen if needed.', 'severity' => self::WARNING],
        ['min' => 85, 'max' => 89, 'alert' => 'Moderate hypoxia: Start oxygen therapy and monitor closely.', 'severity' => self::CRITICAL],
        ['min' => null, 'max' => 84, 'alert' => 'Severe hypoxia: Immediate oxygen therapy required!', 'severity' => self::CRITICAL],
    ];

    /**
     * Analyze the vitals and return alerts
     */
    public function analyzeVitals(array $vitals)
    {
        $alerts = [];

        foreach ($vitals as $key => $value) {
            // Extract time and vital type from the input name
            if (preg_match('/^(temperature|hr|rr|bp|spo2)_(\d{2}:\d{2})$/', $key, $matches)) {
                $type = $matches[1];
                $time = $matches[2];

                if ($value === null || $value === '') continue;

                $ruleSet = $this->getRulesForType($type);

                foreach ($ruleSet as $rule) {
                    if (($rule['min'] === null || $value >= $rule['min']) && ($rule['max'] === null || $value <= $rule['max'])) {
                        $alerts[$time][] = [
                            'alert' => $rule['alert'],
                            'severity' => $rule['severity'],
                        ];
                        break; // stop at first matching rule
                    }
                }
            }
        }

        return $alerts;
    }

    private function getRulesForType($type)
    {
        return match($type) {
            'temperature' => $this->temperatureRules,
            'hr' => $this->hrRules,
            'rr' => $this->rrRules,
            'bp' => $this->bpRules,
            'spo2' => $this->spo2Rules,
            default => [],
        };
    }
}
