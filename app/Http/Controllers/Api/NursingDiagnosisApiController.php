<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NursingDiagnosis;
use App\Services\NursingDiagnosisCdssService;
use Illuminate\Http\Request;

class NursingDiagnosisApiController extends Controller
{
    protected $cdssService;
    public function __construct(NursingDiagnosisCdssService $cdssService) { $this->cdssService = $cdssService; }

    public function updateStepWithCdss(Request $request, $id, $step, $component)
    {
        $nursingDiag = NursingDiagnosis::findOrFail($id);
        $finding = $request->input($step);
        $alert = null;
        if ($step === 'diagnosis') $alert = $this->cdssService->analyzeDiagnosis($component, $finding);
        elseif ($step === 'planning') $alert = $this->cdssService->analyzePlanning($component, $finding);
        elseif ($step === 'intervention') $alert = $this->cdssService->analyzeIntervention($component, $finding);
        elseif ($step === 'evaluation') $alert = $this->cdssService->analyzeEvaluation($component, $finding);

        $nursingDiag->update([$step => $finding, $step . '_alert' => $alert ? $alert->raw_message : 'No findings.']);
        return response()->json(['message' => ucfirst($step) . ' updated', 'data' => $nursingDiag]);
    }
}
