<?php

namespace App\Enums;

use App\Enums\Traits\EnumTrait;
use Illuminate\Support\Facades\Auth;

/**
 * @method static int DASHBOARD()
 * @method static int COMPANIES()
 * @method static int DEPARTMENTS()
 * @method static int KPIS()
 * @method static int KPIS_CATEGORIES()
 * @method static int FORECASTS()
 * @method static int MASTER_TABLE()
 * @method static int USERS()
 */
enum Permission: string
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

    public static function userHas(Permission $permission)
    {
        return Auth::user() && (Auth::user()->hasPermission($permission->value));
    }
}
