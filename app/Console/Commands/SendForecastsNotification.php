<?php

namespace App\Console\Commands;

use App\Enums\ClosedOption;
use App\Enums\UserType;
use App\Mail\ForecastsNotification;
use App\Models\AgentAssignment;
use App\Models\Forecast;
use App\Models\User;
use Illuminate\Console\Command;

class SendForecastsNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forecasts:send-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for agents once it open to submit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = now()->subMonth();
        $year = $date->year;
        $month = $date->month;

        User::active()
            ->where('type', UserType::AGENT())
            ->with('agent_assignments.account')
            ->each(function (User $user) use ($year, $month){
                $accountsToNotify = [];

                foreach ($user->agent_assignments as $assignment) {
                    $count = Forecast::where([
                        ['year', $year],
                        ['month', $month],
                        ['account_id', $assignment->account_id],
                        ['company_id', $assignment->company_id],
                        ['department_id', $assignment->department_id],
                    ])->count();

                    if ($count > 0) {
                        $accountsToNotify[] = [
                            'account' => $assignment->account->name,
                            'count'   => $count,
                        ];
                    }

                }

                if (!empty($accountsToNotify)) {
                    \Mail::to($user->email)
                        ->queue(new ForecastsNotification($user, $year, $month, $accountsToNotify));
                }

            });
    }
}
