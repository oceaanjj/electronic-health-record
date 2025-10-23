<?php

namespace App\Services;

class LabValuesCdssService
{
    const CRITICAL = 'CRITICAL';
    const WARNING  = 'WARNING';
    const INFO     = 'INFO';
    const NONE     = 'NONE';

    // =========================================================================
    // EXPANDED RULES BASED ON PEDIATRIC REFERENCE RANGES
    // =========================================================================

    private $wbcRules = [
        // Neonate (0-30 days)
        ['ageGroup' => 'neonate', 'min' => 9.0, 'max' => 34.0, 'alert' => 'Normal WBC.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => null, 'max' => 8.9, 'alert' => 'Leukopenia (Neonate): Risk of sepsis.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'neonate', 'min' => 34.1, 'max' => null, 'alert' => 'Leukocytosis (Neonate): Possible infection/stress.', 'severity' => self::WARNING],
        // Infant (1mo - 2yr)
        ['ageGroup' => 'infant', 'min' => 6.0, 'max' => 17.5, 'alert' => 'Normal WBC.', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => null, 'max' => 5.9, 'alert' => 'Leukopenia: Risk of infection.', 'severity' => self::WARNING],
        ['ageGroup' => 'infant', 'min' => 17.6, 'max' => null, 'alert' => 'Leukocytosis: Possible infection.', 'severity' => self::WARNING],
        // Child (2yr - 12yr)
        ['ageGroup' => 'child', 'min' => 4.0, 'max' => 15.5, 'alert' => 'Normal WBC.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 3.9, 'alert' => 'Leukopenia: Risk of infection.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'child', 'min' => 15.6, 'max' => null, 'alert' => 'Leukocytosis: Possible infection/inflammation.', 'severity' => self::WARNING],
         // Adolescent (12yr - 18yr)
        ['ageGroup' => 'adolescent', 'min' => 4.0, 'max' => 13.5, 'alert' => 'Normal WBC.', 'severity' => self::NONE],
        ['ageGroup' => 'adolescent', 'min' => null, 'max' => 3.9, 'alert' => 'Leukopenia: Risk of infection.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'adolescent', 'min' => 13.6, 'max' => null, 'alert' => 'Leukocytosis: Possible infection/inflammation.', 'severity' => self::WARNING],
    ];

    private $rbcRules = [
        // Neonate (0-30 days)
        ['ageGroup' => 'neonate', 'min' => 3.9, 'max' => 5.9, 'alert' => 'Normal RBC.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => null, 'max' => 3.8, 'alert' => 'Anemia (Neonate).', 'severity' => self::WARNING],
        ['ageGroup' => 'neonate', 'min' => 6.0, 'max' => null, 'alert' => 'Polycythemia (Neonate).', 'severity' => self::WARNING],
        // Infant (1mo - 2yr)
        ['ageGroup' => 'infant', 'min' => 3.0, 'max' => 5.3, 'alert' => 'Normal RBC.', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => null, 'max' => 2.9, 'alert' => 'Anemia: Possible iron deficiency.', 'severity' => self::WARNING],
        ['ageGroup' => 'infant', 'min' => 5.4, 'max' => null, 'alert' => 'Erythrocytosis: Possible dehydration.', 'severity' => self::INFO],
        // Child (2yr - 12yr)
        ['ageGroup' => 'child', 'min' => 3.9, 'max' => 5.3, 'alert' => 'Normal RBC.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 3.8, 'alert' => 'Anemia.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 5.4, 'max' => null, 'alert' => 'Erythrocytosis.', 'severity' => self::INFO],
         // Adolescent (12yr - 18yr)
        ['ageGroup' => 'adolescent', 'min' => 4.0, 'max' => 5.3, 'alert' => 'Normal RBC.', 'severity' => self::NONE],
        ['ageGroup' => 'adolescent', 'min' => null, 'max' => 3.9, 'alert' => 'Anemia.', 'severity' => self::WARNING],
        ['ageGroup' => 'adolescent', 'min' => 5.4, 'max' => null, 'alert' => 'Erythrocytosis.', 'severity' => self::INFO],
    ];

    private $hgbRules = [
        // Neonate (0-30 days)
        ['ageGroup' => 'neonate', 'min' => 13.5, 'max' => 24.0, 'alert' => 'Normal Hgb.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => null, 'max' => 13.4, 'alert' => 'Anemia (Neonate).', 'severity' => self::WARNING],
        ['ageGroup' => 'neonate', 'min' => 24.1, 'max' => null, 'alert' => 'High Hgb (Neonate).', 'severity' => self::WARNING],
        // Infant (1mo - 2yr)
        ['ageGroup' => 'infant', 'min' => 9.0, 'max' => 14.0, 'alert' => 'Normal Hgb.', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => null, 'max' => 8.9, 'alert' => 'Anemia: Check for iron deficiency.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'infant', 'min' => 14.1, 'max' => null, 'alert' => 'High Hgb.', 'severity' => self::INFO],
        // Child (2yr - 12yr)
        ['ageGroup' => 'child', 'min' => 11.5, 'max' => 15.5, 'alert' => 'Normal Hgb.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 11.4, 'alert' => 'Anemia.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 15.6, 'max' => null, 'alert' => 'High Hgb.', 'severity' => self::INFO],
         // Adolescent (12yr - 18yr)
        ['ageGroup' => 'adolescent', 'min' => 12.0, 'max' => 16.1, 'alert' => 'Normal Hgb.', 'severity' => self::NONE],
        ['ageGroup' => 'adolescent', 'min' => null, 'max' => 11.9, 'alert' => 'Anemia.', 'severity' => self::WARNING],
        ['ageGroup' => 'adolescent', 'min' => 16.2, 'max' => null, 'alert' => 'High Hgb.', 'severity' => self::INFO],
    ];

    private $hctRules = [
        // Neonate (0-30 days)
        ['ageGroup' => 'neonate', 'min' => 42, 'max' => 70, 'alert' => 'Normal Hct.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => null, 'max' => 41, 'alert' => 'Low Hct (Neonate).', 'severity' => self::WARNING],
        ['ageGroup' => 'neonate', 'min' => 71, 'max' => null, 'alert' => 'High Hct (Neonate).', 'severity' => self::WARNING],
        // Infant (1mo - 2yr)
        ['ageGroup' => 'infant', 'min' => 28, 'max' => 42, 'alert' => 'Normal Hct.', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => null, 'max' => 27, 'alert' => 'Low Hct: Anemia.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'infant', 'min' => 43, 'max' => null, 'alert' => 'High Hct.', 'severity' => self::INFO],
        // Child (2yr - 12yr)
        ['ageGroup' => 'child', 'min' => 33, 'max' => 45, 'alert' => 'Normal Hct.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 32, 'alert' => 'Low Hct: Anemia.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 46, 'max' => null, 'alert' => 'High Hct.', 'severity' => self::INFO],
         // Adolescent (12yr - 18yr)
        ['ageGroup' => 'adolescent', 'min' => 35, 'max' => 47, 'alert' => 'Normal Hct.', 'severity' => self::NONE],
        ['ageGroup' => 'adolescent', 'min' => null, 'max' => 34, 'alert' => 'Low Hct: Anemia.', 'severity' => self::WARNING],
        ['ageGroup' => 'adolescent', 'min' => 48, 'max' => null, 'alert' => 'High Hct.', 'severity' => self::INFO],
    ];

    private $plateletRules = [
        // Neonate (0-30 days)
        ['ageGroup' => 'neonate', 'min' => 84, 'max' => 478, 'alert' => 'Normal Platelet count.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => null, 'max' => 83, 'alert' => 'Thrombocytopenia (Neonate): Risk of bleeding.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'neonate', 'min' => 479, 'max' => null, 'alert' => 'Thrombocytosis (Neonate).', 'severity' => self::WARNING],
        // All other ages
        ['ageGroup' => 'infant', 'min' => 150, 'max' => 450, 'alert' => 'Normal Platelet count.', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => null, 'max' => 149, 'alert' => 'Thrombocytopenia: Risk of bleeding.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'infant', 'min' => 451, 'max' => null, 'alert' => 'Thrombocytosis: Possible inflammation/infection.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 150, 'max' => 450, 'alert' => 'Normal Platelet count.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 149, 'alert' => 'Thrombocytopenia: Risk of bleeding.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'child', 'min' => 451, 'max' => null, 'alert' => 'Thrombocytosis: Possible inflammation/infection.', 'severity' => self::WARNING],
        ['ageGroup' => 'adolescent', 'min' => 150, 'max' => 450, 'alert' => 'Normal Platelet count.', 'severity' => self::NONE],
        ['ageGroup' => 'adolescent', 'min' => null, 'max' => 149, 'alert' => 'Thrombocytopenia: Risk of bleeding.', 'severity' => self::CRITICAL],
        ['ageGroup' => 'adolescent', 'min' => 451, 'max' => null, 'alert' => 'Thrombocytosis: Possible inflammation/infection.', 'severity' => self::WARNING],
    ];
    
    // --- DIFFERENTIALS (Note: Physiologic Crossover) ---
    // Neonates: Neutrophil-dominant
    // Infants: Lymphocyte-dominant
    // Children: Return to Neutrophil-dominant

    private $neutrophilsRules = [
        // Neonate (0-30 days)
        ['ageGroup' => 'neonate', 'min' => 32, 'max' => 62, 'alert' => 'Normal neutrophil %.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => null, 'max' => 31, 'alert' => 'Neutropenia: Risk of infection.', 'severity' => self::WARNING],
        ['ageGroup' => 'neonate', 'min' => 63, 'max' => null, 'alert' => 'Neutrophilia: Possible infection/stress.', 'severity' => self::WARNING],
        // Infant (1mo - 2yr)
        ['ageGroup' => 'infant', 'min' => 15, 'max' => 35, 'alert' => 'Normal neutrophil % (Lymphocyte-dominant phase).', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => null, 'max' => 14, 'alert' => 'Neutropenia: Risk of infection.', 'severity' => self::WARNING],
        ['ageGroup' => 'infant', 'min' => 36, 'max' => null, 'alert' => 'Neutrophilia: Possible bacterial infection.', 'severity' => self::WARNING],
        // Child (2yr - 12yr)
        ['ageGroup' => 'child', 'min' => 23, 'max' => 54, 'alert' => 'Normal neutrophil %.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 22, 'alert' => 'Neutropenia: Risk of infection.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 55, 'max' => null, 'alert' => 'Neutrophilia: Possible bacterial infection.', 'severity' => self::WARNING],
         // Adolescent (12yr - 18yr)
        ['ageGroup' => 'adolescent', 'min' => 33, 'max' => 64, 'alert' => 'Normal neutrophil %.', 'severity' => self::NONE],
        ['ageGroup' => 'adolescent', 'min' => null, 'max' => 32, 'alert' => 'Neutropenia: Risk of infection.', 'severity' => self::WARNING],
        ['ageGroup' => 'adolescent', 'min' => 65, 'max' => null, 'alert' => 'Neutrophilia: Possible bacterial infection.', 'severity' => self::WARNING],
    ];

    private $lymphocytesRules = [
        // Neonate (0-30 days)
        ['ageGroup' => 'neonate', 'min' => 19, 'max' => 49, 'alert' => 'Normal lymphocyte %.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => null, 'max' => 18, 'alert' => 'Lymphopenia.', 'severity' => self::INFO],
        ['ageGroup' => 'neonate', 'min' => 50, 'max' => null, 'alert' => 'Lymphocytosis.', 'severity' => self::INFO],
        // Infant (1mo - 2yr)
        ['ageGroup' => 'infant', 'min' => 45, 'max' => 76, 'alert' => 'Normal lymphocyte % (Dominant cell type).', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => null, 'max' => 44, 'alert' => 'Lymphopenia: Risk of immunodeficiency.', 'severity' => self::WARNING],
        ['ageGroup' => 'infant', 'min' => 77, 'max' => null, 'alert' => 'Lymphocytosis: Possible viral infection.', 'severity' => self::WARNING],
        // Child (2yr - 12yr)
        ['ageGroup' => 'child', 'min' => 28, 'max' => 65, 'alert' => 'Normal lymphocyte %.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 27, 'alert' => 'Lymphopenia.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 66, 'max' => null, 'alert' => 'Lymphocytosis: Possible viral infection.', 'severity' => self::WARNING],
         // Adolescent (12yr - 18yr)
        ['ageGroup' => 'adolescent', 'min' => 25, 'max' => 48, 'alert' => 'Normal lymphocyte %.', 'severity' => self::NONE],
        ['ageGroup' => 'adolescent', 'min' => null, 'max' => 24, 'alert' => 'Lymphopenia.', 'severity' => self::WARNING],
        ['ageGroup' => 'adolescent', 'min' => 49, 'max' => null, 'alert' => 'Lymphocytosis: Possible viral infection.', 'severity' => self::WARNING],
    ];

    private $monocytesRules = [
        // All ages (range is fairly stable)
        ['ageGroup' => 'all', 'min' => 0, 'max' => 15, 'alert' => 'Normal monocyte %.', 'severity' => self::NONE],
        ['ageGroup' => 'all', 'min' => 16, 'max' => null, 'alert' => 'Monocytosis: Possible chronic infection/inflammation.', 'severity' => self::WARNING],
    ];

    private $eosinophilsRules = [
        // All ages
        ['ageGroup' => 'all', 'min' => 0, 'max' => 6, 'alert' => 'Normal eosinophil %.', 'severity' => self::NONE],
        ['ageGroup' => 'all', 'min' => 7, 'max' => null, 'alert' => 'Eosinophilia: Possible allergy, asthma, or parasite.', 'severity' => self::WARNING],
    ];

    private $basophilsRules = [
         // All ages
        ['ageGroup' => 'all', 'min' => 0, 'max' => 2, 'alert' => 'Normal basophil %.', 'severity' => self::NONE],
        ['ageGroup' => 'all', 'min' => 3, 'max' => null, 'alert' => 'Basophilia: Possible allergic reaction or chronic inflammation.', 'severity' => self::WARNING],
    ];

    // --- RBC INDICES ---

    private $mcvRules = [
        // Neonate (0-30 days)
        ['ageGroup' => 'neonate', 'min' => 88, 'max' => 123, 'alert' => 'Normal MCV (Macrocytic).', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => null, 'max' => 87, 'alert' => 'Microcytosis (Neonate).', 'severity' => self::WARNING],
        ['ageGroup' => 'neonate', 'min' => 124, 'max' => null, 'alert' => 'High MCV (Neonate).', 'severity' => self::INFO],
        // Infant (1mo - 2yr)
        ['ageGroup' => 'infant', 'min' => 70, 'max' => 88, 'alert' => 'Normal MCV (Physiologic microcytosis).', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => null, 'max' => 69, 'alert' => 'Microcytosis: Possible iron deficiency, thalassemia.', 'severity' => self::WARNING],
        ['ageGroup' => 'infant', 'min' => 89, 'max' => null, 'alert' => 'Macrocytosis: Possible B12/folate deficiency.', 'severity' => self::WARNING],
        // Child (2yr - 12yr)
        ['ageGroup' => 'child', 'min' => 75, 'max' => 95, 'alert' => 'Normal MCV.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 74, 'alert' => 'Microcytosis: Possible iron deficiency, thalassemia.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 96, 'max' => null, 'alert' => 'Macrocytosis: Possible B12/folate deficiency.', 'severity' => self::WARNING],
         // Adolescent (12yr - 18yr)
        ['ageGroup' => 'adolescent', 'min' => 78, 'max' => 98, 'alert' => 'Normal MCV.', 'severity' => self::NONE],
        ['ageGroup' => 'adolescent', 'min' => null, 'max' => 77, 'alert' => 'Microcytosis: Possible iron deficiency, thalassemia.', 'severity' => self::WARNING],
        ['ageGroup' => 'adolescent', 'min' => 99, 'max' => null, 'alert' => 'Macrocytosis: Possible B12/folate deficiency.', 'severity' => self::WARNING],
    ];

    private $mchRules = [
        // Neonate (0-30 days)
        ['ageGroup' => 'neonate', 'min' => 31, 'max' => 39, 'alert' => 'Normal MCH.', 'severity' => self::NONE],
        ['ageGroup' => 'neonate', 'min' => null, 'max' => 30, 'alert' => 'Low MCH (Neonate).', 'severity' => self::INFO],
        ['ageGroup' => 'neonate', 'min' => 40, 'max' => null, 'alert' => 'High MCH (Neonate).', 'severity' => self::INFO],
        // Infant (1mo - 2yr)
        ['ageGroup' => 'infant', 'min' => 23, 'max' => 31, 'alert' => 'Normal MCH.', 'severity' => self::NONE],
        ['ageGroup' => 'infant', 'min' => null, 'max' => 22, 'alert' => 'Hypochromia: Possible iron deficiency.', 'severity' => self::WARNING],
        ['ageGroup' => 'infant', 'min' => 32, 'max' => null, 'alert' => 'Hyperchromia.', 'severity' => self::INFO],
        // Child (2yr - 12yr)
        ['ageGroup' => 'child', 'min' => 24, 'max' => 33, 'alert' => 'Normal MCH.', 'severity' => self::NONE],
        ['ageGroup' => 'child', 'min' => null, 'max' => 23, 'alert' => 'Hypochromia: Possible iron deficiency.', 'severity' => self::WARNING],
        ['ageGroup' => 'child', 'min' => 34, 'max' => null, 'alert' => 'Hyperchromia.', 'severity' => self::INFO],
         // Adolescent (12yr - 18yr)
        ['ageGroup' => 'adolescent', 'min' => 25, 'max' => 35, 'alert' => 'Normal MCH.', 'severity' => self::NONE],
        ['ageGroup' => 'adolescent', 'min' => null, 'max' => 24, 'alert' => 'Hypochromia: Possible iron deficiency.', 'severity' => self::WARNING],
        ['ageGroup' => 'adolescent', 'min' => 36, 'max' => null, 'alert' => 'Hyperchromia.', 'severity' => self::INFO],
    ];

    private $mchcRules = [
        // All ages (range is very stable)
        ['ageGroup' => 'all', 'min' => 30, 'max' => 36, 'alert' => 'Normal MCHC.', 'severity' => self::NONE],
        ['ageGroup' => 'all', 'min' => null, 'max' => 29, 'alert' => 'Low MCHC: Hypochromic anemia, iron deficiency.', 'severity' => self::WARNING],
        ['ageGroup' => 'all', 'min' => 37, 'max' => null, 'alert' => 'High MCHC: Possible spherocytosis.', 'severity' => self::INFO],
    ];

    private $rdwRules = [
        // All ages (range is stable)
        ['ageGroup' => 'all', 'min' => 11.5, 'max' => 15.0, 'alert' => 'Normal RDW.', 'severity' => self::NONE],
        ['ageGroup' => 'all', 'min' => null, 'max' => 11.4, 'alert' => 'Low RDW (rare, often not significant).', 'severity' => self::INFO],
        ['ageGroup' => 'all', 'min' => 15.1, 'max' => null, 'alert' => 'High RDW (Anisocytosis): Possible iron deficiency or mixed anemia.', 'severity' => self::WARNING],
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
        
        $numericValue = (float)$value;

        foreach ($rules as $rule) {
            if ($rule['ageGroup'] !== 'all' && $rule['ageGroup'] !== $ageGroup) {
                continue;
            }

            $minOk = is_null($rule['min']) || $numericValue >= $rule['min'];
            $maxOk = is_null($rule['max']) || $numericValue <= $rule['max'];

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