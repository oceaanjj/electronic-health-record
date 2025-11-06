<?php

namespace App\Services;

class VitalCdssService
{
    const CRITICAL = 'CRITICAL';
    const WARNING = 'WARNING';
    const INFO = 'INFO';
    const NONE = 'NONE';

    /**
     * -----------------------------
     * EXPANDED RULES FOR EACH VITAL
     * -----------------------------
     */

    private $temperatureRules = [
        ['min' => null, 'max' => 34.9, 'alert' => 'Severe Hypothermia (Risk of metabolic failure)', 'severity' => self::CRITICAL],
        ['min' => 35.0, 'max' => 35.5, 'alert' => 'Mild Hypothermia (Rewarm gradually)', 'severity' => self::WARNING],
        ['min' => 35.6, 'max' => 36.0, 'alert' => 'Low Temp (Monitor for cold stress)', 'severity' => self::INFO],
        ['min' => 36.1, 'max' => 37.5, 'alert' => 'Normal Temperature', 'severity' => self::NONE],
        ['min' => 37.6, 'max' => 38.0, 'alert' => 'Low-grade Fever (Monitor hydration)', 'severity' => self::INFO],
        ['min' => 38.1, 'max' => 38.9, 'alert' => 'Mild Fever (Possible infection)', 'severity' => self::WARNING],
        ['min' => 39.0, 'max' => 40.0, 'alert' => 'High Fever (Antipyretics may be indicated)', 'severity' => self::WARNING],
        ['min' => 40.1, 'max' => null, 'alert' => 'Hyperpyrexia (Risk of febrile seizure)', 'severity' => self::CRITICAL],
    ];

    private $hrRules = [
        ['min' => null, 'max' => 50,  'alert' => 'Severe Bradycardia (Possible hypoxia or vagal response)', 'severity' => self::CRITICAL],
        ['min' => 51,   'max' => 70,  'alert' => 'Bradycardia (Monitor closely)', 'severity' => self::INFO],
        ['min' => 71,   'max' => 120, 'alert' => 'Normal Heart Rate', 'severity' => self::NONE],
        ['min' => 121,  'max' => 150, 'alert' => 'Mild Tachycardia (May indicate pain, anxiety, or fever)', 'severity' => self::INFO],
        ['min' => 151,  'max' => 170, 'alert' => 'Tachycardia (Assess hydration or infection)', 'severity' => self::WARNING],
        ['min' => 171,  'max' => null, 'alert' => 'Severe Tachycardia (Possible shock or dehydration)', 'severity' => self::CRITICAL],
    ];

    private $rrRules = [
        ['min' => null, 'max' => 8,   'alert' => 'Severe Bradypnea (Possible CNS depression)', 'severity' => self::CRITICAL],
        ['min' => 9,    'max' => 12,  'alert' => 'Mild Bradypnea (Observe airway patency)', 'severity' => self::WARNING],
        ['min' => 13,   'max' => 25,  'alert' => 'Normal Respiratory Rate', 'severity' => self::NONE],
        ['min' => 26,   'max' => 35,  'alert' => 'Tachypnea (Possible respiratory distress)', 'severity' => self::WARNING],
        ['min' => 36,   'max' => null, 'alert' => 'Severe Tachypnea (Risk of respiratory failure)', 'severity' => self::CRITICAL],
    ];

    private $bpRules = [
        ['min' => null, 'max' => 85,  'alert' => 'Severe Hypotension (Possible shock)', 'severity' => self::CRITICAL],
        ['min' => 86,   'max' => 100, 'alert' => 'Low BP (Assess perfusion, consider fluids)', 'severity' => self::WARNING],
        ['min' => 101,  'max' => 120, 'alert' => 'Normal BP', 'severity' => self::NONE],
        ['min' => 121,  'max' => 139, 'alert' => 'Prehypertension (Monitor for trend)', 'severity' => self::INFO],
        ['min' => 140,  'max' => 159, 'alert' => 'Hypertension (Recheck and assess cause)', 'severity' => self::WARNING],
        ['min' => 160,  'max' => null, 'alert' => 'Severe Hypertension (Risk of end-organ damage)', 'severity' => self::CRITICAL],
    ];

    private $spo2Rules = [
        ['min' => null, 'max' => 89,  'alert' => 'Severe Hypoxemia (Apply O₂, urgent eval)', 'severity' => self::CRITICAL],
        ['min' => 90,   'max' => 92,  'alert' => 'Moderate Hypoxemia (Check airway and breathing)', 'severity' => self::WARNING],
        ['min' => 93,   'max' => 94,  'alert' => 'Mild Hypoxemia (Reassess after intervention)', 'severity' => self::INFO],
        ['min' => 95,   'max' => 100, 'alert' => 'Normal Oxygen Saturation', 'severity' => self::NONE],
    ];

    private $severityMap = [
        self::CRITICAL => 1,
        self::WARNING => 2,
        self::INFO => 3,
        self::NONE => 4,
    ];

    private function parseBp($value)
    {
        if (is_numeric($value)) return (float)$value;
        if (is_string($value) && preg_match('/(\d{2,3})[\/, \-]?(\d{2,3})?/', $value, $matches)) {
            return (float)$matches[1];
        }
        return null;
    }

    private function getRulesForType($type)
    {
        return match ($type) {
            'temperature' => $this->temperatureRules,
            'hr' => $this->hrRules,
            'rr' => $this->rrRules,
            'bp' => $this->bpRules,
            'spo2' => $this->spo2Rules,
            default => [],
        };
    }

    public function getAlertForVital($param, $value)
    {
        if ($value === null || $value === '') {
            return ['alert' => '', 'severity' => self::NONE];
        }

        $numericValue = null;
        $rules = [];

        switch ($param) {
            case 'temperature':
            case 'hr':
            case 'rr':
            case 'spo2':
                $rules = $this->getRulesForType($param);
                $numericValue = (float)$value;
                break;
            case 'bp':
                $rules = $this->bpRules;
                $numericValue = $this->parseBp($value);
                if ($numericValue === null && $value !== '') {
                    return ['alert' => 'Invalid BP', 'severity' => self::WARNING];
                }
                break;
            default:
                return ['alert' => '', 'severity' => self::NONE];
        }

        foreach ($rules as $rule) {
            $minOk = is_null($rule['min']) || $numericValue >= $rule['min'];
            $maxOk = is_null($rule['max']) || $numericValue <= $rule['max'];
            if ($minOk && $maxOk) {
                return ['alert' => $rule['alert'], 'severity' => $rule['severity']];
            }
        }

        return ['alert' => 'Out of range', 'severity' => self::WARNING];
    }

    /**
     * ----------------------------------------
     * COMBINED VITALS ANALYSIS (SMART LOGIC)
     * ----------------------------------------
     */
    private function detectCombinedAlerts($vitals)
    {
        $alerts = [];

        $temp = $vitals['temperature'] ?? null;
        $hr = $vitals['hr'] ?? null;
        $bp = $vitals['bp'] ?? null;
        $rr = $vitals['rr'] ?? null;
        $spo2 = $vitals['spo2'] ?? null;

        if ($temp > 38 && $hr > 120) {
            $alerts[] = ['alert' => 'Fever with tachycardia — Possible infection or dehydration.', 'severity' => self::WARNING];
        }

        if ($hr > 150 && $bp < 90) {
            $alerts[] = ['alert' => 'Tachycardia with hypotension — Possible early shock.', 'severity' => self::CRITICAL];
        }

        if ($spo2 < 92 && $rr > 30) {
            $alerts[] = ['alert' => 'Low SpO₂ with tachypnea — Possible respiratory distress.', 'severity' => self::CRITICAL];
        }

        if ($temp > 39 && $rr > 30) {
            $alerts[] = ['alert' => 'High fever with rapid breathing — Risk of sepsis.', 'severity' => self::CRITICAL];
        }

        if ($spo2 < 94 && $hr > 130) {
            $alerts[] = ['alert' => 'Desaturation with tachycardia — Possible hypoxia or anemia.', 'severity' => self::WARNING];
        }

        if ($bp < 90 && $spo2 < 90) {
            $alerts[] = ['alert' => 'Hypotension and hypoxia — Critical instability.', 'severity' => self::CRITICAL];
        }

        return $alerts;
    }

    public function analyzeVitalsForAlerts(array $vitals)
    {
        $allAlerts = [];

        foreach ($vitals as $type => $value) {
            if ($value === null || $value === '') continue;
            $result = $this->getAlertForVital($type, $value);
            if ($result['severity'] !== self::NONE) {
                $allAlerts[] = $result;
            }
        }

        // Add smart combined alerts
        $combinedAlerts = $this->detectCombinedAlerts($vitals);
        $allAlerts = array_merge($allAlerts, $combinedAlerts);

        if (empty($allAlerts)) {
            return ['alert' => 'Vitals stable.', 'severity' => self::NONE];
        }

        usort($allAlerts, fn($a, $b) => $this->severityMap[$a['severity']] <=> $this->severityMap[$b['severity']]);
        $highestSeverity = $allAlerts[0]['severity'];
        $summary = implode('; ', array_column($allAlerts, 'alert'));

        return [
            'alert' => $summary,
            'severity' => $highestSeverity,
        ];
    }
}
