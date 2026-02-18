@php
    $alertData = session('physical-exam-alerts')[$type] ?? null;
    $level = $alertData->level ?? 'INFO';
    $message = $alertData->message ?? null;
    $colorClass = ($level === 'CRITICAL') ? 'alert-red' : (($level === 'WARNING') ? 'alert-orange' : 'alert-green');
    $levelIcon = ($level === 'CRITICAL') ? 'error' : (($level === 'WARNING') ? 'warning' : 'info');
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
    onclick="openRecommendationModal(this)">
    <div class="banner-content">
        <div class="banner-icon"><span class="material-symbols-outlined">{{ $levelIcon }}</span></div>
        <div class="banner-text">
            <div class="banner-title">Clinical Support</div>
            <div class="banner-subtitle" data-full-message="{{ $message }}">
                {{ Str::limit(strip_tags($message), 80) }}
            </div>
        </div>
    </div>
    <div class="banner-action"><span>View</span><span class="material-symbols-outlined">arrow_forward</span></div>
</div>