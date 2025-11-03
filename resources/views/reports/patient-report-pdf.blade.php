<!DOCTYPE html>
<html>
<head>
    <title>Patient Report</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        h1, h2, h3 { color: #333; }
        .section { margin-bottom: 30px; border: 1px solid #eee; padding: 15px; border-radius: 5px; }
        .section-title { background-color: #f9f9f9; padding: 10px; margin: -15px -15px 15px -15px; border-bottom: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .no-data { color: #777; font-style: italic; }
        .diagnostic-image { max-width: 100%; height: auto; margin-top: 10px; border: 1px solid #ddd; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <h1>Patient Report for {{ $patient->name }}</h1>

    <div class="section">
        <h2 class="section-title">1. Medical History</h2>
        <h3>Present Illness</h3>
        @if($presentIllness->isEmpty())
            <p class="no-data">No Present Illness data available.</p>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach($presentIllness->first()->getAttributes() as $column => $value)
                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($presentIllness as $item)
                        <tr>
                            @foreach($item->getAttributes() as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <h3>Past Medical / Surgical</h3>
        @if($pastMedicalSurgical->isEmpty())
            <p class="no-data">No Past Medical / Surgical data available.</p>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach($pastMedicalSurgical->first()->getAttributes() as $column => $value)
                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($pastMedicalSurgical as $item)
                        <tr>
                            @foreach($item->getAttributes() as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <h3>Known Conditions or Allergies</h3>
        @if($allergies->isEmpty())
            <p class="no-data">No Known Conditions or Allergies data available.</p>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach($allergies->first()->getAttributes() as $column => $value)
                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($allergies as $item)
                        <tr>
                            @foreach($item->getAttributes() as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <h3>Vaccination</h3>
        @if($vaccination->isEmpty())
            <p class="no-data">No Vaccination data available.</p>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach($vaccination->first()->getAttributes() as $column => $value)
                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($vaccination as $item)
                        <tr>
                            @foreach($item->getAttributes() as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">2. Physical Exam</h2>
        @if($physicalExam->isEmpty())
            <p class="no-data">No Physical Exam data available.</p>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach($physicalExam->first()->getAttributes() as $column => $value)
                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($physicalExam as $item)
                        <tr>
                            @foreach($item->getAttributes() as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">3. Vital Signs</h2>
        @if($vitals->isEmpty())
            <p class="no-data">No Vital Signs data available.</p>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach($vitals->first()->getAttributes() as $column => $value)
                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($vitals as $item)
                        <tr>
                            @foreach($item->getAttributes() as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">4. Intake and Output</h2>
        @if($intakeAndOutput->isEmpty())
            <p class="no-data">No Intake and Output data available.</p>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach($intakeAndOutput->first()->getAttributes() as $column => $value)
                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($intakeAndOutput as $item)
                        <tr>
                            @foreach($item->getAttributes() as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">5. Activities of Daily Living</h2>
        @if($actOfDailyLiving->isEmpty())
            <p class="no-data">No Activities of Daily Living data available.</p>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach($actOfDailyLiving->first()->getAttributes() as $column => $value)
                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($actOfDailyLiving as $item)
                        <tr>
                            @foreach($item->getAttributes() as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">6. Lab Values</h2>
        @if($labValues->isEmpty())
            <p class="no-data">No Lab Values data available.</p>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach($labValues->first()->getAttributes() as $column => $value)
                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($labValues as $item)
                        <tr>
                            @foreach($item->getAttributes() as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">7. Diagnostics</h2>
        @if($diagnostics->isEmpty())
            <p class="no-data">No Diagnostics data available.</p>
        @else
            @foreach($diagnostics as $item)
                <p><strong>Type:</strong> {{ $item->type }}</p>
                <p><strong>Original Name:</strong> {{ $item->original_name }}</p>
                @if($item->path)
                    <img src="{{ asset('storage/' . $item->path) }}" alt="Diagnostic Image" class="diagnostic-image">
                @else
                    <p class="no-data">No image available for this diagnostic entry.</p>
                @endif
                <hr>
            @endforeach
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">8. IV's & Lines</h2>
        @if($ivsAndLines->isEmpty())
            <p class="no-data">No IV's & Lines data available.</p>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach($ivsAndLines->first()->getAttributes() as $column => $value)
                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($ivsAndLines as $item)
                        <tr>
                            @foreach($item->getAttributes() as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">9. Medication Administration</h2>
        <p class="no-data">Medication Administration data is not available in this report.</p>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">10. Medication Reconciliation</h2>
        <h3>Patient's Current Medication (Upon Admission)</h3>
        @if($currentMedication->isEmpty())
            <p class="no-data">No Current Medication data available.</p>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach($currentMedication->first()->getAttributes() as $column => $value)
                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($currentMedication as $item)
                        <tr>
                            @foreach($item->getAttributes() as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <h3>Patient's Home Medication (If Any)</h3>
        @if($homeMedication->isEmpty())
            <p class="no-data">No Home Medication data available.</p>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach($homeMedication->first()->getAttributes() as $column => $value)
                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($homeMedication as $item)
                        <tr>
                            @foreach($item->getAttributes() as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <h3>Changes in Medication During Hospitalization</h3>
        @if($changesInMedication->isEmpty())
            <p class="no-data">No Changes in Medication data available.</p>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach($changesInMedication->first()->getAttributes() as $column => $value)
                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($changesInMedication as $item)
                        <tr>
                            @foreach($item->getAttributes() as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>