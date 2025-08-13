<?php

use App\Console\Commands\AutoCloseForecasts;
use App\Console\Commands\SendForecastsNotification;

Schedule::command(AutoCloseForecasts::class)->dailyAt('00:01');

Schedule::command(SendForecastsNotification::class)->monthlyOn(1, '07:00');

