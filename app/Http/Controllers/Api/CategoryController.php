<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        $query = Category::query();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        // Sorting
        $sortBy = $request->has('sort_by') ? $request->sort_by : 'name';
        $sortOrder = $request->has('sort_order') ? $request->sort_order : 'asc';
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination
        $perPage = $request->has('per_page') ? $request->per_page : 10;
        $categories = $query->paginate($perPage);
        
        // Return response in the required format
        return response()->json([
            'message' => 'تم جلب البيانات بنجاح', // Data fetched successfully
            'data' => $categories->items(),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'last_page' => $categories->lastPage(),
                'from' => $categories->firstItem(),
                'to' => $categories->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created category (Admin only).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            // Return validation error in the required format
            return response()->json([
                'message' => 'خطأ في التحقق', // Validation error
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        // Return success response in the required format
        return response()->json([
            'message' => 'تم إنشاء الفئة بنجاح', // Category created successfully
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            // Return success response in the required format
            return response()->json([
                'message' => 'تم جلب البيانات بنجاح', // Data fetched successfully
                'data' => $category
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Return not found error in the required format
            return response()->json([
                'message' => 'الفئة غير موجودة' // Category not found
            ], 404);
        }
    }

    /**
     * Update the specified category (Admin only).
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                // Return validation error in the required format
                return response()->json([
                    'message' => 'خطأ في التحقق', // Validation error
                    'errors' => $validator->errors()
                ], 422);
            }

            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
            ]);

            // Return success response in the required format
            return response()->json([
                'message' => 'تم تحديث الفئة بنجاح', // Category updated successfully
                'data' => $category
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Return not found error in the required format
            return response()->json([
                'message' => 'الفئة غير موجودة' // Category not found
            ], 404);
        }
    }

    /**
     * Remove the specified category (Admin only).
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            // Check if category has products
            if ($category->products()->count() > 0) {
                // Return validation error in the required format
                return response()->json([
                    'message' => 'لا يمكن حذف فئة تحتوي على منتجات' // Cannot delete category with products
                ], 422);
            }
            
            $category->delete();
            
            // Return success response in the required format
            return response()->json([
                'message' => 'تم حذف الفئة بنجاح' // Category deleted successfully
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Return not found error in the required format
            return response()->json([
                'message' => 'الفئة غير موجودة' // Category not found
            ], 404);
        }
    }

    /**
     * Get categories with product count for dropdowns.
     */
    public function withCount()
    {
        $categories = Category::withCount('products')->get();
        
        // Return success response in the required format
        return response()->json([
            'message' => 'تم جلب البيانات بنجاح', // Data fetched successfully
            'data' => $categories
        ]);
    }
}