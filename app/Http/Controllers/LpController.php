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
    
        // Fetch total offers by province
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
    
        $topRetailers = CleanSheet::select('retailer_id', DB::raw('COUNT(DISTINCT offer_id) as offer_count'))
            ->where('lp_id', $lp->id)
            ->groupBy('retailer_id')
            ->orderByDesc('offer_count')
            ->take(5)
            ->get();
    
        // Fetch retailer names by retrieving each retailer's first and last name based on the `retailer_id`
        $retailerNames = $topRetailers->map(function ($item) {
            $retailer = Retailer::select('first_name', 'last_name')->find($item->retailer_id);
            return $retailer ? $retailer->first_name . ' ' . $retailer->last_name : 'Unknown';
        })->toArray();
    
        $retailerOfferCounts = $topRetailers->pluck('offer_count')->toArray();
    
        // Fetch total number of distributors
        $totalDistributors = Retailer::where('lp_id', $lp->id)->count(); // Assuming Distributor model and lp_id relationship
        $totalCarevouts = DB::table('carveouts')->where('lp_id', $lp->id)->count(); 
        $totalReportsSubmitted = DB::table('reports')->where('lp_id', $lp->id)->count();
        // Return the data to the view
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
            'totalReportsSubmitted' // Pass this to the view
        ));
    }
    

    public function exportLpStatement($lp_id,$date)
    {
        set_time_limit(900);
        $date = '2024-10-01';

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
        // Retrieve the LP by its ID
        $lp = Lp::findOrFail($lp_id);

        // Retrieve the related retailer statements based on lp_id
        $statements = RetailerStatement::where('lp_id', $lp_id)->get();

        // Calculate the total fee sum
        $totalFeeSum = $statements->sum('total_fee');

        // Define province tax rates
        $taxRates = [
            'Alberta' => 0.05,
            'Ontario' => 0.03,
            'Manitoba' => 0.05,
            'British Columbia' => 0.05,
            'Saskatchewan' => 0.05
        ];

        // Calculate total fee with tax for each statement based on province
        $totalFeeWithTaxSum = 0;
        foreach ($statements as $statement) {
            $province = $statement->province;
            $taxRate = $taxRates[$province] ?? 0; // Default to 0% if no tax rate found for province

            // Calculate total fee with tax
            $totalFeeWithTaxSum += $statement->total_fee * (1 + $taxRate);
        }

        // Return the view with the necessary data
        return view('super_admin.lp.statement', compact('lp', 'totalFeeSum', 'totalFeeWithTaxSum', 'statements'));
    }

    public function index()
    {
        // Clear the session variable when loading the offers index
        session()->forget('viewing_offers_from_lp_show');

        // Fetch LPs ordered by creation date, most recent first
        $lps = Lp::orderBy('created_at', 'desc')->get();

        return view('super_admin.lp.index', compact('lps'));
    }


    public function show($id)
    {
        $lp = Lp::with('address')->findOrFail($id);

        $lps = Lp::all();
        // Set a session variable to indicate that the user is viewing offers from LP show
        session(['viewing_offers_from_lp_show' => true]);

        return view('super_admin.lp.show', compact('lps','lp'));
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
        return redirect()->back()->with('error', 'LP status updated but email could not be sent.');
    }

    // Redirect with success message
    return redirect()->route('lp.index')->with('toast_success', 'LP status updated and email sent successfully');
}






    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[^\d]+$/'], // Disallow numeric characters
            'dba' => 'required|string|max:255',
            'primary_contact_email' => [
                'required',
                'email',
                'unique:users,email',
                'regex:/^[\w\.-]+@[\w\.-]+\.\w{2,4}$/'  // Example regex for standard email formats
            ],
         'primary_contact_phone' => [
    'required',
    'regex:/^\+?[0-9\s\-\(\)]+$/',
    'max:20'
],

            'primary_contact_position' => ['required', 'string', 'max:255', 'regex:/^[^\d]+$/'], // Disallow numeric characters
            'password' => 'nullable|string|min:8',
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

        return redirect()->route('lp.create')->with('success', 'LP created and email sent!');
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
            'address.street_number' => 'nullable|string|max:50',
            'address.street_name' => 'nullable|string|max:255',
            'address.postal_code' => 'nullable|string|max:20',
            'address.city' => 'required|string|max:255',
            'address.province' => 'nullable|exists:provinces,id',  // Validate that the province exists in the provinces table by its ID
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
            'status' => 'pending'
        ]);

        // Create or update the address with province
        $lp->address()->updateOrCreate(
            ['lp_id' => $lp->id],
            [
                'street_number' => $validatedData['address']['street_number'],
                'street_name' => $validatedData['address']['street_name'],
                'postal_code' => $validatedData['address']['postal_code'],
                'city' => $validatedData['address']['city'],
                'province_id' => $validatedData['address']['province'], // Store the province ID
            ]
        );

        // Redirect to the login page with a success message
        return redirect()->route('login')->with('success', 'LP information completed successfully. Please log in.');
    }




    public function edit(Lp $lp)
    {
        $provinces = Province::all(); 
        return view('super_admin.lp.edit', compact('lp', 'provinces'));
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
        return redirect()->route('lp.index')->with('toast_success', 'LP updated successfully.');
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
            return redirect()->route('lp.index')->with('toast_success', 'LP deleted successfully.');
        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('lp.index')->with('error', 'Something went wrong.');
        }
    }
}
