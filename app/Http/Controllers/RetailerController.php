<?php
namespace App\Http\Controllers;
use App\Models\LP;
use App\Models\Role;
use App\Models\User;
use App\Models\Report;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\RetailerFormMail;
use App\Models\RetailerStatement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\RetailerAddress; // Address model

class RetailerController extends Controller
{
    public function index()
    {
        // Check if the user is a Super Admin
        if (auth()->user()->hasRole('Super Admin')) {
            // Super Admin can view all retailers
            $retailers = Retailer::with('address')->get();
        } else {
            // For LPs, get only retailers created by the logged-in LP
            $lpId = LP::where('user_id', auth()->id())->value('id');
            $retailers = Retailer::with('address')
                ->where('lp_id', $lpId)
                ->get();
        }
    
        return view('super_admin.retailer.index', compact('retailers'));
    }
    
    public function dashboard()
    {
        $user = auth()->user();
        $totalIrccDollarAllRetailers = 0;
        $retailerIrccDollars = []; // Array to store each retailer's IRCC dollar data
        
        // Fetch all reports and loop through them
        $reports = Report::with('retailer')->get();
        
        foreach ($reports as $report) {
            $retailerId = $report->retailer_id;
            
            // Fetch statements for each retailer
            $statements = RetailerStatement::where('retailer_id', $retailerId)->get();
            
            $totalIrccDollar = 0;
            
            foreach ($statements as $statement) {
                // Calculate total IRCC dollar sum
                $totalIrccDollar += $statement->ircc_dollar;
            }
            
            // Store the calculated data for each retailer
            $retailerIrccDollars[] = [
                'retailer_id' => $retailerId,
                'total_ircc_dollar' => $totalIrccDollar,
            ];
            
    
            $totalIrccDollarAllRetailers += $totalIrccDollar;
        }
        
        // Total purchase sum from Cleansheet
        $totalPurchaseSum = Cleansheet::sum('purchase');
    
        // Get the total purchases grouped by province
        $provinceData = Cleansheet::select('province', DB::raw('SUM(purchase) as total_purchase'))
            ->groupBy('province')
            ->get();
    
        // Pass the data to the view
        return view('super_admin.retailer.dashboard', compact('totalIrccDollarAllRetailers', 'retailerIrccDollars', 'totalPurchaseSum', 'provinceData'));
    }
    

    public function manageInfo()
    {
        // Retrieve the retailer information. Modify this as needed based on your data structure.
        $retailer = Retailer::with('address')->find(auth()->user()->id);

        // Return the view with retailer data
        return view('retailers.manage-info', compact('retailer'));
    }
    public function viewStatement($retailerId)
    {
        // Fetch the retailer by ID
        $retailer = Retailer::findOrFail($retailerId);
    
        // Fetch the retailer's statement data (Modify the query as necessary)
        $statements = RetailerStatement::where('retailer_id', $retailerId)->get();
    
        // Assume that `report_id` is available on each statement.
        // Pass the first report ID found for this retailer if it exists.
        $reportId = $statements->first()->report_id ?? null;
    
        // Return the view with the retailer, statements, and reportId data
        return view('super_admin.retailer.view-statement', compact('retailer', 'statements', 'reportId'));
    }
    
    
    
    public function create()
    {
        $lps = Lp::all();
        return view('super_admin.retailer.create', compact('lps'));
    }
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/',  // Only allow letters and spaces
            ],
            'last_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/',  // Only allow letters and spaces
            ],
            'email' => [
                'required',
                'email',
                'unique:retailers,email',
                'unique:users,email',  // Ensure the email is unique in the users table as well
                'regex:/^[\w\.-]+@[\w\.-]+\.\w{2,4}$/'  // Example regex for standard email formats
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^\+?\d{1,3}\s*\(?\d{3}?\)?\s*\d{3}[-\s]?\d{4}$/'  // Accepts formats like +1 (425) 274-9782
            ],
            'type' => 'required|string', 
            // For Super Admin, validate LP if the user is Super Admin
            'lp_id' => auth()->user()->hasRole('Super Admin') ? 'required|exists:lps,id' : 'nullable',
        ]);
    
        // Create the user who will be the actual retailer
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'password' => Hash::make(Str::random(10)), // Generate a temporary password
        ]);
    
        // Assign the retailer role to the user
        $role = Role::where('original_name', 'Retailer')->first();
        if ($role) {
            $user->assignRole($role->name);
        } else {
            return redirect()->back()->with('error', 'Role not found.');
        }
    
        // Check if the user is a Super Admin
        if (auth()->user()->hasRole('Super Admin')) {
            // For Super Admin, assign the selected LP ID from the dropdown
            $lpId = $request->input('lp_id');
        } else {
            // For LP portal, use the authenticated user's LP ID
            $lpId = LP::where('user_id', auth()->id())->value('id');
        }
    
        // Create the retailer record and link it to the selected or current LP
        $retailer = Retailer::create([
            'user_id' => $user->id,
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'status' => 'requested',
            'lp_id' => $lpId, // Assign the lp_id
            'type' => $request->input('type'),
        ]);
    
        // Generate a token and the link for completing the retailer information
        $token = base64_encode($retailer->id);
        $link = route('retailer.fillForm', ['token' => $token]);
    
        // Send an email with the link
        Mail::to($validatedData['email'])->send(new RetailerFormMail($link));
    
        // Redirect back with a success message
        return redirect()->route('retailer.create')->with('success', 'Retailer created and email sent successfully!');
    }
    
    
    public function showForm($token)
    {
        $retailerId = base64_decode($token);
        $retailer = Retailer::findOrFail($retailerId);
        $provinces = Province::all(); 
        return view('super_admin.retailer.complete_form', compact('retailer', 'provinces'));
    }
    public function submitForm(Request $request)
{
    // Validate the request data
    $validatedData = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:retailers,email,' . $request->retailer_id,
        'phone' => 'required|string',
        'corporate_name' => 'nullable|string',
        'dba' => 'required|string',
        'password' => 'required|confirmed|min:8',
        'addresses.*.street_no' => 'nullable|string|max:50',
        'addresses.*.street_name' => 'nullable|string|max:255',
        'addresses.*.province' => 'nullable|string|max:255',
        'addresses.*.city' => 'nullable|string|max:255',
        'addresses.*.location' => 'nullable|string|max:255',
        'addresses.*.contact_person_name' => 'nullable|string|max:255',
        'addresses.*.contact_person_phone' => 'nullable|string|max:20',
        'addresses.*.postal_code' => 'nullable|string|max:20', 
    ]);
    // Find the retailer based on the provided ID
    $retailer = Retailer::findOrFail($request->retailer_id);
    // Update retailer details with the validated data
    $retailer->update([
        'corporate_name' => $validatedData['corporate_name'],
        'dba' => $validatedData['dba'],
        'status' => 'approved', // Change status to 'approved'
    ]);
    // Create or update the User record for the retailer
    $user = User::updateOrCreate(
        ['email' => $validatedData['email']], // Unique identifier
        [
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'password' => Hash::make($validatedData['password']),
            'phone' => $validatedData['phone'],
        ]
    );
    // Fetch the role by original name (ensure the role exists)
    $role = Role::where('original_name', 'Retailer')->first(); // Adjust 'Retailer' if necessary
    if ($role) {
        // Assign the role to the user
        $user->assignRole($role->name);
    } else {
        return redirect()->back()->with('error', 'Role not found.');
    }
    // Clear existing addresses and create new ones
    $retailer->address()->delete();
    foreach ($request->input('addresses', []) as $addressData) {
        $retailer->address()->create($addressData);
    }
    // Redirect to the login page with a success message
    return redirect()->route('login')->with('success', 'Retailer information completed successfully. Please log in.');
}
    public function edit($id)
    {
        $lps = Lp::all();
        $retailer = Retailer::with('address')->findOrFail($id);
        return view('super_admin.retailer.edit', compact('retailer','lps'));
    }
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'corporate_name' => 'nullable|string|max:255',
            'dba' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'street_no' => 'nullable|string|max:50',
            'street_name' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'postal_code' => 'nullable|string|max:20', // Add postal_code validation
        ]);
        $retailer = Retailer::findOrFail($id);
        $retailer->update($request->only([
            'first_name', 'last_name', 'corporate_name', 'dba', 'phone', 'email','status'
        ]));
        $retailer->address()->updateOrCreate(
            ['retailer_id' => $retailer->id],
            $request->only([
                'street_no', 'street_name', 'province', 'city', 'location',
                'contact_person_name', 'contact_person_phone'
            ])
        );
        return redirect()->route('retailer.index')->with('success', 'Retailer updated successfully.');
    }
    public function destroy($id)
    {
        $retailer = Retailer::findOrFail($id);
        $retailer->delete();
        return redirect()->route('retailer.index')->with('success', 'Retailer deleted successfully.');
    }
    public function show($id)
    {
        $retailer = Retailer::with('address')->findOrFail($id);
        return view('super_admin.retailer.show', compact('retailer'));
    }
    public function createAddress($id)
    {
        $retailer = Retailer::findOrFail($id);
        $provinces = Province::all(); // Fetching provinces
        return view('super_admin.retailer.create_address', compact('retailer', 'provinces'));
    }
    public function editAddress($id)
    {
        $retailer = Retailer::findOrFail($id);
        return view('retailer.edit_address', compact('retailer'));
    }
    public function updateAddress(Request $request, $id)
    {
        $retailer = Retailer::findOrFail($id);
        $addressData = $request->validate([
            'street_no' => 'nullable|string|max:50',
            'street_name' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
        ]);
        $retailer->address()->updateOrCreate([], $addressData);
        return redirect()->route('retailer.show', $id)->with('success', 'Address updated successfully.');
    }
    public function storeAddress(Request $request, $id)
    {
        
        $request->validate([
            'addresses.*.street_no' => 'required|string|max:50',
            'addresses.*.street_name' => 'required|string|max:255',
            'addresses.*.province' => 'required|string|max:255',
            'addresses.*.city' => 'required|string|max:255',
            'addresses.*.location' => 'required|string|max:255',
            'addresses.*.contact_person_name' => 'nullable|string|max:255',
            'addresses.*.contact_person_phone' => 'nullable|string|max:20',
        ], [
            'addresses.*.street_no.required' => 'Street No is required.',
            'addresses.*.street_name.required' => 'Street Name is required.',
            'addresses.*.province.required' => 'Province is required.',
            'addresses.*.city.required' => 'City is required.',
            'addresses.*.location.required' => 'Location is required.',
            'addresses.*.contact_person_name.max' => 'Contact Person Name cannot exceed 255 characters.',
            'addresses.*.contact_person_phone.max' => 'Contact Person Phone cannot exceed 20 characters.',
        ]);
        // Find the retailer or fail
        $retailer = Retailer::findOrFail($id);
        // Loop through the addresses and create them
        foreach ($request->addresses as $addressData) {
            $retailer->address()->create($addressData);
        }
        // Redirect back with a success message
        return redirect()->route('retailer.show', $id)->with('success', 'Addresses added successfully.');
    }

    public function getAddresses($retailerId)
{
    // Assuming you have a Retailer model that has addresses defined as a relationship
    $addresses = RetailerAddress::where('retailer_id', $retailerId)->get();

    // Return the addresses as JSON
    return response()->json($addresses);
}
}