<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderRepository;
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index(Request $request)
    {
        $orders = $this->orderRepository->listWhere(
            [
                "page" => $request->get('page', 1),
                "limit" => 9
            ]
        );
        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }

    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'username' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
            'note' => 'nullable|string',
            'total_amount' => 'required|numeric',
            'status' => 'required|in:Đang chờ xử lý,Đã xác nhận,Đang vận chuyển,Đã giao hàng,Đã hủy,Đang chờ thanh toán',
            'orderdate' => 'nullable|date',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            // Create order
            $order = Order::create($validatedData);

            // Save order items
            foreach ($validatedData['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->quantity -= $item['quantity'];
                    $product->sold += $item['quantity'];
                    $product->save();
                }
            }


            return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
        } catch (\Exception $e) {
            // Log the error
            // Log::error('Error creating order: ' . $e->getMessage());

            // Return error response
            return response()->json(['message' => 'Failed to create order', 'error' => $e->getMessage()], 500);
        }
    }


    // public function update(Request $request, $id)
    // {
    //    return $this->orderRepository->updateWhere($request->all(),$id);
    // }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(null, 204);
    }
    public function getUserOrders($userId)
    {
        $orders = Order::where('user_id', $userId)
            ->with(['orderItems.product.color', 'orderItems.product'])
            ->get();

        return response()->json($orders);
    }

    // public function cancelOrder($orderId)
    // {
    //     $order = Order::find($orderId);
    //     if ($order) {
    //         $order->status = 'Đã hủy';
    //         $order->save();
    //         return response()->json(['message' => 'Order has been cancelled.'], 200);
    //     }
    //     return response()->json(['message' => 'Order not found.'], 404);
    // }

    public function cancelOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $orderTime = $order->created_at;
            $currentTime = now();
            $timeDifference = $currentTime->diffInMinutes($orderTime);

            if ($timeDifference <= 30) {
                $order->status = 'Đã hủy';
                $order->save();

                // Lấy danh sách sản phẩm và hoàn trả lại số lượng sản phẩm
                $products = $order->items;
                $productDetails = [];

                foreach ($products as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->quantity += $item->quantity;
                        $product->sold -= $item->quantity;
                        $product->save();

                        $productDetails[] = [
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity
                        ];
                    }
                }

                return response()->json([
                    'message' => 'Đơn hàng đã bị hủy.',
                    'products' => $productDetails
                ], 200);
            } else {
                return response()->json(['message' => 'Đơn hàng không thể bị hủy sau 30 phút.'], 400);
            }
        }

        return response()->json(['message' => 'Không tìm thấy đơn hàng.'], 404);
    }

    public function okeOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->status = 'Đã xác nhận';
            $order->save();

            return response()->json([
                'message' => 'Đơn hàng đã được xác nhận.',
            ], 200);
        }
        return response()->json(['message' => 'Không tìm thấy đơn hàng.'], 404);
    }

    public function getOrderDetail($orderId)
    {
        $order = Order::with(['user', 'orderItems.product.color'])->find($orderId);

        if ($order) {
            return response()->json($order);
        }

        return response()->json(['message' => 'Order not found.'], 404);
    }
    // public function getUserOrders($userId)
    // {
    //     $orders = Order::where('user_id', $userId)
    //         ->with(['orderItems.product.color', 'orderItems.product'])
    //         ->get();

    //     $products = $orders->flatMap(function ($order) {
    //         return $order->orderItems->map(function ($orderItem) {
    //             return [
    //                 'image' => $orderItem->product->image,
    //                 'name' => $orderItem->product->name,
    //                 'price' => $orderItem->product->price,
    //                 'image_url' => $orderItem->product->image_url,
    //                 'quantity' => $orderItem->quantity,
    //                 'created' => $orderItem->created_at,
    //                 'color' => $orderItem->product->color->name, // assuming color relation is defined
    //                 'product_id' => $orderItem->product->id
    //             ];
    //         });
    //     });

    //     return response()->json($products);
    // }
}
