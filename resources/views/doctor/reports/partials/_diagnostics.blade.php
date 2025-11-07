<div class="page-break"></div>

<div class="section">
    <h2 class="section-title">7. Diagnostics</h2>
    @if($diagnostics->isEmpty())
        <p class="no-data">No Diagnostics data available.</p>
    @else
        @foreach($diagnostics as $item)
            @php
                $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                $filteredAttributes = [];
                foreach ($item->getAttributes() as $column => $value) {
                    if (!in_array($column, $excludedColumns)) {
                        if ($column == 'path') {
                            $imagePath = storage_path('app/public/' . $item->path);
                            if (file_exists($imagePath)) {
                                $imageData = base64_encode(file_get_contents($imagePath));
                                $imageMimeType = mime_content_type($imagePath);
                                $filteredAttributes[
                                    ucfirst(
                                        str_replace(['_', 'original', 'path'], [' ', 'file', 'image'], $column)
                                    )] = '<img src="data:' . $imageMimeType . ';base64,' . $imageData . '" class="diagnostic-image">';
                            } else {
                                $filteredAttributes[
                                    ucfirst(
                                        str_replace(['_', 'original', 'path'], [' ', 'file', 'image'], $column)
                                    )] = 'Image not found: ' . $item->path;
                            }
                        } else {
                            $filteredAttributes[
                                ucfirst(
                                    str_replace(['_', 'original', 'path'], [' ', 'file', 'image'], $column)
                                )] = ucfirst(str_replace('_', ' ', $value));
                        }
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
                                    <td>{!! isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' !!}</td>
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