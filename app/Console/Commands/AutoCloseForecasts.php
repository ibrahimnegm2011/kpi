<?php

namespace App\Console\Commands;

use App\Enums\ClosedOption;
use App\Models\Forecast;
use Illuminate\Console\Command;

class AutoCloseForecasts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forecasts:close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close Forecasts that exceed the submission period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Forecast::where('auto_close_option', ClosedOption::MONTH_DAY)
            ->where('auto_close_day', now()->day)
            ->where('year', now()->year)
            ->where('month', now()->subMonth()->month)
            ->update(['is_closed' => true]);
    }
}
