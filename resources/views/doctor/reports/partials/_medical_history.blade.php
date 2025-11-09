<div class="section">
    <h2 class="section-title">Medical History</h2>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Condition Name</th>
                    <th>Description</th>
                    <th>Medication</th>
                    <th>Dosage</th>
                    <th>Side Effect</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>

                {{-- 1. Present Illness Section --}}
                @forelse($presentIllness as $item)
                    <tr>
                        <td>Present Illness</td>
                        <td>{{$item->condition_name ?? '' }}</td>
                        <td>{{ $item->description ?? '' }}</td>
                        <td>{{ $item->medication ?? '' }}</td>
                        <td>{{ $item->dosage ?? '' }}</td>
                        <td>{{ $item->side_effect ?? '' }}</td>
                        <td>{{ $item->comment ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td>Present Illness</td>
                        <td colspan="6" class="no-data">No Present Illness data available.</td>
                    </tr>
                @endforelse

                {{-- 2. Past Medical / Surgical Section --}}
                @forelse($pastMedicalSurgical as $item)
                    <tr>
                        <td>Past Medical / Surgical</td>
                        <td>{{$item->condition_name ?? '' }}</td>
                        <td>{{ $item->description ?? '' }}</td>
                        <td>{{ $item->medication ?? '' }}</td>
                        <td>{{ $item->dosage ?? '' }}</td>
                        <td>{{ $item->side_effect ?? '' }}</td>
                        <td>{{ $item->comment ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td>Past Medical / Surgical</td>
                        <td colspan="6" class="no-data">No Past Medical / Surgical data available.</td>
                    </tr>
                @endforelse


                {{-- 3. Known Conditions or Allergies Section --}}
                @forelse($allergies as $item)
                    <tr>
                        <td>Known Conditions or Allergies</td>
                        <td>{{$item->condition_name ?? '' }}</td>
                        <td>{{ $item->description ?? '' }}</td>
                        <td>{{ $item->medication ?? '' }}</td>
                        <td>{{ $item->dosage ?? '' }}</td>
                        <td>{{ $item->side_effect ?? '' }}</td>
                        <td>{{ $item->comment ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td>Allergies</td>
                        <td colspan="6" class="no-data">No Known Conditions or Allergies data available.</td>
                    </tr>
                @endforelse

                {{-- 4. Vaccination Section --}}
                @forelse($vaccination as $item)
                    <tr>
                        <td>Vaccination</td>
                        <td>{{$item->condition_name ?? '' }}</td>
                        <td>{{ $item->description ?? '' }}</td>
                        <td>{{ $item->medication ?? '' }}</td>
                        <td>{{ $item->dosage ?? '' }}</td>
                        <td>{{ $item->side_effect ?? '' }}</td>
                        <td>{{ $item->comment ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td>Vaccination</td>
                        <td colspan="6" class="no-data">No Vaccination data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>