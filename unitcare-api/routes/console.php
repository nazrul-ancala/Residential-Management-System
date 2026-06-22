<?php

use App\Console\Commands\AutoCheckoutVisitors;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-checkout visitors still checked-in from previous days (runs 1 min past midnight)
Schedule::command(AutoCheckoutVisitors::class)->dailyAt('00:01');
