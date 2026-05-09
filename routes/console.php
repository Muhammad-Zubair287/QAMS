<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('qams:assign-auto-zero')
    ->hourly()
    ->withoutOverlapping()
    ->name('qams-auto-zero-missed-assignments');
