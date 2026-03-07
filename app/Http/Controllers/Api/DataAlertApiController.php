<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vitals;
use App\Models\PhysicalExam;
use App\Models\ActOfDailyLiving;
use App\Models\IntakeAndOutput;
use App\Models\LabValues;
use Illuminate\Http\Request;

class DataAlertApiController extends Controller
{
    /**
     * Get the latest alerts for each of the 5 assessment sections.
     *
     * @param int $patient_id
     */
    public function show($patient_id)
    {
        return response()->json([
            'vital_signs' => $this->getVitalsAlerts($patient_id),
            'physical_exam' => $this->getPhysicalExamAlerts($patient_id),
            'adl' => $this->getAdlAlerts($patient_id),
            'intake_and_output' => $this->getIntakeOutputAlerts($patient_id),
            'lab_values' => $this->getLabValuesAlerts($patient_id),
        ]);
    }

    /**
     * Get the latest alerts for a specific component.
     */
    public function showByComponent($component, $patient_id)
    {
        $alert = match($component) {
            'vital-signs' => $this->getVitalsAlerts($patient_id),
            'physical-exam' => $this->getPhysicalExamAlerts($patient_id),
            'adl' => $this->getAdlAlerts($patient_id),
            'intake-and-output' => $this->getIntakeOutputAlerts($patient_id),
            'lab-values' => $this->getLabValuesAlerts($patient_id),
            default => 'Invalid component'
        };

        return response()->json(['alert' => $alert]);
    }

    private function getVitalsAlerts($patient_id)
    {
        $vitals = Vitals::where('patient_id', $patient_id)->latest('date')->latest('time')->first();
        return $vitals ? ($vitals->alerts ?: 'No findings.') : 'No findings.';
    }

    private function getPhysicalExamAlerts($patient_id)
    {
        $record = PhysicalExam::where('patient_id', $patient_id)->latest()->first();
        return $this->formatPhysicalExamAlerts($record);
    }

    private function getAdlAlerts($patient_id)
    {
        $record = ActOfDailyLiving::where('patient_id', $patient_id)->latest('date')->first();
        return $this->formatAdlAlerts($record);
    }

    private function getIntakeOutputAlerts($patient_id)
    {
        $record = IntakeAndOutput::where('patient_id', $patient_id)->latest('day_no')->first();
        return $record ? ($record->alert ?: 'No findings.') : 'No findings.';
    }

    private function getLabValuesAlerts($patient_id)
    {
        $record = LabValues::where('patient_id', $patient_id)->latest()->first();
        return $this->formatLabValuesAlerts($record);
    }

    private function formatPhysicalExamAlerts($record)
    {
        if (!$record) return 'No findings.';
        $alerts = [];
        $fields = [
            'general_appearance_alert', 'skin_alert', 'eye_alert', 'oral_alert',
            'cardiovascular_alert', 'abdomen_alert', 'extremities_alert', 'neurological_alert'
        ];
        foreach ($fields as $field) {
            $val = $record->$field;
            if ($val && !in_array($val, ['No findings.', 'N/A'])) {
                $alerts[] = $val;
            }
        }
        return empty($alerts) ? 'No findings.' : implode('; ', array_unique($alerts));
    }

    private function formatAdlAlerts($record)
    {
        if (!$record) return 'No findings.';
        $alerts = [];
        $fields = [
            'mobility_alert', 'hygiene_alert', 'toileting_alert', 'feeding_alert',
            'hydration_alert', 'sleep_pattern_alert', 'pain_level_alert'
        ];
        foreach ($fields as $field) {
            $val = $record->$field;
            if ($val && !in_array($val, ['No findings.', 'N/A'])) {
                $alerts[] = $val;
            }
        }
        return empty($alerts) ? 'No findings.' : implode('; ', array_unique($alerts));
    }

    private function formatLabValuesAlerts($record)
    {
        if (!$record) return 'No findings.';
        $alerts = [];
        foreach ($record->getAttributes() as $key => $value) {
            if (str_ends_with($key, '_alert') && $value && !in_array($value, ['No findings.', 'N/A'])) {
                $alerts[] = $value;
            }
        }
        return empty($alerts) ? 'No findings.' : implode('; ', array_unique($alerts));
    }
}
