<div class="page-break"></div>

<div>
    <h2 class="section-title">IV's & Lines</h2>

    @if($ivsAndLines->isEmpty())
        <p>No IV's & Lines data available.</p>
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
                        <td>{{ $item->iv_fluid ?? 'N/A' }}</td>
                        <td>{{ $item->rate ?? 'N/A' }}</td>
                        <td>{{ $item->site ?? 'N/A' }}</td>
                        <td>{{ $item->status ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>