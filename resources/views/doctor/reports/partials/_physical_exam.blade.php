<div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">2. Physical Exam</h2>
        @if($physicalExam->isEmpty())
            <p class="no-data">No Physical Exam data available.</p>
        @else
            @foreach($physicalExam as $item)
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