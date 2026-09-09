<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('reminders:send-due')
    ->dailyAt('09:00')
    ->withoutOverlapping(30)
    ->onOneServer();
