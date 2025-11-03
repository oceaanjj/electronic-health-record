<!DOCTYPE html>
<html>
<head>
    <title>Patient Report</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; margin: 5mm 10mm; font-size: 10px; }
        h1 { color: #333; font-size: 18px; }
        h2 { color: #333; font-size: 14px; }
        h3 { color: #333; font-size: 12px; }
        .section { margin-bottom: 10px; border: 1px solid #eee; padding: 8px; border-radius: 5px; }
        .section-title { background-color: #f9f9f9; padding: 5px; margin: -8px -8px 8px -8px; border-bottom: 1px solid #eee; }
        .table-responsive { overflow-x: auto; }
                table {
                    width: 100%;
                    table-layout: fixed; /* Crucial for fixed column widths */
                    border-collapse: collapse;
                    margin-top: 5px;
                    max-width: 100%;
                }
        
                th,
                td {
                    border: 1px solid #ddd;
                    padding: 5px;
                    text-align: left;
                    word-break: break-word;
                    vertical-align: top;
                    width: 33.33%; /* Equal width for 3 columns */
                }
        th { background-color: #f2f2f2; }
        .no-data { color: #777; font-style: italic; }
        .diagnostic-image { max-width: 100%; height: auto; margin-top: 5px; border: 1px solid #ddd; }
        img { max-width: 100%; height: auto; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="patient-demographics">
        <h2>Patient Information</h2>
        <p><strong>Name:</strong> {{ $patient->name }}</p>
        <p><strong>Age:</strong> {{ $patient->age }}</p>
        <p><strong>Sex:</strong> {{ $patient->sex }}</p>
        <p><strong>Address:</strong> {{ $patient->address }}</p>
        <p><strong>Chief of Complaints:</strong> {{ $patient->chief_of_complaints }}</p>
        <p><strong>Room No:</strong> {{ $patient->room_no }}</p>
        <p><strong>Bed No:</strong> {{ $patient->bed_no }}</p>
    </div>

    <div class="section">
        <h2 class="section-title">1. Medical History</h2>
        <h3>Present Illness</h3>
        @if($presentIllness->isEmpty())
            <p class="no-data">No Present Illness data available.</p>
        @else
            @foreach($presentIllness as $item)
                {{-- ... content ... --}}
            @endforeach
        @endif

        <h3>Past Medical / Surgical</h3>
        @if($pastMedicalSurgical->isEmpty())
            <p class="no-data">No Past Medical / Surgical data available.</p>
        @else
            @foreach($pastMedicalSurgical as $item)
                {{-- ... content ... --}}
            @endforeach
        @endif

        <h3>Known Conditions or Allergies</h3>
        @if($allergies->isEmpty())
            <p class="no-data">No Known Conditions or Allergies data available.</p>
        @else
            @foreach($allergies as $item)
                {{-- ... content ... --}}
            @endforeach
        @endif

        <h3>Vaccination</h3>
        @if($vaccination->isEmpty())
            <p class="no-data">No Vaccination data available.</p>
        @else
            @foreach($vaccination as $item)
                {{-- ... content ... --}}
            @endforeach
        @endif
    </div>

    {{-- ... other sections ... --}}
</body>
</html>