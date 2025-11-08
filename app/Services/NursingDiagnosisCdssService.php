<?php

namespace App\Services;

class NursingDiagnosisCdssService
{
    // --- HELPER FUNCTION ---
    private function createAlert($recommendations)
    {
        if (empty($recommendations)) {
            return null;
        }

        $messageHtml = '<ul class="list-disc list-inside text-left">';
        foreach ($recommendations as $rec) {
            $messageHtml .= '<li>' . htmlspecialchars($rec) . '</li>';
        }
        $messageHtml .= '</ul>';

        return (object) [
            'level' => 'recommendation', // Or 'warning', 'info'
            'message' => $messageHtml
        ];
    }

    /**
     * Analyzes Step 1: Diagnosis
     */
    public function analyzeDiagnosis($finding)
    {
        $findingLower = strtolower(trim($finding));
        $recommendations = [];

        if (str_contains($findingLower, 'pain')) {
            $recommendations[] = 'Recommend pain assessment (e.g., PQRST, 0-10 scale).';
        }
        if (str_contains($findingLower, 'risk for')) {
            $recommendations[] = 'Ensure preventative interventions are planned.';
        }
        if (str_contains($findingLower, 'impaired') && str_contains($findingLower, 'skin')) {
            $recommendations[] = 'Recommend Braden Scale assessment for skin integrity.';
        }
        if (str_contains($findingLower, 'fluid volume')) {
            $recommendations[] = 'Monitor Intake & Output (I&O) closely. Check daily weights.';
        }

        return $this->createAlert($recommendations);
    }

    /**
     * Analyzes Step 2: Planning
     */
    public function analyzePlanning($finding)
    {
        $findingLower = strtolower(trim($finding));
        $recommendations = [];

        if (!str_contains($findingLower, 'patient will')) {
            $recommendations[] = 'Goals are often patient-centered. Start with "Patient will...".';
        }
        if (str_contains($findingLower, 'smart')) {
            $recommendations[] = 'Ensure goals are Specific, Measurable, Achievable, Relevant, and Time-bound.';
        }
         if (str_contains($findingLower, 'understand')) {
            $recommendations[] = 'Avoid "understand" or "know". Use measurable verbs like "demonstrate", "state", or "list".';
        }

        return $this->createAlert($recommendations);
    }

    /**
     * Analyzes Step 3: Intervention
     */
    public function analyzeIntervention($finding)
    {
        $findingLower = strtolower(trim($finding));
        $recommendations = [];

        if (str_contains($findingLower, 'monitor')) {
            $recommendations[] = 'Specify frequency of monitoring (e.g., "Monitor vital signs q4h").';
        }
        if (str_contains($findingLower, 'assess')) {
            $recommendations[] = 'Be specific: "Assess lung sounds" instead of "Assess patient".';
        }
         if (str_contains($findingLower, 'educate')) {
            $recommendations[] = 'Include "teach-back" method to confirm understanding.';
        }

        return $this->createAlert($recommendations);
    }

    /**
     * Analyzes Step 4: Evaluation
     */
    public function analyzeEvaluation($finding)
    {
        $findingLower = strtolower(trim($finding));
        $recommendations = [];

        if (str_contains($findingLower, 'met')) {
            $recommendations[] = 'Ensure evaluation directly addresses the goal from the Planning step.';
        }
         if (str_contains($findingLower, 'not met')) {
            $recommendations[] = 'If goal is not met, a new plan/re-evaluation is needed. Document it.';
        }

        return $this->createAlert($recommendations);
    }
}