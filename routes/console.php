<?php

use App\Console\Commands\AutoCloseForecasts;
use App\Console\Commands\SendForecastsNotification;

Schedule::command(AutoCloseForecasts::class)->dailyAt('01:00');

Schedule::command(SendForecastsNotification::class)->monthlyOn(1, '07:00');

