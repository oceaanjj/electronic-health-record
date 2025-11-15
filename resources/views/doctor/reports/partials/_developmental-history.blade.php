<div class="page-break"></div>

<div class="section">
    <h2 class="section-title">Developmental History</h2>
    @if($developmentalHistory)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Domain</th>
                    <th>Findings</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Gross Motor</td>
                    <td>{{ $developmentalHistory->gross_motor ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Fine Motor</td>
                    <td>{{ $developmentalHistory->fine_motor ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Language</td>
                    <td>{{ $developmentalHistory->language ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Cognitive</td>
                    <td>{{ $developmentalHistory->cognitive ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Social-Emotional</td>
                    <td>{{ $developmentalHistory->social ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <p class="no-data">No Developmental History data available.</p>
    @endif
</div>