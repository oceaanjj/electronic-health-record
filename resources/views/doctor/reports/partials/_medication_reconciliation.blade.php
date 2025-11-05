<div class="page-break"></div>

<div class="section">
    <h2 class="section-title">10. Medication Reconciliation</h2>
    <h3>Patient's Current Medication (Upon Admission)</h3>
    @if($currentMedication->isEmpty())
        <p class="no-data">No Current Medication data available.</p>
    @else
        @foreach($currentMedication as $item)
            @php
                $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                $filteredAttributes = [];
                foreach ($item->getAttributes() as $column => $value) {
                    if (!in_array($column, $excludedColumns)) {
                        $filteredAttributes[
                            ucfirst(
                                str_replace(['_', 'current', 'med'], ['', '', 'medication'], $column)
                            )

                        ] = $value;
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

    <h3>Patient's Home Medication (If Any)</h3>
    @if($homeMedication->isEmpty())
        <p class="no-data">No Home Medication data available.</p>
    @else
        @foreach($homeMedication as $item)
            @php
                $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                $filteredAttributes = [];
                foreach ($item->getAttributes() as $column => $value) {
                    if (!in_array($column, $excludedColumns)) {
                        $filteredAttributes[
                            ucfirst(
                                str_replace(['_', 'home', 'med'], ['', '', 'medication'], $column)
                            )

                        ] = $value;
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

    <h3>Changes in Medication During Hospitalization</h3>
    @if($changesInMedication->isEmpty())
        <p class="no-data">No Changes in Medication data available.</p>
    @else
        @foreach($changesInMedication as $item)
            @php
                $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                $filteredAttributes = [];
                foreach ($item->getAttributes() as $column => $value) {
                    if (!in_array($column, $excludedColumns)) {
                        $filteredAttributes[
                            ucfirst(
                                str_replace(['_', 'change', 'med'], ['', '', 'medication'], $column)
                            )

                        ] = $value;
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