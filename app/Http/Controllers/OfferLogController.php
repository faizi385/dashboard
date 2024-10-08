<?php

namespace App\Http\Controllers;

use App\Models\OfferLog; // Ensure this model is defined
use Illuminate\Http\Request;

class OfferLogController extends Controller
{
    public function index()
    {
        // Fetch all offer logs, optionally paginate them
        $offerLogs = OfferLog::with(['user', 'offer.lp'])->latest()->get(); // Adjust based on your relationships

        return view('super_admin.offers.offer_logs', compact('offerLogs'));
    }
}
