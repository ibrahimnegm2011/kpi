<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;

abstract class AgentController
{
    public function __construct()
    {
        if(! session()->has('selected_account')) {
            session()->put('selected_account', \Auth::user()->agentAccounts()->first()->id);
        }
    }
}
