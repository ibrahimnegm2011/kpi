<?php

namespace App\Http\Controllers\Agent;

class DashboardController extends AgentController
{
    public function index()
    {
        return view('dashboard');
    }
}
