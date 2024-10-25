<?php

namespace App\Http\Controllers;

use App\Models\CarveoutLog;
use Illuminate\Http\Request;

class CarveoutLogController extends Controller
{
    public function index()
    {
        // Fetch carveout logs with related users and carveouts
        $carveoutLogs = CarveoutLog::with(['user', 'carveout.lp', 'carveout.retailer'])->get();

        return view('super_admin.carveouts.logs', compact('carveoutLogs'));
    }
}
