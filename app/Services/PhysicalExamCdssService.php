<?php

namespace App\Services;

class PhysicalExamCdssService
{
    // Gagamit tayo ng severity levels for alerts, based on tis severity kung ano yung irereturn
    const CRITICAL = 'critical';
    const WARNING = 'warning';
    const INFO = 'info';

    private $generalAppearanceRules = [
        [
            'keywords' => ['unresponsive', 'severe distress', 'cyanotic'],
            'alert' => 'URGENT: Immediate resuscitation required. Call code blue.',
            'severity' => self::CRITICAL
        ],
        [
            'keywords' => ['lethargic', 'confused', 'disoriented'],
            'alert' => 'Monitor neurological status closely. Consider further evaluation.',
            'severity' => self::WARNING
        ]
    ];
    private $skinRules = [
        [
            'keywords' => ['cyanosis', 'blue lips', 'blue fingertips', 'bluish'],
            'alert' => 'URGENT: Check oxygen saturation immediately. Consider oxygen therapy.',
            'severity' => self::CRITICAL
        ],
        [
            'keywords' => ['jaundice', 'yellow skin', 'yellowing'],
            'alert' => 'Check bilirubin levels. Monitor for liver function.',
            'severity' => self::WARNING
        ]

    ];
    private $eyeRules = [
        [
            'keywords' => ['red eye', 'eye pain', 'vision loss', 'blurred vision'],
            'alert' => 'Urgent ophthalmology referral needed. Possible infection or acute glaucoma.',
            'severity' => self::CRITICAL
        ],
        [
            'keywords' => ['dry eyes', 'itchy eyes', 'watery eyes'],
            'alert' => 'Consider allergy management or lubricating eye drops.',
            'severity' => self::INFO
        ]
    ];
    private $oralRules = [
        [
            'keywords' => ['sores', 'ulcers', 'white patches', 'red patches'],
            'alert' => 'Evaluate for oral infections or malignancy. Consider biopsy if persistent.',
            'severity' => self::WARNING
        ],
        [
            'keywords' => ['bleeding gums', 'swollen gums', 'tooth pain'],
            'alert' => 'Recommend dental evaluation. Possible periodontal disease.',
            'severity' => self::INFO
        ]
    ];

    private $cardiovascularRules = [
        [
            'keywords' => ['murmur', 'irregular heartbeat', 'palpitations'],
            'alert' => 'Refer to cardiology for further evaluation.',
            'severity' => self::WARNING
        ],
        [
            'keywords' => ['edema', 'swelling legs', 'swollen ankles'],
            'alert' => 'Assess for heart failure. Monitor fluid status.',
            'severity' => self::WARNING
        ]
    ];

    private $abdomenRules = [
        [
            'keywords' => ['tenderness', 'guarding', 'rebound'],
            'alert' => 'Urgent surgical evaluation needed. Possible acute abdomen.',
            'severity' => self::CRITICAL
        ],
        [
            'keywords' => ['distension', 'bloating', 'nausea'],
            'alert' => 'Consider imaging to evaluate for obstruction or other pathology.',
            'severity' => self::WARNING
        ]
    ];
    private $extremitiesRules = [
        [
            'keywords' => ['swelling', 'redness', 'warmth', 'pain'],
            'alert' => 'Evaluate for deep vein thrombosis (DVT). Consider Doppler ultrasound.',
            'severity' => self::CRITICAL
        ],
        [
            'keywords' => ['joint pain', 'stiffness', 'limited range of motion'],
            'alert' => 'Consider rheumatology referral for possible arthritis.',
            'severity' => self::WARNING
        ]
    ];

    private $neurologicalRules = [
        [
            'keywords' => ['weakness', 'numbness', 'tingling', 'paralysis'],
            'alert' => 'Urgent neurological evaluation needed. Possible stroke or neuropathy.',
            'severity' => self::CRITICAL
        ],
        [
            'keywords' => ['dizziness', '   ', 'confusion'],
            'alert' => 'Monitor neurological status. Consider imaging if symptoms persist.',
            'severity' => self::WARNING
        ]
    ];


    public function analyzeFindings($findingsData)
    {
        $alerts = [];
        
        if (!empty($findingsData['skin_condition'])) {
            $skinAlert = $this->analyzeSkin($findingsData['skin_condition']);
            if ($skinAlert) {
                $alerts['skin_alerts'] = $skinAlert;
            }
        }
        if (!empty($findingsData['eye_condition'])) {
            $eyeAlert = $this->analyzeEye($findingsData['eye_condition']);
            if ($eyeAlert) {
                $alerts['eye_alerts'] = $eyeAlert;
            }
        }
        if (!empty($findingsData['oral_condition'])) {
            $oralAlert = $this->analyzeOral($findingsData['oral_condition']);
            if ($oralAlert) {
                $alerts['oral_alerts'] = $oralAlert;
            }
        }
        if (!empty($findingsData['cardiovascular'])) {
            $cardioAlert = $this->analyzeCardiovascular($findingsData['cardiovascular']);
            if ($cardioAlert) {
                $alerts['cardiovascular_alerts'] = $cardioAlert;
            }
        }
        if (!empty($findingsData['abdomen_condition'])) {
            $abdomenAlert = $this->analyzeAbdomen($findingsData['abdomen_condition']);
            if ($abdomenAlert) {
                $alerts['abdomen_alerts'] = $abdomenAlert;
            }
        }
        if (!empty($findingsData['extremities'])) {
            $extremitiesAlert = $this->analyzeExtremities($findingsData['extremities']);
            if ($extremitiesAlert) {
                $alerts['extremities_alerts'] = $extremitiesAlert;
            }
        }
        if (!empty($findingsData['neurological'])) {
            $neurologicalAlert = $this->analyzeNeurological($findingsData['neurological']);
            if ($neurologicalAlert) {
                $alerts['neurological_alerts'] = $neurologicalAlert;
            }
        }
        if (!empty($findingsData['general_appearance'])) {
            $generalAlert = $this->analyzeGeneralAppearance($findingsData['general_appearance']);
            if ($generalAlert) {
                $alerts['general_appearance_alerts'] = $generalAlert;
            }
        }




        return $alerts;
    }



    //Analysis functions for each system

    private function analyzeGeneralAppearance($findings)
    {
        foreach ($this->generalAppearanceRules as $rule) {
            if ($this->matchesKeywords($findings, $rule['keywords'])) {
                return $rule['alert'];
            }
        }
        return null;
    }
    private function analyzeSkin($findings)
    {
        foreach ($this->skinRules as $rule) {
            if ($this->matchesKeywords($findings, $rule['keywords'])) {
                return $rule['alert'];
            }
        }
        return null;
    }
    private function analyzeEye($findings)
    {
        foreach ($this->eyeRules as $rule) {
            if ($this->matchesKeywords($findings, $rule['keywords'])) {
                return $rule['alert'];
            }
        }
        return null;
    }
    private function analyzeOral($findings)
    {
        foreach ($this->oralRules as $rule) {
            if ($this->matchesKeywords($findings, $rule['keywords'])) {
                return $rule['alert'];
            }
        }
        return null;
    }
    private function analyzeCardiovascular($findings)
    {
        foreach ($this->cardiovascularRules as $rule) {
            if ($this->matchesKeywords($findings, $rule['keywords'])) {
                return $rule['alert'];
            }
        }
        return null;
    }
    private function analyzeAbdomen($findings)
    {
        foreach ($this->abdomenRules as $rule) {
            if ($this->matchesKeywords($findings, $rule['keywords'])) {
                return $rule['alert'];
            }
        }
        return null;
    }
    private function analyzeExtremities($findings)
    {
        foreach ($this->extremitiesRules as $rule) {
            if ($this->matchesKeywords($findings, $rule['keywords'])) {
                return $rule['alert'];
            }
        }
        return null;
    }
    private function analyzeNeurological($findings)
    {
        foreach ($this->neurologicalRules as $rule) {
            if ($this->matchesKeywords($findings, $rule['keywords'])) {
                return $rule['alert'];
            }
        }
        return null;
    }


    // Helper method for keyword matching
    private function matchesKeywords($text, $keywords)
    {
        $text = strtolower($text);
        foreach ($keywords as $keyword) {
            if (strpos($text, strtolower($keyword)) !== false) {
                return true;
            }
        }
        return false;
    }
}
