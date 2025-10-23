<?php

namespace App\Services;

class VitalCdssService
{
    const CRITICAL = 'CRITICAL';
    const WARNING = 'WARNING';
    const INFO = 'INFO';
    const NONE = 'NONE';

    private $temperatureRules = [
        ['min' => null, 'max' => 35.0, 'alert' => 'Severe Hypothermia', 'severity' => self::CRITICAL],
        ['min' => 35.1, 'max' => 36.0, 'alert' => 'Low Temp', 'severity' => self::INFO],
        ['min' => 36.1, 'max' => 38.0, 'alert' => 'Normal Temp', 'severity' => self::NONE],
        ['min' => 38.1, 'max' => 39.0, 'alert' => 'Mild Fever', 'severity' => self::INFO],
        ['min' => 39.1, 'max' => null, 'alert' => 'High Fever (Risk of infection)', 'severity' => self::WARNING],
    ];

    private $hrRules = [
        ['min' => null, 'max' => 40,   'alert' => 'Severe Bradycardia (Risk of syncope)', 'severity' => self::CRITICAL],
        ['min' => 41,   'max' => 50,   'alert' => 'Bradycardia', 'severity' => self::INFO],
        ['min' => 51,   'max' => 90,   'alert' => 'Normal HR', 'severity' => self::NONE],
        ['min' => 91,   'max' => 110,  'alert' => 'Mild Tachycardia (Check for pain/fever)', 'severity' => self::INFO],
        ['min' => 111,  'max' => 130,  'alert' => 'Tachycardia (Check for dehydration/infection)', 'severity' => self::WARNING],
        ['min' => 131,  'max' => null, 'alert' => 'Severe Tachycardia (Risk of shock)', 'severity' => self::CRITICAL],
    ];

    private $rrRules = [
        ['min' => null, 'max' => 8,    'alert' => 'Severe Bradypnea (Risk of resp. arrest)', 'severity' => self::CRITICAL],
        ['min' => 9,    'max' => 11,   'alert' => 'Bradypnea', 'severity' => self::INFO],
        ['min' => 12,   'max' => 20,   'alert' => 'Normal Respiration', 'severity' => self::NONE],
        ['min' => 21,   'max' => 24,   'alert' => 'Tachypnea (Possible resp. distress)', 'severity' => self::WARNING],
        ['min' => 25,   'max' => null, 'alert' => 'Severe Tachypnea (Risk of resp. failure)', 'severity' => self::CRITICAL],
    ];

    private $bpRules = [ // Systolic BP
        ['min' => null, 'max' => 90,   'alert' => 'Hypotension (Risk of shock)', 'severity' => self::CRITICAL],
        ['min' => 91,   'max' => 100,  'alert' => 'Low BP (Monitor)', 'severity' => self::WARNING],
        ['min' => 101,  'max' => 110,  'alert' => 'Low-Normal BP', 'severity' => self::INFO],
        ['min' => 111,  'max' => 139,  'alert' => 'Normal BP', 'severity' => self::NONE],
        ['min' => 140,  'max' => 159,  'alert' => 'Hypertension', 'severity' => self::WARNING],
        ['min' => 160,  'max' => null, 'alert' => 'Severe Hypertension (Risk of stroke)', 'severity' => self::CRITICAL],
    ];

    private $spo2Rules = [
        ['min' => null, 'max' => 91,   'alert' => 'Severe Hypoxemia (Apply O2)', 'severity' => self::CRITICAL],
        ['min' => 92,   'max' => 93,   'alert' => 'Moderate Hypoxemia', 'severity' => self::WARNING],
        ['min' => 94,   'max' => 95,   'alert' => 'Mild Hypoxemia', 'severity' => self::INFO],
        ['min' => 96,   'max' => null, 'alert' => 'Normal O2', 'severity' => self::NONE],
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

        if ($numericValue !== null) {
            foreach ($rules as $rule) {
                $minOk = is_null($rule['min']) || $numericValue >= $rule['min'];
                $maxOk = is_null($rule['max']) || $numericValue <= $rule['max'];
                if ($minOk && $maxOk) {
                    return ['alert' => $rule['alert'], 'severity' => $rule['severity']];
                }
            }
        }
        
        return ['alert' => 'Out of range', 'severity' => self::WARNING];
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

        if (empty($allAlerts)) {
            return ['alert' => 'Vitals stable.', 'severity' => self::NONE];
        }

        usort($allAlerts, function ($a, $b) {
            return $this->severityMap[$a['severity']] <=> $this->severityMap[$b['severity']];
        });
        
        $highestSeverity = $allAlerts[0]['severity']; 

        $alertStrings = array_map(fn($alert) => $alert['alert'], $allAlerts);


        $summary = implode('; ', $alertStrings);

        return [
            'alert' => $summary, 
            'severity' => $highestSeverity
        ];
    }
}