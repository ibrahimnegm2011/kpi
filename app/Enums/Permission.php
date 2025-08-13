<?php

namespace App\Enums;

use App\Enums\Traits\EnumTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * @method static string DASHBOARD()
 * @method static string COMPANIES()
 * @method static string DEPARTMENTS()
 * @method static string KPIS()
 * @method static string KPIS_CATEGORIES()
 * @method static string FORECASTS()
 * @method static string MASTER_TABLE()
 * @method static string USERS()
 * @method static string ADMIN_ACCOUNTS()
 * @method static string ADMIN_USERS()
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
    case AGENTS = 'agents';

    case ADMIN_ACCOUNTS = 'admin_accounts';
    case ADMIN_USERS = 'admin_users';

    public function title()
    {
        return match ($this) {
            self::ADMIN_ACCOUNTS => 'Accounts',
            self::ADMIN_USERS => 'Users',

            self::MASTER_TABLE => 'Performance Report',
            self::DASHBOARD => 'Summary Dashboard',

            default => Str::headline($this->value)
        };
    }

    public static function adminPermissions()
    {
        return [
            self::ADMIN_ACCOUNTS,
            self::ADMIN_USERS,
        ];
    }

    public static function accountPermissions()
    {
        return [
            self::DASHBOARD,
            self::MASTER_TABLE,
            self::FORECASTS,
            self::KPIS,
            self::KPIS_CATEGORIES,
            self::COMPANIES,
            self::DEPARTMENTS,
            self::USERS,
            self::AGENTS,
        ];
    }

    public static function userHas(Permission $permission)
    {
        return Auth::user() && (Auth::user()->hasPermission($permission->value));
    }
}
