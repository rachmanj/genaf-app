<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Auto-verify distributions pending for more than 7 days
Schedule::command('supplies:auto-verify-distributions')
    ->daily()
    ->at('02:00')
    ->description('Auto-verify supply distributions pending for more than 7 days');
