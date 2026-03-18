<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('diagnostics:cleanup', function () {
    $this->info('Starting diagnostics cleanup...');
    
    $sevenDaysAgo = now()->subDays(7);
    
    $oldDeletedRecords = \App\Models\Diagnostic::where('original_name', 'like', 'deleted-%')
        ->where('updated_at', '<=', $sevenDaysAgo)
        ->get();
        
    $count = 0;
    foreach ($oldDeletedRecords as $record) {
        if ($record->path && \Illuminate\Support\Facades\Storage::disk('public')->exists($record->path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($record->path);
        }
        $record->delete();
        $count++;
    }
    
    $this->info("Cleanup finished. Deleted {$count} old records and files.");
})->purpose('Delete diagnostic images marked as deleted that are older than 7 days');
