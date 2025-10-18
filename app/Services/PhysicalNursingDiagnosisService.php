<?php

namespace App\Services;

use App\Models\PhysicalExam;

class PhysicalNursingDiagnosisService
{
    /**
     * Generates a full DPIE recommendation based on Physical Exam findings.
     * This method acts as a rule engine, checking various fields for keywords.
     *
     * @param PhysicalExam $physicalExam The patient's physical exam data.
     * @return array The recommended DPIE data, or an empty array if no rule matches.
     */
    public function generateDPIE(PhysicalExam $physicalExam): array
    {
        // --- Rule Engine ---
        // The engine checks fields from the physical exam for specific keywords.
        // It returns the first matching rule it finds.

        // Rule 1: Neurological - Risk for Injury
        if ($this->hasKeywords($physicalExam->neurological, ['unconscious', 'confused', 'dizzy', 'lethargic', 'unsteady gait'])) {
            return [
                'diagnosis' => 'Risk for Injury related to altered mental status and impaired mobility.',
                'planning' => 'Patient will remain free from injury (e.g., falls, trauma) throughout the hospital stay.',
                'intervention' => 'Implement fall precautions: keep bed in low position, side rails up, and call bell within reach. Orient patient to time, place, and person as needed. Assist with ambulation.',
                'evaluation' => 'Patient remained free from any falls or injuries during the shift.',
            ];
        }

        // Rule 2: Cardiovascular - Breathing Difficulty
        if ($this->hasKeywords($physicalExam->cardiovascular, ['difficulty breathing', 'shortness of breath', 'dyspnea'])) {
            return [
                'diagnosis' => 'Ineffective Breathing Pattern related to respiratory distress.',
                'planning' => 'After 4 hours of nursing interventions, patient will demonstrate an effective breathing pattern with respiratory rate between 12-20 breaths/min.',
                'intervention' => 'Monitor vital signs, especially respiratory rate and O2 saturation. Position patient in high-Fowler\'s position. Administer supplemental oxygen as prescribed.',
                'evaluation' => 'After 4 hours, patient\'s respiratory rate stabilized at 18 breaths/min and O2 saturation was maintained at 97%.',
            ];
        }

        // Rule 3: Abdomen - Acute Pain
        if ($this->hasKeywords($physicalExam->abdomen_condition, ['tenderness', 'severe pain', 'guarding', 'cramping'])) {
            return [
                'diagnosis' => 'Acute Pain related to abdominal tissue inflammation or injury.',
                'planning' => 'Within 2 hours of intervention, patient will report a pain level reduction to 4/10 or less.',
                'intervention' => 'Assess pain characteristics (PQRST). Administer analgesics as ordered. Provide non-pharmacological comfort measures.',
                'evaluation' => 'After 2 hours, patient reported pain level is 3/10 and appears more comfortable.',
            ];
        }
        
        // Rule 4: General Appearance - Nutrition Imbalance
        if ($this->hasKeywords($physicalExam->general_appearance, ['malnourished', 'underweight', 'poor appetite', 'cachectic'])) {
            return [
                'diagnosis' => 'Imbalanced Nutrition: Less Than Body Requirements related to inadequate intake.',
                'planning' => 'Patient will consume at least 50% of all meals and demonstrate a stable weight within 72 hours.',
                'intervention' => 'Offer small, frequent, nutrient-dense meals. Monitor food intake and weight daily. Consult with a dietitian to develop a personalized meal plan.',
                'evaluation' => 'Patient consumed 75% of breakfast and lunch. Weight remains stable.',
            ];
        }
        
        // Rule 5: Extremities - Fluid Volume Excess
        if ($this->hasKeywords($physicalExam->extremities, ['edema', 'swelling', 'pitting edema', 'fluid retention'])) {
            return [
                'diagnosis' => 'Fluid Volume Excess related to compromised regulatory mechanisms.',
                'planning' => 'Patient will show a reduction in peripheral edema and maintain stable weight within 24 hours.',
                'intervention' => 'Monitor daily weights. Measure intake and output accurately. Administer diuretics as prescribed. Elevate edematous limbs.',
                'evaluation' => 'Patient\'s weight decreased by 1kg in 24 hours. Pitting edema in lower extremities reduced from +3 to +2.',
            ];
        }

        // Rule 6: Skin - Impaired Skin Integrity
        if ($this->hasKeywords($physicalExam->skin_condition, ['redness', 'lesion', 'wound', 'pressure sore', 'breakdown'])) {
             return [
                'diagnosis' => 'Impaired Skin Integrity related to pressure and immobility.',
                'planning' => 'Patient\'s wound will show no signs of infection and begin to show signs of healing within 48 hours.',
                'intervention' => 'Perform wound care as prescribed. Reposition patient every 2 hours. Assess skin condition during each shift, especially over bony prominences.',
                'evaluation' => 'After 48 hours, wound edges are clean and pink, with no purulent drainage noted.',
            ];
        }

        // If no rules match, return an empty array.
        return [];
    }

    /**
     * Helper function to check if a string contains any of the given keywords (case-insensitive).
     *
     * @param string|null $haystack The string to search within.
     * @param array $needles The list of keywords to search for.
     * @return bool
     */
    private function hasKeywords(?string $haystack, array $needles): bool
    {
        if ($haystack === null) {
            return false;
        }

        // Convert the input to lowercase for a case-insensitive search
        $lowerHaystack = strtolower($haystack);

        foreach ($needles as $needle) {
            if (str_contains($lowerHaystack, strtolower($needle))) {
                return true; // Return true on the first match
            }
        }

        return false;
    }
}