<?php

namespace App\Http\Controllers\Agent;

abstract class AgentController
{
    public function __construct()
    {
        if (! session()->has('selected_account')) {
            session()->put('selected_account', \Auth::user()->agentAccounts()->first()->id);
        }
    }
}
