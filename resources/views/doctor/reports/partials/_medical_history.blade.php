<div class="section">
        <h2 class="section-title">1. Medical History</h2>
        <h3>Present Illness</h3>
        @if($presentIllness->isEmpty())
            <p class="no-data">No Present Illness data available.</p>
        @else
            @foreach($presentIllness as $item)
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach ($item->getAttributes() as $column => $value) {
                        if (!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
                @if(!$loop->last)
                <hr>@endif
            @endforeach
        @endif

        <h3>Past Medical / Surgical</h3>
        @if($pastMedicalSurgical->isEmpty())
            <p class="no-data">No Past Medical / Surgical data available.</p>
        @else
            @foreach($pastMedicalSurgical as $item)
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach ($item->getAttributes() as $column => $value) {
                        if (!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
                @if(!$loop->last)
                <hr>@endif
            @endforeach
        @endif

        <h3>Known Conditions or Allergies</h3>
        @if($allergies->isEmpty())
            <p class="no-data">No Known Conditions or Allergies data available.</p>
        @else
            @foreach($allergies as $item)
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach ($item->getAttributes() as $column => $value) {
                        if (!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
                @if(!$loop->last)
                <hr>@endif
            @endforeach
        @endif

        <h3>Vaccination</h3>
        @if($vaccination->isEmpty())
            <p class="no-data">No Vaccination data available.</p>
        @else
            @foreach($vaccination as $item)
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach ($item->getAttributes() as $column => $value) {
                        if (!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
                @if(!$loop->last)
                <hr>@endif
            @endforeach
        @endif
    </div>