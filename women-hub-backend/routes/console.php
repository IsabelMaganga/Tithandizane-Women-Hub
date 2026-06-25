<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\MarkMissedSessions;
use Illuminate\Foundation\Inspiring;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('sessions:mark-missed')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();