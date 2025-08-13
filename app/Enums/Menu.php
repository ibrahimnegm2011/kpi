<?php

namespace App\Enums;

use App\Enums\Traits\EnumTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

enum Menu: string
{
    use EnumTrait;
    case DASHBOARD = 'dashboard';
    case COMPANIES = 'companies';
    case DEPARTMENTS = 'departments';
    case KPIS = 'kpis';
    case KPIS_CATEGORIES = 'categories';
    case FORECASTS = 'forecasts';
    case MASTER_TABLE = 'master';
    case USERS = 'users';
    case AGENTS = 'agents';

    case AGENT_DASHBOARD = 'agent_dashboard';
    case AGENT_KPIS = 'agent_kpis';
    case AGENT_SUBMITTED_KPIS = 'agent_submitted_kpis';
    case AGENT_OVERDUE_KPIS = 'agent_overdue_kpis';

    case ADMIN_DASHBOARD = 'admin_dashboard';
    case ADMIN_ACCOUNTS = 'admin_accounts';
    case ADMIN_USERS = 'admin_users';

    public function icon()
    {
        return match ($this->name) {
            'DASHBOARD' => '<i class="fa fa-chart-line mr-3"></i>',
            'MASTER_TABLE' => '<i class="fas fa-crosshairs mr-3"></i>',
            'FORECASTS' => '<i class="fas fa-bullseye mr-3"></i>',
            'KPIS', 'COMPANY_KPIS' => '<i class="fas fa-key mr-3"></i>',
            'KPIS_CATEGORIES' => '<i class="fas fa-list mr-3"></i>',
            'COMPANIES' => '<i class="fas fa-building mr-3"></i>',
            'DEPARTMENTS' => '<i class="fas fa-layer-group mr-3"></i>',
            'USERS', 'ADMIN_USERS' => '<i class="fas fa-users mr-3"></i>',
            'AGENTS' => '<i class="fas fa-user-tie mr-3"></i>',

            'ADMIN_ACCOUNTS' => '<i class="fas fa-sitemap mr-3"></i>',

            'AGENT_DASHBOARD' => '<i class="fas fa-chart-line mr-3"></i>',
            'AGENT_KPIS' => '<i class="fas fa-key mr-3"></i>',
            'AGENT_SUBMITTED_KPIS' => '<i class="fas fa-check-double mr-3"></i>',
            'AGENT_OVERDUE_KPIS' => '<i class="fas fa-hourglass-end mr-3"></i>',
            default => ''
        };
    }

    public function title()
    {
        return match ($this->name) {
            'DASHBOARD' => 'Summary Dashboard',
            'MASTER_TABLE' => 'Performance Report',
            'KPIS' => 'KPI Definitions',

            'ADMIN_DASHBOARD' => 'Dashboard',
            'ADMIN_ACCOUNTS' => 'Accounts',
            'ADMIN_USERS' => 'Users',

            'AGENT_DASHBOARD' => 'Dashboard',
            'AGENT_KPIS' => 'Upcoming KPIs',
            'AGENT_SUBMITTED_KPIS' => 'Done KPIs',
            'AGENT_OVERDUE_KPIS' => 'Overdue KPIs',
            default => Str::headline($this->value)
        };
    }

    public function route()
    {
        return match ($this->name) {
            'USERS' => 'account.users.index',
            'AGENTS' => 'account.agents.index',
            'COMPANIES' => 'account.companies.index',
            'DEPARTMENTS' => 'account.departments.index',
            'KPIS_CATEGORIES' => 'account.categories.index',
            'KPIS' => 'account.kpis.index',
            'FORECASTS' => 'account.forecasts.index',
            'MASTER_TABLE' => 'account.master.index',
            'DASHBOARD' => 'account.dashboard',

            'ADMIN_DASHBOARD' => 'admin.home',
            'ADMIN_ACCOUNTS' => 'admin.accounts.index',
            'ADMIN_USERS' => 'admin.users.index',

            'AGENT_DASHBOARD' => 'agent.home',
            'AGENT_KPIS' => 'agent.kpis',
            'AGENT_SUBMITTED_KPIS' => 'agent.submitted_kpis',
            'AGENT_OVERDUE_KPIS' => 'agent.overdue_kpis',

            default => Route::has($this->value.'.index') ? $this->value.'.index' : 'home'
        };
    }

    public function isActive()
    {
        if (in_array($this, $this->adminItems())) {
            $segments = explode('.', $this->route());

            return $segments[0] == 'admin' && str_contains(request()->route()->getName(), $segments[1]);
        }

        if (in_array($this, $this->agentItems())) {
            $segments = explode('.', $this->route());

            return $segments[0] == 'agent' && request()->route()->getName() === $this->route();
        }

        if (in_array($this, $this->accountItems())) {
            $segments = explode('.', $this->route());

            return $segments[0] == 'account' && str_contains(request()->route()->getName(), $segments[1]);
        }

        return str_starts_with(request()->route()->getName(), explode('.', $this->route())[0]);
    }

    public function isGroup()
    {
        return false;
    }

    public function groupItems()
    {
        return null;
    }

    public static function items()
    {
        return match (Auth::user()->type) {
            UserType::ADMIN => self::adminItems(),
            UserType::ACCOUNT => self::accountItems(),
            UserType::AGENT => self::agentItems(),
            default => [],
        };
    }

    public static function agentItems()
    {
        return [
            Menu::AGENT_DASHBOARD,
            Menu::AGENT_KPIS,
            Menu::AGENT_OVERDUE_KPIS,
            Menu::AGENT_SUBMITTED_KPIS,
        ];
    }

    public static function adminItems()
    {
        return [
            //            Menu::ADMIN_DASHBOARD,
            Menu::ADMIN_ACCOUNTS,
            Menu::ADMIN_USERS,
        ];
    }

    public static function accountItems()
    {
        return [
            Menu::DASHBOARD,
            Menu::MASTER_TABLE,
            Menu::FORECASTS,
            Menu::KPIS,
            Menu::KPIS_CATEGORIES,
            Menu::AGENTS,
            Menu::COMPANIES,
            Menu::DEPARTMENTS,
            Menu::USERS,
        ];
    }
}
