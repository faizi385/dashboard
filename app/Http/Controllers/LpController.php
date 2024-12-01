<?php
namespace App\Http\Controllers;

use App\Models\Lp;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Offer;
use App\Models\Product;
use App\Mail\LpFormMail;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use Illuminate\Http\Request;
use App\Traits\LPStatementTrait;
use App\Models\RetailerStatement;
use App\Exports\LpStatementExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\LPStatusChangeMail;
use App\Models\LpAddress;

class LpController extends Controller
{
    use LPStatementTrait;

    public function dashboard()
    {
        $lp = Lp::where('user_id', Auth::user()->id)->first();

        // Fetch all purchase data
        $purchases = CleanSheet::where('lp_id', $lp->id)->where('dqi_flag', 1)->get();

        // Get total purchases
        $totalPurchases = $purchases->sum('purchase'); // Assuming 'purchase' is the column name

        // Group by province and sum the purchases for each province
        $provincePurchases = $purchases->groupBy('province')->map(function ($items) {
            return $items->sum('purchase');
        });

        // Get the province names and corresponding purchase totals
        $provinces = $provincePurchases->keys()->toArray(); // Province names
        $purchaseData = $provincePurchases->values()->toArray(); // Total purchases for each province

        $date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-01');
        $dateOffer = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-01');
        $monthName = Carbon::parse($date)->format('F Y');
        // Get the total number of offers for a specific LP (based on $lp->id) and the previous month's offer date
        $totalOffersIds = Offer::where('lp_id', $lp->id) // Filter by LP ID
            ->where('offer_date', $dateOffer) // Filter by the date of the previous month
            ->pluck('id') // Get all offer IDs for the previous month
            ->toArray();

        // Get the IDs of offers that have been availed (mapped) by retailers in the previous month
        $availedOffersIds = RetailerStatement::select('offer_id')
            ->whereNotNull('offer_id')
            ->where('reconciliation_date', $date) // Filter by reconciliation date (previous month)
            ->distinct() // Ensure distinct offer IDs are counted
            ->pluck('offer_id') // Get all offer IDs that were availed
            ->toArray();

        // Use array_diff to get the unavailed offers by finding the difference between the two sets of offer IDs
        $totalUnmappedOffersIds = array_diff($totalOffersIds, $availedOffersIds);

        // Count the number of unavailed offers
        $totalUnmappedOffers = count($totalUnmappedOffersIds);

        // Prepare data for the retailer offer bar chart
        $availedOffers = count($availedOffersIds); // Availed offers count
        $availedOffersData = [$availedOffers, $totalUnmappedOffers]; // Data for chart: Availed vs Unavailed



        $provinceOffers = DB::table('offers')
            ->select('province_id', DB::raw('count(*) as total_offers'))
            ->where('lp_id', $lp->id)
            ->groupBy('province_id')
            ->pluck('total_offers', 'province_id');

        // Get province names for offers chart
        $offerProvinces = DB::table('provinces')
            ->whereIn('id', array_keys($provinceOffers->toArray()))
            ->pluck('name', 'id')
            ->toArray();

        // Convert province IDs to names and total offers for use in chart
        $offerProvinceLabels = array_values($offerProvinces); // Province names for offers
        $offerData = array_values($provinceOffers->toArray()); // Total offers for each province

        $topRetailersWithoutOffers = DB::table('top_retailers')
        ->where('lp_id', $lp->id)
        ->orderByDesc('total_purchase') // Order by total purchases
        ->limit(5) // Limit to the top 5
        ->get();

    // Fetch retailer names for this graph
    $retailerNamesWithoutOffers = $topRetailersWithoutOffers->map(function ($item) {
        $retailer = Retailer::select('first_name', 'last_name')->find($item->retailer_id);
        return $retailer ? $retailer->first_name . ' ' . $retailer->last_name : 'Unknown';
    })->toArray();

    $retailerPurchaseTotals = $topRetailersWithoutOffers->pluck('total_purchase')->toArray();


        // Fetch top retailers based on offer count
      // Fetch top retailers directly from the 'top_retailers_with_deals' view
$topRetailers = DB::table('top_retailers_with_deals')
->where('lp_id', $lp->id) // Filter by the current LP's ID
->orderByDesc('offer_count') // Already ordered in the view, but added for clarity
->take(5) // Limit to top 5 results
->get();

// Fetch retailer names by retrieving each retailer's first and last name based on the `retailer_id`
$retailerNames = $topRetailers->map(function ($item) {
$retailer = Retailer::select('first_name', 'last_name')->find($item->retailer_id);
return $retailer ? $retailer->first_name . ' ' . $retailer->last_name : 'Unknown';
})->toArray();

// Extract offer counts for the top retailers
$retailerOfferCounts = $topRetailers->pluck('offer_count')->toArray();


        // dd(   $retailerOfferCounts);

        // Fetch total number of distributors
        $totalDistributors = Retailer::where('lp_id', $lp->id)->count();

        // Fetch total number of carveouts
        $totalCarevouts = DB::table('carveouts')
            ->where('lp_id', $lp->id)
            ->whereNull('deleted_at') // Exclude soft-deleted records
            ->count();

        // Fetch total number of reports
        $totalReportsSubmitted = DB::table('reports')
        ->where('lp_id', $lp->id)
        ->whereNull('deleted_at')
        ->count();

        // Calculate total revenue
        $retailerStatements = RetailerStatement::where('lp_id', $lp->id)->get();
        $totalRevenue = $retailerStatements->sum(function ($statement) {
            return ((float)$statement->fee_per * (float)$statement->quantity * (float)$statement->unit_cost) / 100;
        });


        $availedRetailers =  RetailerStatement::where('lp_id', $lp->id)
            ->whereNotNull('offer_id')
            ->distinct('retailer_id')
            ->count('retailer_id');


        $nonAvailedRetailers = Retailer::where('lp_id', $lp->id)
            ->whereNotIn('id',  RetailerStatement::where('lp_id', $lp->id)->whereNotNull('offer_id')->pluck('retailer_id'))
            ->count();

            $totalDeals = Offer::where('lp_id', $lp->id)->count();

            $date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-01');
            $noDealProducts = DB::table('no_deal_products_view')
            ->where('lp_id', $lp->id)
            ->where('reconciliation_date', $date)
            ->orderByDesc('total_purchase')
            ->limit(5)                                          // Limit to the top 5 products
            ->get();


        return view('super_admin.lp.dashboard', compact(
            'purchases',
            'totalPurchases',
            'provinces',
            'purchaseData',
            'offerProvinceLabels',
            'offerData',
            'retailerNames',
            'retailerOfferCounts',
            'totalDistributors',
            'totalCarevouts',
            'totalReportsSubmitted',
            'totalRevenue',
            'totalOffersIds',
            'availedOffers',
            'totalUnmappedOffers',
            'availedRetailers',
            'nonAvailedRetailers' ,
            'totalDeals',
            'noDealProducts',
            'retailerNamesWithoutOffers',
            'retailerPurchaseTotals',
            'monthName'
        ));
    }



   public function exportLpStatement($lp_id,$date)
    {
        set_time_limit(900);
        $date = Carbon::parse($date)->startofMonth()->subMonth()->format('Y-m-d');
        $lp = Lp::where('id', $lp_id)->with('user')->first();
        $sortedCollection = $this->generateLpStatement($lp_id, $date);
        $lpName = $lp->name ?? 'LP_Name';
        $formattedDate = Carbon::parse($date)->format('M-Y') ?? 'Date';
        return Excel::download(
            new LpStatementExport(true, $sortedCollection),
            str_replace(' ', '_', trim($lpName)) . '-' . $formattedDate . '-Statement.xlsx'
        );
    }

    public function viewStatement($lp_id)
    {
        $lpId = $lp_id;
        $lp = LP::where('id',$lp_id)->first();
        $lpStatement = DB::table('retailer_statements')
            ->join('reports', 'retailer_statements.report_id', '=', 'reports.id')
            ->join(DB::raw('(SELECT lp_id, MAX(id) AS max_id
                    FROM lp_addresses
                    GROUP BY lp_id) latest_lp'), function ($join) {
                $join->on('retailer_statements.lp_id', '=', 'latest_lp.lp_id');
            })
            ->join('lp_addresses', function ($join) {
                $join->on('retailer_statements.lp_id', '=', 'lp_addresses.lp_id')
                    ->on('lp_addresses.id', '=', 'latest_lp.max_id');
            })
            ->leftJoin('provinces', function ($join) {
                $join->on('lp_addresses.province_id', '=', 'provinces.id');
            })
            ->where('retailer_statements.lp_id', $lpId)
            ->where('retailer_statements.flag', '0')
            ->select(
                DB::raw('YEAR(reports.date) as year'),
                DB::raw('MONTH(reports.date) as month'),
                DB::raw('SUM(retailer_statements.fee_in_dollar) as total'),
                DB::raw('ROUND(SUM(retailer_statements.fee_in_dollar * IFNULL(provinces.tax_value, 5) / 100), 2) as fee_in_dollar_with_tax'),
                DB::raw('ROUND(SUM(retailer_statements.fee_in_dollar + (retailer_statements.fee_in_dollar * IFNULL(provinces.tax_value, 5) / 100)), 2) as total_with_tax')
            )
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('super_admin.lp.statement', compact('lp','lpStatement'));
    }

    public function index()
    {
        // Clear the session variable when loading the offers index
        session()->forget('viewing_offers_from_lp_show');

        // Fetch LPs ordered by creation date, most recent first
        $lps = Lp::orderBy('created_at', 'desc')->get();

        $provinces = Province::where('status',1)->get();

        return view('super_admin.lp.index', compact('lps','provinces'));
    }


    public function show($id)
    {
        $lp = Lp::with('address')->findOrFail($id);

        $lps = Lp::all();

        $provinces = Province::where('status',1)->get();

        // Set a session variable to indicate that the user is viewing offers from LP show
        session(['viewing_offers_from_lp_show' => true]);

        return view('super_admin.lp.show', compact('lps','lp','provinces'));
    }

    public function create()
    {
        return view('super_admin.lp.create');
    }

    public function updateStatus(Request $request, $id)
{
    $lp = Lp::findOrFail($id); // Find the LP by ID

    $status = $request->input('status');
    if (!in_array($status, ['approved', 'rejected'])) {
        return redirect()->back()->with('error', 'Invalid status value');
    }

    // Update the LP status
    $lp->status = $status;
    $lp->save();

    // If approved, assign the 'LP' role to the associated user
    if ($status === 'approved') {
        $user = $lp->user; // Assuming there's a relationship between LP and User
        if ($user) {
            // Find the LP role by its original_name
            $role = Role::where('original_name', 'LP')->first();
            if ($role) {
                // Assign the role to the user
                $user->assignRole($role->name);
            } else {
                return redirect()->back()->with('error', 'Role not found.');
            }
        }
    }

    try {
        // Send status update email
        Mail::to($lp->primary_contact_email)->send(new LPStatusChangeMail($lp, $status));
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Supplier status updated but email could not be sent.');
    }

    // Redirect with success message
    return redirect()->route('lp.index')->with('toast_success', 'Supplier status updated and email sent successfully');
}






    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[^\d]+$/'], // Disallow numeric characters
            'dba' => 'required|string|max:255', // Custom error message for this field
            'primary_contact_email' => [
                'required',
                'email',
                'unique:users,email',
                'regex:/^[\w\.-]+@[\w\.-]+\.\w{2,4}$/', // Example regex for standard email formats
            ],
            'primary_contact_phone' => [
                'required',
                'regex:/^\+?[0-9\s\-\(\)]+$/',
                'max:20',
            ],
            'primary_contact_position' => ['required', 'string', 'max:255', 'regex:/^[^\d]+$/'], // Disallow numeric characters
            'password' => 'nullable|string|min:8',
        ], [
            // Custom error messages
            'dba.required' => 'The organization name field is required.',

        ]);


        // Split the name into first and last name
        $nameParts = explode(' ', $validatedData['name'], 2);
        $firstName = $nameParts[0]; // First part
        $lastName = isset($nameParts[1]) ? $nameParts[1] : ''; // Second part (if exists)

        // Create User for the LP
        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $validatedData['primary_contact_email'],
            'phone' => $validatedData['primary_contact_phone'],
            'password' => Hash::make($validatedData['password'] ?? 'defaultPassword'),
        ]);


        $lp = Lp::create(array_merge(
            $validatedData,
            [
                'user_id' => $user->id,
                'status' => 'requested' // Set the status to 'requested'
            ]
        ));

        // Send email notification
        Mail::to($validatedData['primary_contact_email'])->send(new LpFormMail($lp));

        return redirect()->route('lp.create')->with('success', 'Supplier created and email sent!');
    }



    public function completeForm($id)
    {
        $lp = Lp::findOrFail($id);
        $provinces = Province::all();  // Fetch all provinces
        return view('super_admin.lp.complete_form', compact('lp', 'provinces'));
    }

    public function submitCompleteForm(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'lp_id' => 'required|exists:lps,id',
            'name' => 'required|string|max:255',
            'dba' => 'nullable|string|max:255',
            'primary_contact_email' => 'required|string|email|max:255',
            'primary_contact_phone' => 'nullable|string|max:20',
            'primary_contact_position' => 'nullable|string|max:255',
            'password' => 'required|string|confirmed|min:8',
            'address.address' => 'required|string|max:255', // Single address field
            'address.postal_code' => 'nullable|string|max:20',
            'address.city' => 'required|string|max:255',
            'address.province' => 'nullable|exists:provinces,id', // Validate that the province exists in the provinces table by its ID
        ]);

        // Find the LP based on the provided ID
        $lp = Lp::findOrFail($request->lp_id);

        $user = User::updateOrCreate(
            ['email' => $validatedData['primary_contact_email']], // Unique identifier
            [
                'name' => $validatedData['name'],
                'phone' => $validatedData['primary_contact_phone'],
                'password' => Hash::make($validatedData['password']),
            ]
        );

        // Fetch the role by original name (Ensure the role exists)
        $role = Role::where('original_name', 'LP')->first(); // Adjust 'LP' if necessary
        if ($role) {
            // Assign role to the user
            $user->assignRole($role->name);
        } else {
            return redirect()->back()->with('error', 'Role not found.');
        }

        $lp->update([
            'name' => $validatedData['name'],
            'dba' => $validatedData['dba'],
            'primary_contact_email' => $validatedData['primary_contact_email'],
            'primary_contact_phone' => $validatedData['primary_contact_phone'],
            'primary_contact_position' => $validatedData['primary_contact_position'],
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        // Create or update the address with province
        $lp->address()->updateOrCreate(
            ['lp_id' => $lp->id],
            [
                'address' => $validatedData['address']['address'], // Single address field
                'postal_code' => $validatedData['address']['postal_code'],
                'city' => $validatedData['address']['city'],
                'province_id' => $validatedData['address']['province'], // Store the province ID
            ]
        );

        // Redirect to the login page with a success message
        return redirect()->route('login')->with('success', 'Supplier information completed successfully. Please log in.');
    }





    public function edit(Lp $lp)
    {
        $provinces = Province::all();
        $lpAddresses = LpAddress::where('lp_id', $lp->id)->get();
        return view('super_admin.lp.edit', compact('lp', 'provinces', 'lpAddresses'));
    }


    public function update(Request $request, Lp $lp)
    {
        // Define custom validation messages
        $customMessages = [
            'address.*.address.required' => 'Address is required',  // Custom message for address field
            'address.*.province.required' => 'Province is required',  // Custom message for province field
            'address.*.city.required' => 'City is required',  // Custom message for city field
        ];

        // Validate the incoming request data with custom messages
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'dba' => 'required|string|max:255',
            'primary_contact' => 'required|string|max:20',
            'primary_contact_email' => 'required|email|max:255',
            'primary_contact_position' => 'nullable|string|max:255',

            // Address validation with correct structure
            'address.*.address' => 'required|string|max:255',  // Ensure each address is provided
            'address.*.province' => 'required|exists:provinces,id',  // Validate province_id exists
            'address.*.city' => 'required|string|max:100',  // Ensure city is provided
        ], $customMessages); // Pass custom validation messages

        // Update LP record with validated data
        $lp->update([
            'name' => $validatedData['name'],
            'dba' => $validatedData['dba'],
            'primary_contact' => $validatedData['primary_contact'],
            'primary_contact_email' => $validatedData['primary_contact_email'],
            'primary_contact_position' => $validatedData['primary_contact_position'] ?? null,
        ]);

        // Update address information (if necessary)
        if (isset($request->address)) {
            foreach ($request->address as $index => $addressData) {
                // Update the address in the database for the LP
                $lp->address[$index]->update([
                    'address' => $addressData['address'],  // Store the address text
                    'province_id' => $addressData['province'],  // Store the province_id
                    'city' => $addressData['city'],  // Store the city
                ]);
            }
        }

        // Redirect to LP index
        return redirect()->route('lp.index')->with('toast_success', 'Supplier updated successfully.');
    }





    public function destroy(Lp $lp)
    {
        DB::beginTransaction();
        try {
            $deletedLp = LP::where('id', $lp->id)->first();
            $lp->delete();
            $afterDelete = LP::withTrashed()->where('id', $deletedLp->id)->first();
            User::where('id', $afterDelete->user_id)->update([
                'email' => $afterDelete->primary_contact_email
            ]);
            User::where('id', $afterDelete->user_id)->delete();

            DB::commit();
            return redirect()->route('lp.index')->with('toast_success', 'Supplier deleted successfully.');
        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('lp.index')->with('error', 'Something went wrong.');
        }
    }
}
