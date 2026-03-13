@php
    $alertKey = ($component === 'physical-exam') ? 'physical-exam-alerts' : 
                (($component === 'vital-signs') ? 'vital-signs-alerts' : 
                (($component === 'lab-values') ? 'lab-values-alerts' : 
                (($component === 'intake-and-output') ? 'intake-and-output-alerts' : 
                (($component === 'adl') ? 'act-of-daily-living-alerts' : ''))));
                
    $alertData = session($alertKey)[$type] ?? null;
    $level = $alertData->level ?? 'INFO';
    $message = $alertData->message ?? null;
    $colorClass = ($level === 'CRITICAL') ? 'alert-red' : (($level === 'WARNING') ? 'alert-orange' : 'alert-green');
    $levelIcon = ($level === 'CRITICAL') ? 'error' : (($level === 'WARNING') ? 'warning' : 'info');
    $levelText = ($level === 'CRITICAL') ? 'Critical Alert' : (($level === 'WARNING') ? 'Warning' : 'Clinical Decision Support');
    $levelIconColor = ($level === 'CRITICAL') ? '#ef4444' : (($level === 'WARNING') ? '#f59e0b' : '#10b981');
@endphp

<div id="no-recommendation-{{ $type }}" class="recommendation-banner alert-info {{ $message ? 'hidden' : '' }}">
    <div class="banner-content">
        <div class="banner-icon"><span class="material-symbols-outlined">edit_note</span></div>
        <div class="banner-text">
            <div class="banner-title">No Recommendations Yet</div>
            <div class="banner-subtitle">Type more details in the diagnosis field to receive clinical recommendations
            </div>
        </div>
    </div>
</div>

<div id="recommendation-{{ $type }}" class="recommendation-banner {{ $colorClass }} {{ $message ? '' : 'hidden' }}"
    data-level-text="{{ $levelText }}"
    data-level-icon="{{ $levelIcon }}"
    data-level-icon-color="{{ $levelIconColor }}"
    onclick="openRecommendationModal(this)">
    <div class="banner-content">
        <div class="banner-icon"><span class="material-symbols-outlined">{{ $levelIcon }}</span></div>
        <div class="banner-text">
            <div class="banner-title">{{ $levelText }}</div>
            <div class="banner-subtitle" data-full-message="{{ $message }}">
                {{ Str::limit(strip_tags($message), 80) }}
            </div>
        </div>
    </div>
    <div class="banner-action"><span>View Details</span><span class="material-symbols-outlined">arrow_forward</span></div>
</div>