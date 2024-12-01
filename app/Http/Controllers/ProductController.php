<?php

namespace App\Http\Controllers;

use App\Models\Lp;
use App\Models\ProductVariation; 
use App\Models\Province;
use App\Models\Product; // Make sure to import your Product model
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Method to show all products
    public function index()
    {
        $products = Product::with('lp')->get(); // Fetch all products with their associated LPs
        return view('super_admin.lp.products', compact('products')); // Return the products view
    }

    // Method to show the edit product form
    public function edit($id)
    {
        $product = Product::findOrFail($id); // Fetch the product by ID
        return view('super_admin.lp.edit_product', compact('product')); // Return the edit view with product data
    }

    // Method to delete a product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $productVariation = ProductVariation::where('product_id',$product->id)->delete();
        $product->delete(); // Soft delete or delete the product
        return redirect()->route('lp.products')->with('success', 'Product deleted successfully.');
    }

    // Method to update a product
    public function update(Request $request, $id)
    {
        // Validate the request data for the required fields
        $request->validate([
            'product_name' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'provincial_sku' => 'required|string|max:255',
            'gtin' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
        ]);
    
        // Find the product by ID
        $product = Product::findOrFail($id);

        if ($request->has('is_validate')) {
           $isValidate = 1;
        } else {
            $isValidate = 0;
        }
    
        // Update the product with the validated data
        $product->update([
            'product_name' => $request->input('product_name'),
            'province' => $request->input('province'),
            'provincial_sku' => $request->input('provincial_sku'),
            'gtin' => $request->input('gtin'),
            'category' => $request->input('category'),
            'brand' => $request->input('brand'),
            'case_quantity' => $request->input('case_quantity'),
            'product_size' => $request->input('product_size'),
            'thc_range' => $request->input('thc_range'),
            'cbd_range' => $request->input('cbd_range'),
            'unit_cost' => $request->input('unit_cost'),
            'is_validate' => $isValidate,
        ]);
    
        // Redirect back with success message
        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }
    

    // Method to show variations of a product
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

    public function update_variation_view($id)
       {
           $productVariation = ProductVariation::where('id', $id)->first();

           $product = Product::where('id',$productVariation->product_id)->first();
           $lpID = $product->lp_id;
           $gtin = $product->gtin;

           $provinces = Province::where('status',1)->get();

           return view('super_admin.lp.product_variation_edit', compact('productVariation','provinces','lpID','gtin'));
       }
    public function update_variation(Request $request, $id)
       {
           $request->validate([
               'product_name' => 'required|string|max:255',
               'province' => 'required|string|max:255',
               'provincial_sku' => 'required|string|max:255',
               'gtin' => 'required|string|max:255',
               'category' => 'required|string|max:255',
               'brand' => 'required|string|max:255',
           ]);
       
           $productVariation = ProductVariation::findOrFail($id);
           $product = Product::where('id',$productVariation->product_id)->first();
   
           if ($request->has('is_validate')) {
              $isValidate = 1;
           } else {
               $isValidate = 0;
           }
           $province = Province::where('id',$request->input('province'))->first();
           $productVariation->update([
               'product_name' => $request->input('product_name'),
               'province_id' => $request->input('province'),
               'province' => $province->name,
               'provincial_sku' => $request->input('provincial_sku'),
               'gtin' => $request->input('gtin'),
               'category' => $request->input('category'),
               'brand' => $request->input('brand'),
            //    'case_quantity' => $request->input('case_quantity'),
               'product_size' => $request->input('product_size'),
               'thc_range' => $request->input('thc_range'),
               'cbd_range' => $request->input('cbd_range'),
               'price_per_unit' => $request->input('price_per_unit'),
               'is_validate' => $isValidate,
           ]);
           $lpID = $product->lp_id;
           $gtin = $product->gtin;
           return redirect()->route('products.variations',[$lpID,$gtin])->with('success', 'Product Variation updated successfully.');
       }

    public function viewProducts(Request $request, $lp_id = null)
    {
        $user = auth()->user();
    
        if ($lp_id) {
            $lp = Lp::find($lp_id);
            if ($lp) {
                $products = Product::with('lp')->where('lp_id', $lp->id)->get();
            } else {
                return redirect()->back()->with('error', 'LP not found.');
            }
        } else {
            if ($user->hasRole('Super Admin')) {
                // Super Admin: Fetch all products for all LPs
                $products = Product::with('lp')->get();
            } elseif ($user->hasRole('LP')) {
                // Get the LP ID associated with the logged-in user
                $lp = Lp::where('user_id', $user->id)->first();
                $products = $lp ? Product::with('lp')->where('lp_id', $lp->id)->get() : collect();
            } else {
                return redirect()->back()->with('error', 'Unauthorized access.');
            }
        }
    
        return view('super_admin.lp.products', compact('products'));
    }
    
}
