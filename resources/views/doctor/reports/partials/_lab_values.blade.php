<div class="page-break"></div>

<div>
    <h2 class="section-title">Lab Values</h2>

    @if($labValues->isEmpty())
        <p>No Lab Values data available.</p>
    @else
        @foreach($labValues as $item)
            <table>
                <thead>
                    <tr>
                        <th>Lab Test</th>
                        <th>Result</th>
                        <th>Pediatric Normal Range</th>
                        <th>Alerts</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- White Blood Cell (WBC) --}}
                    <tr>
                        <td>WBC (×10⁹/L)</td>
                        <td>{{ $item->wbc_result ?? 'N/A' }}</td>
                        <td>{{ $item->wbc_normal_range ?? 'N/A' }}</td>
                        <td>{{ $item->wbc_alert ?? '' }}</td>
                    </tr>

                    {{-- Red Blood Cell (RBC) --}}
                    <tr>
                        <td>RBC (×10¹²/L)</td>
                        <td>{{ $item->rbc_result ?? 'N/A' }}</td>
                        <td>{{ $item->rbc_normal_range ?? 'N/A' }}</td>
                        <td>{{ $item->rbc_alert ?? '' }}</td>
                    </tr>

                    {{-- Hemoglobin (Hgb) --}}
                    <tr>
                        <td>Hgb (g/dL)</td>
                        <td>{{ $item->hgb_result ?? 'N/A' }}</td>
                        <td>{{ $item->hgb_normal_range ?? 'N/A' }}</td>
                        <td>{{ $item->hgb_alert ?? '' }}</td>
                    </tr>

                    {{-- Hematocrit (Hct) --}}
                    <tr>
                        <td>Hct (%)</td>
                        <td>{{ $item->hct_result ?? 'N/A' }}</td>
                        <td>{{ $item->hct_normal_range ?? 'N/A' }}</td>
                        <td>{{ $item->hct_alert ?? '' }}</td>
                    </tr>

                    {{-- Platelets --}}
                    <tr>
                        <td>Platelets (×10⁹/L)</td>
                        <td>{{ $item->platelets_result ?? 'N/A' }}</td>
                        <td>{{ $item->platelets_normal_range ?? 'N/A' }}</td>
                        <td>{{ $item->platelets_alert ?? '' }}</td>
                    </tr>

                    {{-- Mean Corpuscular Volume (MCV) --}}
                    <tr>
                        <td>MCV (fL)</td>
                        <td>{{ $item->mcv_result ?? 'N/A' }}</td>
                        <td>{{ $item->mcv_normal_range ?? 'N/A' }}</td>
                        <td>{{ $item->mcv_alert ?? '' }}</td>
                    </tr>

                    {{-- Mean Corpuscular Hemoglobin (MCH) --}}
                    <tr>
                        <td>MCH (pg)</td>
                        <td>{{ $item->mch_result ?? 'N/A' }}</td>
                        <td>{{ $item->mch_normal_range ?? 'N/A' }}</td>
                        <td>{{ $item->mch_alert ?? '' }}</td>
                    </tr>

                    {{-- Mean Corpuscular Hemoglobin Concentration (MCHC) --}}
                    <tr>
                        <td>MCHC (g/dL)</td>
                        <td>{{ $item->mchc_result ?? 'N/A' }}</td>
                        <td>{{ $item->mchc_normal_range ?? 'N/A' }}</td>
                        <td>{{ $item->mchc_alert ?? '' }}</td>
                    </tr>

                    {{-- Red Cell Distribution Width (RDW) --}}
                    <tr>
                        <td>RDW (%)</td>
                        <td>{{ $item->rdw_result ?? 'N/A' }}</td>
                        <td>{{ $item->rdw_normal_range ?? 'N/A' }}</td>
                        <td>{{ $item->rdw_alert ?? '' }}</td>
                    </tr>

                    {{-- Neutrophils --}}
                    <tr>
                        <td>Neutrophils (%)</td>
                        <td>{{ $item->neutrophils_result ?? 'N/A' }}</td>
                        <td>{{ $item->neutrophils_normal_range ?? 'N/A' }}</td>
                        <td>{{ $item->neutrophils_alert ?? '' }}</td>
                    </tr>

                    {{-- Lymphocytes --}}
                    <tr>
                        <td>Lymphocytes (%)</td>
                        <td>{{ $item->lymphocytes_result ?? 'N/A' }}</td>
                        <td>{{ $item->lymphocytes_normal_range ?? 'N/A' }}</td>
                        <td>{{ $item->lymphocytes_alert ?? '' }}</td>
                    </tr>

                    {{-- Monocytes --}}
                    <tr>
                        <td>Monocytes (%)</td>
                        <td>{{ $item->monocytes_result ?? 'N/A' }}</td>
                        <td>{{ $item->monocytes_normal_range ?? 'N/A' }}</td>
                        <td>{{ $item->monocytes_alert ?? '' }}</td>
                    </tr>

                    {{-- Eosinophils --}}
                    <tr>
                        <td>Eosinophils (%)</td>
                        <td>{{ $item->eosinophils_result ?? 'N/A' }}</td>
                        <td>{{ $item->eosinophils_normal_range ?? 'N/A' }}</td>
                        <td>{{ $item->eosinophils_alert ?? '' }}</td>
                    </tr>

                    {{-- Basophils --}}
                    <tr>
                        <td>Basophils (%)</td>
                        <td>{{ $item->basophils_result ?? 'N/A' }}</td>
                        <td>{{ $item->basophils_normal_range ?? 'N/A' }}</td>
                        <td>{{ $item->basophils_alert ?? '' }}</td>
                    </tr>
                </tbody>
            </table>
            @if(!$loop->last)
                <hr>
            @endif
        @endforeach
    @endif
</div>