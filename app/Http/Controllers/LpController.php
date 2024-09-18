<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LPController extends Controller
{
    public function dashboard()
    {
        return view('lp.dashboard');
    }

}
