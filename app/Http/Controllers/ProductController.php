<?php

namespace App\Http\Controllers;

use App\Models\Lp;

use Illuminate\Http\Request;
use App\Models\ProductVariation;
use App\Models\Product; // Make sure to import your Product model

class ProductController extends Controller
{
    // public function index()
    // {
    //     $products = Product::all(); // Fetch all products
    //     return view('lp.products', compact('products')); // Return the products view
    // }

    public function edit($id)
    {
        $product = Product::findOrFail($id); // Fetch the product by ID
        return view('super_admin.lp.edit_product', compact('product')); // Return the edit view with product data
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete(); // Soft delete or delete the product
        return redirect()->route('lp.products')->with('success', 'Product deleted successfully.');
    }

    public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);
    $product->update($request->all()); // Update the product with request data
    return redirect()->route('products.index')->with('success', 'Product updated successfully.');
}
public function showVariations($lp_id, $gtin)
{
    // Fetch products based on lp_id and gtin
    $products = ProductVariation::where('lp_id', $lp_id)
                ->where('gtin', $gtin)
                ->get();

    // Fetch the LP associated with the lp_id
    $lp = Lp::find($lp_id);

    // Pass both products, gtin, and LP to the view
    return view('super_admin.lp.product_variations', compact('products', 'gtin', 'lp'));
}

public function viewProducts($lp_id = null)
{
    // Get the currently authenticated user
    $user = auth()->user();

    // If an LP ID is provided (super admin scenario)
    if ($lp_id) {
        $lp = Lp::find($lp_id);
        if ($lp) {
            // Fetch products uploaded by this LP, with DBA name
            $products = Product::with('lp')->where('lp_id', $lp->id)->get();
        } else {
            return redirect()->back()->with('error', 'LP not found.');
        }
    } else {
        // Check if the user is an LP
        if ($user->hasRole('LP')) {
            // Get the LP ID associated with the logged-in user
            $lp = Lp::where('user_id', $user->id)->first();

            if ($lp) {
                // Fetch products uploaded by this LP, with DBA name
                $products = Product::with('lp')->where('lp_id', $lp->id)->get();
            } else {
                $products = collect(); // Empty collection if no LP found
            }
        } else {
            // Super admin: Fetch all products for all LPs, with DBA name
            $products = Product::with('lp')->get();
        }
    }

    return view('super_admin.lp.products', compact('products'));
}


}
