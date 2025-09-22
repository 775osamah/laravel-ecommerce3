<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Create a new order.
     * 
     * Handles: POST /api/v1/orders
     * Request Body should be:
     * {
     *    "items": [
     *        {"product_id": 10, "qty": 2},
     *        {"product_id": 12, "qty": 1}
     *    ],
     *    "address": { "city": "Sanaa", "street": "Hadda" }
     * }
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'address.city' => 'required|string|max:255',
            'address.street' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطأ في التحقق',
                'errors' => $validator->errors()
            ], 422);
        }

        // Use a database transaction to ensure data consistency
        try {
            DB::beginTransaction();

            // Calculate total and check product availability
            $total = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                // Check if product exists and has enough stock
                if (!$product) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'المنتج غير موجود'
                    ], 404);
                }

                if ($product->quantity < $item['qty']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "الكمية غير متوفرة للمنتج: {$product->name}"
                    ], 422);
                }

                // Calculate item total and update product stock
                $itemTotal = $product->price * $item['qty'];
                $total += $itemTotal;

                // Prepare order item data
                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'unit_price' => $product->price,
                    'total_price' => $itemTotal
                ];

                // Update product stock
                $product->decrement('quantity', $item['qty']);
            }

            // Generate a unique order code
            $orderCode = 'ORD-' . strtoupper(uniqid());

            // Get the authenticated user using Auth facade
            $user = Auth::user();

            // Create the order
            $order = Order::create([
                'user_id' => $user->id,
                'code' => $orderCode,
                'total_price' => $total,
                'status' => 'PENDING', // Default status
                'address' => json_encode($request->address), // Store address as JSON
            ]);

            // Create order items
            foreach ($orderItems as $orderItem) {
                $orderItem['order_id'] = $order->id;
                OrderItem::create($orderItem);
            }

            DB::commit();

            // Load relationships for the response
            $order->load('items.product');

            return response()->json([
                'message' => 'تم إنشاء الطلب بنجاح',
                'data' => $order
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'فشل في إنشاء الطلب'
            ], 500);
        }
    }

    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        // Get the authenticated user using Auth facade
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)
                      ->with('items.product')
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);

        return response()->json([
            'message' => 'تم جلب البيانات بنجاح',
            'data' => $orders->items(),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'last_page' => $orders->lastPage(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
            ]
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show($code)
    {
        // Get the authenticated user using Auth facade
        $user = Auth::user();

        $order = Order::where('code', $code)
                     ->where('user_id', $user->id)
                     ->with('items.product')
                     ->first();

        if (!$order) {
            return response()->json([
                'message' => 'الطلب غير موجود'
            ], 404);
        }

        return response()->json([
            'message' => 'تم جلب البيانات بنجاح',
            'data' => $order
        ]);
    }

    // Other methods will be implemented later
    public function updateStatus(Request $request, $id) {}
}