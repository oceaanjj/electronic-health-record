<?php

namespace App\Http\Controllers\ADPIE;

use Illuminate\Http\Request;

/**
 * Interface for all ADPIE component logic.
 * This ensures the main controller can call any component
 * using the same set of methods.
 */
interface AdpieComponentInterface
{
    /**
     * Step 1: Show the Diagnosis form.
     * @param string $component The component name (e.g., 'physical-exam')
     * @param mixed $id The ID (e.g., physicalExamId or patientId)
     * @return \Illuminate\View\View
     */
    public function startDiagnosis(string $component, $id);

    /**
     * Step 1: Store the Diagnosis.
     * @param Request $request
     * @param string $component The component name
     * @param mixed $id The ID (e.g., physicalExamId or patientId)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeDiagnosis(Request $request, string $component, $id);

    /**
     * Step 2: Show the Planning form.
     * @param string $component The component name
     * @param int $nursingDiagnosisId
     * @return \Illuminate\View\View
     */
    public function showPlanning(string $component, $nursingDiagnosisId);

    /**
     * Step 2: Store the Planning.
     * @param Request $request
     * @param string $component The component name
     * @param int $nursingDiagnosisId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePlanning(Request $request, string $component, $nursingDiagnosisId);

    /**
     * Step 3: Show the Intervention form.
     * @param string $component The component name
     * @param int $nursingDiagnosisId
     * @return \Illuminate\View\View
     */
    public function showIntervention(string $component, $nursingDiagnosisId);

    /**
     * Step 3: Store the Intervention.
     * @param Request $request
     * @param string $component The component name
     * @param int $nursingDiagnosisId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeIntervention(Request $request, string $component, $nursingDiagnosisId);

    /**
     * Step 4: Show the Evaluation form.
     * @param string $component The component name
     * @param int $nursingDiagnosisId
     * @return \Illuminate\View\View
     */
    public function showEvaluation(string $component, $nursingDiagnosisId);

    /**
     * Step 4: Store the Evaluation.
     * @param Request $request
     * @param string $component The component name
     * @param int $nursingDiagnosisId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeEvaluation(Request $request, string $component, $nursingDiagnosisId);
}