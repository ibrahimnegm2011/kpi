<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Account\AgentsController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Forecast;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DashboardController extends AgentController
{
    public function index()
    {
        return view('dashboard');
    }
}
