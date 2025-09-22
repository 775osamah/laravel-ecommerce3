<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     * 
     * Handles: GET /api/v1/products?search=&category_id=&page=
     */
    public function index(Request $request)
    {
        // Start building the query
        $query = Product::with('category'); // Eager load the category relationship

        // 1. Apply Search Filter (if provided)
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // 2. Apply Category Filter (if provided)
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        // 3. Apply Pagination (default: 10 items per page)
        $perPage = $request->has('per_page') ? $request->per_page : 10;
        $products = $query->paginate($perPage);

        // 4. Return the response in the required JSON format
        return response()->json([
            'message' => 'تم جلب البيانات بنجاح', // Data fetched successfully
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]
        ]);
    }

    /**
     * Display the specified product by its slug.
     * 
     * Handles: GET /api/v1/products/{slug}
     */
    public function show($slug)
    {
        try {
            // Find the product by its slug
            $product = Product::where('slug', $slug)->firstOrFail();
            
            // Load the product's category and any other relations needed
            $product->load('category');

            return response()->json([
                'message' => 'تم جلب البيانات بنجاح',
                'data' => $product
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'المنتج غير موجود' // Product not found
            ], 404);
        }
    }

    // The store(), update(), destroy(), and uploadImages() methods for admin will be built later.
    public function store(Request $request) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}
    public function uploadImages(Request $request, $id) {}
}