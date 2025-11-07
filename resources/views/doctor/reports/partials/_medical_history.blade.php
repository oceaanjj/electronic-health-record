<div class="section">
    <h2 class="section-title">1. Medical History</h2>

    <h3>Present Illness</h3>
    @forelse($presentIllness as $item)
        @php
            $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
            $filteredAttributes = [];

            // 1. Prepare attributes (keeping only the first 6 for a 2x6 table)
            foreach ($item->getAttributes() as $column => $value) {
                if (!in_array($column, $excludedColumns)) {
                    $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                }
            }

            // 2. Select only the first 6 attributes to guarantee a 2x6 (6 column) structure
            $chunk = array_slice($filteredAttributes, 0, 6, true);
        @endphp

        @if(count($chunk) > 0)
            <div class="table-responsive">
                <table>
                    {{-- Row 1: Headers (6 Columns) --}}
                    <thead>
                        <tr>
                            @for($i = 0; $i < 6; $i++)
                                {{-- Display the label of the i-th attribute, or empty if less than 6 --}}
                                <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                            @endfor
                        </tr>
                    </thead>
                    {{-- Row 2: Data Values (6 Columns) --}}
                    <tbody>
                        <tr>
                            @for($i = 0; $i < 6; $i++)
                                {{-- Display the value of the i-th attribute, or empty if less than 6 --}}
                                <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            {{-- If the item existed but had no useful attributes --}}
            <p class="no-data">No Present Illness data available.</p>
        @endif

        @if(!$loop->last)
        <hr>@endif
    @empty
        <p class="no-data">No Present Illness data available.</p>
    @endforelse

    <!-- --- -->

    <h3>Past Medical / Surgical</h3>
    @forelse($pastMedicalSurgical as $item)
        @php
            $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
            $filteredAttributes = [];

            foreach ($item->getAttributes() as $column => $value) {
                if (!in_array($column, $excludedColumns)) {
                    $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                }
            }
            $chunk = array_slice($filteredAttributes, 0, 6, true);
        @endphp

        @if(count($chunk) > 0)
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            @for($i = 0; $i < 6; $i++)
                                <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @for($i = 0; $i < 6; $i++)
                                <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <p class="no-data">No Past Medical / Surgical data available.</p>
        @endif

        @if(!$loop->last)
        <hr>@endif
    @empty
        <p class="no-data">No Past Medical / Surgical data available.</p>
    @endforelse

    <!-- --- -->

    <h3>Known Conditions or Allergies</h3>
    @forelse($allergies as $item)
        @php
            $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
            $filteredAttributes = [];

            foreach ($item->getAttributes() as $column => $value) {
                if (!in_array($column, $excludedColumns)) {
                    $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                }
            }
            $chunk = array_slice($filteredAttributes, 0, 6, true);
        @endphp

        @if(count($chunk) > 0)
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            @for($i = 0; $i < 6; $i++)
                                <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @for($i = 0; $i < 6; $i++)
                                <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <p class="no-data">No Known Conditions or Allergies data available.</p>
        @endif

        @if(!$loop->last)
        <hr>@endif
    @empty
        <p class="no-data">No Known Conditions or Allergies data available.</p>
    @endforelse

    <!-- --- -->

    <h3>Vaccination</h3>
    @forelse($vaccination as $item)
        @php
            $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
            $filteredAttributes = [];

            foreach ($item->getAttributes() as $column => $value) {
                if (!in_array($column, $excludedColumns)) {
                    $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                }
            }
            $chunk = array_slice($filteredAttributes, 0, 6, true);
        @endphp

        @if(count($chunk) > 0)
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            @for($i = 0; $i < 6; $i++)
                                <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @for($i = 0; $i < 6; $i++)
                                <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <p class="no-data">No Vaccination data available.</p>
        @endif

        @if(!$loop->last)
        <hr>@endif
    @empty
        <p class="no-data">No Vaccination data available.</p>
    @endforelse
</div>