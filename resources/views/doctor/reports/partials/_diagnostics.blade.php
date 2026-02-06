<div class="page-break"></div>

<div class="section">
    <h2 class="section-title">Diagnostics</h2>

    @if ($diagnostics->isEmpty())
        <p class="no-data">No Diagnostics data available.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($diagnostics as $item)
                    @php
                        $imageHtml = 'Image not found: ' . $item->path;

                        if (isset($item->path)) {
                            $imagePath = storage_path('app/public/' . $item->path);

                            if (file_exists($imagePath)) {
                                $imageData = base64_encode(file_get_contents($imagePath));
                                $imageMimeType = mime_content_type($imagePath);

                                // Base64 Data URL for DomPDF/Web Embedding
                                $imageHtml = '<img src="data:' . $imageMimeType . ';base64,' . $imageData . '" style="max-width: 50%; height: auto; margin:auto;">';
                            }
                        }
                    @endphp

                    <tr>
                        <td>{{ strtoupper(str_replace('_', ' ', $item->type)) ?? 'N/A' }}</td>
                        <td style="text-align: center">{!! $imageHtml !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
