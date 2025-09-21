<?php

namespace App\Services;

class LabValuesCdssService
{

    public function analyzeLabValues(array $labData): array
    {
        $alerts = [];

        // Define pediatric normal ranges (These are general ranges, actual ranges may vary)
        $normalRanges = [
            'wbc' => ['min' => 4.5, 'max' => 13.5, 'unit' => '×10⁹/L'],
            'rbc' => ['min' => 4.0, 'max' => 5.5, 'unit' => '×10¹²/L'],
            'hgb' => ['min' => 12.0, 'max' => 16.0, 'unit' => 'g/dL'],
            'hct' => ['min' => 36.0, 'max' => 48.0, 'unit' => '%'],
            'platelets' => ['min' => 150, 'max' => 450, 'unit' => '×10⁹/L'],
            'mcv' => ['min' => 80, 'max' => 95, 'unit' => 'fL'],
            'mch' => ['min' => 27, 'max' => 33, 'unit' => 'pg'],
            'mchc' => ['min' => 32, 'max' => 36, 'unit' => 'g/dL'],
            'rdw' => ['min' => 11.5, 'max' => 14.5, 'unit' => '%'],
            'neutrophils' => ['min' => 40.0, 'max' => 60.0, 'unit' => '%'],
            'lymphocytes' => ['min' => 20.0, 'max' => 40.0, 'unit' => '%'],
            'monocytes' => ['min' => 2.0, 'max' => 8.0, 'unit' => '%'],
            'eosinophils' => ['min' => 1.0, 'max' => 4.0, 'unit' => '%'],
            'basophils' => ['min' => 0.5, 'max' => 1.0, 'unit' => '%'],
        ];

        foreach ($normalRanges as $test => $range) {
            $resultKey = $test . '_result';
            $alertKey = $test . '_alerts';

            // Check if the result exists and is a valid number
            if (isset($labData[$resultKey]) && is_numeric($labData[$resultKey])) {
                $result = (float) $labData[$resultKey];
                $min = $range['min'];
                $max = $range['max'];
                $unit = $range['unit'];

                if ($result < $min) {
                    $alerts[$alertKey][] = ucfirst($test) . " result is low ($result $unit). Normal range: $min-$max $unit.";
                } elseif ($result > $max) {
                    $alerts[$alertKey][] = ucfirst($test) . " result is high ($result $unit). Normal range: $min-$max $unit.";
                }
            }
        }

        return $alerts;
    }
}
