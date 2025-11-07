<div class="section">
    <h2 class="section-title">Developmental History</h2>
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
                <td>{{ $developmentalHistory->gross_motor ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Fine Motor</td>
                <td>{{ $developmentalHistory->fine_motor ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Language</td>
                <td>{{ $developmentalHistory->language ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Cognitive</td>
                <td>{{ $developmentalHistory->cognitive ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Social-Emotional</td>
                <td>{{ $developmentalHistory->social ?? 'N/A' }}</td>
            </tr>
        </tbody>
    </table>
</div>