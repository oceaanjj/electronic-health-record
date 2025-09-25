<?php

namespace App\Services;

class LabValuesCdssService
{
    const CRITICAL = 'CRITICAL';
    const WARNING  = 'WARNING';
    const INFO     = 'INFO';
    const NONE     = 'NONE';

    private $wbcRules = [
        ['ageGroup' => 'child', 'min' => 5.0, 'max' => 14.9, 'alert' => 'Normal WBC.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 4.9, 'alert' => 'Leukopenia.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 15.0, 'max' => null, 'alert' => 'Leukocytosis.', 'severity' => self::WARNING],
    ];
    private $rbcRules = [
        ['ageGroup' => 'child', 'min' => 4.1, 'max' => 5.5, 'alert' => 'Normal RBC.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 4.0, 'alert' => 'Low RBC: Possible anemia.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 5.6, 'max' => null, 'alert' => 'High RBC: Possible polycythemia.', 'severity' => self::WARNING],
    ];
    private $hgbRules = [
        ['ageGroup' => 'child', 'min' => 11.5, 'max' => 15.5, 'alert' => 'Normal Hgb.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 11.4, 'alert' => 'Anemia.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 15.6, 'max' => null, 'alert' => 'High hemoglobin.', 'severity' => self::WARNING],
    ];
    private $hctRules = [
        ['ageGroup' => 'child', 'min' => 35, 'max' => 45, 'alert' => 'Normal Hct.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 34.9, 'alert' => 'Low Hct.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 45.1, 'max' => null, 'alert' => 'High Hct.', 'severity' => self::WARNING],
    ];
    private $plateletRules = [
        ['ageGroup' => 'all', 'min' => 150, 'max' => 450, 'alert' => 'Normal Platelet count.', 'severity' => self::NONE],
        ['ageGroup' => 'all', 'min' => null, 'max' => 149, 'alert' => 'Thrombocytopenia.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'all', 'min' => 451, 'max' => null, 'alert' => 'Thrombocytosis.', 'severity' => self::WARNING],
    ];
    private $neutrophilsRules = [
        ['ageGroup' => 'child', 'min' => 40, 'max' => 70, 'alert' => 'Normal neutrophil %.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 39, 'alert' => 'Neutropenia: infection risk.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 71, 'max' => null, 'alert' => 'Neutrophilia: possible bacterial infection.', 'severity' => self::WARNING],
    ];
    private $lymphocytesRules = [
        ['ageGroup' => 'child', 'min' => 20, 'max' => 50, 'alert' => 'Normal lymphocyte %.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 19, 'alert' => 'Lymphopenia.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 51, 'max' => null, 'alert' => 'Lymphocytosis: possible viral infection.', 'severity' => self::WARNING],
    ];
    private $monocytesRules = [
        ['ageGroup' => 'child', 'min' => 2, 'max' => 10, 'alert' => 'Normal monocyte %.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 1, 'alert' => 'Monocytopenia.', 'severity' => self::INFO],
        ['ageGroup' => 'child', 'min' => 11, 'max' => null, 'alert' => 'Monocytosis: possible chronic infection.', 'severity' => self::WARNING],
    ];
    private $eosinophilsRules = [
        ['ageGroup' => 'child', 'min' => 1, 'max' => 6, 'alert' => 'Normal eosinophil %.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 0, 'alert' => 'Eosinopenia (usually not significant).', 'severity' => self::INFO],
        ['ageGroup' => 'child', 'min' => 7, 'max' => null, 'alert' => 'Eosinophilia: possible allergy/parasite.', 'severity' => self::WARNING],
    ];
    private $basophilsRules = [
        ['ageGroup' => 'child', 'min' => 0, 'max' => 2, 'alert' => 'Normal basophil %.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => 3, 'max' => null, 'alert' => 'Basophilia: allergy or chronic inflammation.', 'severity' => self::WARNING],
    ];
    private $mcvRules = [
        ['ageGroup' => 'child', 'min' => 77, 'max' => 95, 'alert' => 'Normal MCV.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 76, 'alert' => 'Microcytosis: possible iron deficiency.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 96, 'max' => null, 'alert' => 'Macrocytosis: possible B12/folate deficiency.', 'severity' => self::WARNING],
    ];
    private $mchRules = [
        ['ageGroup' => 'child', 'min' => 25, 'max' => 33, 'alert' => 'Normal MCH.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 24, 'alert' => 'Low MCH: hypochromia.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 34, 'max' => null, 'alert' => 'High MCH.', 'severity' => self::INFO],
    ];
    private $mchcRules = [
        ['ageGroup' => 'child', 'min' => 32, 'max' => 36, 'alert' => 'Normal MCHC.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 31, 'alert' => 'Low MCHC: hypochromic anemia.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 37, 'max' => null, 'alert' => 'High MCHC: possible spherocytosis.', 'severity' => self::INFO],
    ];
    private $rdwRules = [
        ['ageGroup' => 'child', 'min' => 11.5, 'max' => 14.5, 'alert' => 'Normal RDW.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 11.4, 'alert' => 'Low RDW (rare, often not significant).', 'severity' => self::INFO],
        ['ageGroup' => 'child', 'min' => 14.6, 'max' => null, 'alert' => 'High RDW: anisocytosis, possible mixed anemia.', 'severity' => self::WARNING],
    ];


    public function checkLabResult($param, $value, $ageGroup)
    {
        $rules = match ($param) {
            'wbc'         => $this->wbcRules,
            'rbc'         => $this->rbcRules,
            'hgb'         => $this->hgbRules,
            'hct'         => $this->hctRules,
            'platelet'    => $this->plateletRules,
            'neutrophils' => $this->neutrophilsRules,
            'lymphocytes' => $this->lymphocytesRules,
            'monocytes'   => $this->monocytesRules,
            'eosinophils' => $this->eosinophilsRules,
            'basophils'   => $this->basophilsRules,
            'mcv'         => $this->mcvRules,
            'mch'         => $this->mchRules,
            'mchc'        => $this->mchcRules,
            'rdw'         => $this->rdwRules,
            default       => []
        };

        foreach ($rules as $rule) {
            if ($rule['ageGroup'] !== 'all' && $rule['ageGroup'] !== $ageGroup) {
                continue;
            }

            $minOk = is_null($rule['min']) || $value >= $rule['min'];
            $maxOk = is_null($rule['max']) || $value <= $rule['max'];

            if ($minOk && $maxOk) {
                return [
                    'alert' => $rule['alert'],
                    'severity' => $rule['severity']
                ];
            }
        }

        return ['alert' => 'No matching rule found.', 'severity' => self::INFO];
    }
}

        // ['min' => 30, 'max' => null, 'alert' => 'Severe leukocytosis in neonate: High risk of sepsis!', 'severity' => self::CRITICAL, 'ageGroup' => 'neonate'],
        // ['min' => 9, 'max' => 29, 'alert' => 'Normal WBC (neonate).', 'severity' => self::NONE, 'ageGroup' => 'neonate'],
        // ['min' => 7, 'max' => 8.9, 'alert' => 'Mild leukopenia (neonate).', 'severity' => self::WARNING, 'ageGroup' => 'neonate'],
        // ['min' => null, 'max' => 6.9, 'alert' => 'Severe leukopenia (neonate): Risk of infection!', 'severity' => self::CRITICAL, 'ageGroup' => 'neonate'],
        // // Infants
        // ['min' => 20, 'max' => null, 'alert' => 'High WBC (infant): Possible infection.', 'severity' => self::CRITICAL, 'ageGroup' => 'infant'],
        // ['min' => 6, 'max' => 19.9, 'alert' => 'Normal WBC (infant).', 'severity' => self::NONE, 'ageGroup' => 'infant'],
        // ['min' => 4, 'max' => 5.9, 'alert' => 'Mild leukopenia (infant).', 'severity' => self::WARNING, 'ageGroup' => 'infant'],
        // ['min' => null, 'max' => 3.9, 'alert' => 'Severe leukopenia (infant).', 'severity' => self::CRITICAL, 'ageGroup' => 'infant'],
        // // Child
        // ['min' => 15, 'max' => null, 'alert' => 'High WBC (child): Possible infection.', 'severity' => self::CRITICAL, 'ageGroup' => 'child'],
        // ['min' => 5, 'max' => 14.9, 'alert' => 'Normal WBC (child).', 'severity' => self::NONE, 'ageGroup' => 'child'],
        // ['min' => 3, 'max' => 4.9, 'alert' => 'Mild leukopenia (child).', 'severity' => self::WARNING, 'ageGroup' => 'child'],
        // ['min' => null, 'max' => 2.9, 'alert' => 'Severe leukopenia (child).', 'severity' => self::CRITICAL, 'ageGroup' => 'child'],