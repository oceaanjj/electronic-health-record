<div class="page-break"></div>

<div class="section">
    <h2 class="section-title">IV's & Lines</h2>

    @if($ivsAndLines->isEmpty())
        <p class="no-data">No IV's & Lines data available.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>IV Fluid</th>
                    <th>Rate</th>
                    <th>Site</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ivsAndLines as $item)
                    <tr>
                        <td>{{ $item->iv_fluid ?? '-' }}</td>
                        <td>{{ $item->rate ?? '-' }}</td>
                        <td>{{ $item->site ?? '-' }}</td>
                        <td>{{ $item->status ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>