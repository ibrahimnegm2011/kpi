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

    case COMPANY_DASHBOARD = 'company_dashboard';
    case COMPANY_KPIS = 'company_kpis';
    case COMPANY_SUBMITTED_KPIS = 'company_submitted_kpis';
    case COMPANY_OVERDUE_KPIS = 'company_overdue_kpis';

    public function icon()
    {
        return match ($this->name) {
            'DASHBOARD' => '<i class="fa fa-chart-line mr-3"></i>',
            'MASTER_TABLE' => '<i class="fas fa-crosshairs mr-3"></i>',
            'FORECASTS' => '<i class="fas fa-bullseye mr-3"></i>',
            'KPIS' => '<i class="fas fa-key mr-3"></i>',
            'KPIS_CATEGORIES' => '<i class="fas fa-list mr-3"></i>',
            'COMPANIES' => '<i class="fas fa-building mr-3"></i>',
            'DEPARTMENTS' => '<i class="fas fa-layer-group mr-3"></i>',
            'USERS' => '<i class="fas fa-users mr-3"></i>',

            'COMPANY_DASHBOARD' => '<i class="fas fa-chart-line mr-3"></i>',
            'COMPANY_KPIS' => '<i class="fas fa-key mr-3"></i>',
            'COMPANY_SUBMITTED_KPIS' => '<i class="fas fa-check-double mr-3"></i>',
            'COMPANY_OVERDUE_KPIS' => '<i class="fas fa-hourglass-end mr-3"></i>',
            default => ''
        };
    }

    public function title()
    {
        return match ($this->name) {
            'MASTER_TABLE' => 'Performance Report',
            'KPIS' => 'KPI Definitions',
            'COMPANY_KPIS' => 'KPIs',
            'COMPANY_SUBMITTED_KPIS' => 'Done KPIs',
            'COMPANY_OVERDUE_KPIS' => 'Overdue KPIs',
            default => Str::headline($this->value)
        };
    }

    public function route()
    {
        return match ($this->name) {
            'DASHBOARD' => 'home',
//            'COMPANY_DASHBOARD' => 'company.dashboard',
            'COMPANY_KPIS' => 'company_kpis',
            'COMPANY_SUBMITTED_KPIS' => 'submitted_kpis',
            'COMPANY_OVERDUE_KPIS' => 'overdue_kpis',
            default => Route::has($this->value.'.index') ? $this->value.'.index' : 'home'
        };
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
        if (Auth::user()->is_representative) {
            return self::representativesItems();
        }

        return [
            //            Menu::DASHBOARD,
            Menu::MASTER_TABLE,
            Menu::FORECASTS,
            Menu::KPIS,
            Menu::KPIS_CATEGORIES,
            Menu::COMPANIES,
            Menu::DEPARTMENTS,
            Menu::USERS,
        ];
    }

    public static function representativesItems()
    {
        return [
//            Menu::COMPANY_DASHBOARD,
            Menu::COMPANY_KPIS,
            Menu::COMPANY_OVERDUE_KPIS,
            Menu::COMPANY_SUBMITTED_KPIS,
        ];
    }
}
