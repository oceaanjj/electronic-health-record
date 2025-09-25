<?php

namespace App\Services;

class LabValuesCdssService
{

    public function analyzeLabValues(array $labData): array
    {
        $alerts = [];

        // Define pediatric normal ranges (These are general ranges, actual ranges may vary)

        //     'pediatric_ranges' => [
//     'wbc' => [ 
//         '0-1y' => ['min' => 6.0, 'max' => 17.5, 'unit' => '×10⁹/L'],
//         '1-6y' => ['min' => 5.0, 'max' => 15.5, 'unit' => '×10⁹/L'],
//         '6-18y'=> ['min' => 4.5, 'max' => 13.5, 'unit' => '×10⁹/L'],
//     ],
//     'rbc' => [
//         '1-18y' => ['min' => 4.0, 'max' => 5.5, 'unit' => '×10¹²/L'],
//     ],
//     'hgb' => [
//         '0-1m'  => ['min' => 13.5, 'max' => 19.5, 'unit' => 'g/dL'],
//         '1-12m' => ['min' => 10.5, 'max' => 13.5, 'unit' => 'g/dL'],
//         '1-18y' => ['min' => 11.5, 'max' => 15.5, 'unit' => 'g/dL'],
//     ],
//     'hct' => [
//         '0-1m'  => ['min' => 42.0, 'max' => 60.0, 'unit' => '%'],
//         '1-12m' => ['min' => 33.0, 'max' => 39.0, 'unit' => '%'],
//         '1-18y' => ['min' => 35.0, 'max' => 45.0, 'unit' => '%'],
//     ],
//     'platelets' => [
//         '0-18y' => ['min' => 150, 'max' => 450, 'unit' => '×10⁹/L'],
//     ],
//     'mcv' => [
//         '0-1y'  => ['min' => 85, 'max' => 105, 'unit' => 'fL'],
//         '1-18y' => ['min' => 75, 'max' => 95, 'unit' => 'fL'],
//     ],
//     'mch' => [
//         '1-18y' => ['min' => 24, 'max' => 30, 'unit' => 'pg'],
//     ],
//     'mchc' => [
//         '1-18y' => ['min' => 32, 'max' => 36, 'unit' => 'g/dL'],
//     ],
//     'rdw' => [
//         '0-18y' => ['min' => 11.5, 'max' => 14.5, 'unit' => '%'],
//     ],
//     'neutrophils' => [
//         '1-6y'  => ['min' => 25.0, 'max' => 55.0, 'unit' => '%'],
//         '6-18y' => ['min' => 40.0, 'max' => 60.0, 'unit' => '%'],
//     ],
//     'lymphocytes' => [
//         '1-6y'  => ['min' => 35.0, 'max' => 65.0, 'unit' => '%'],
//         '6-18y' => ['min' => 25.0, 'max' => 45.0, 'unit' => '%'],
//     ],
//     'monocytes' => [
//         '0-18y' => ['min' => 2.0, 'max' => 10.0, 'unit' => '%'],
//     ],
//     'eosinophils' => [
//         '0-18y' => ['min' => 1.0, 'max' => 6.0, 'unit' => '%'],
//     ],
//     'basophils' => [
//         '0-18y' => ['min' => 0.0, 'max' => 1.0, 'unit' => '%'],
//     ],
// ];



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
