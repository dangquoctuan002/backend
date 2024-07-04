<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cart;
use App\Repositories\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class CartController extends Controller
{
    public function addItem(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // $user = auth()->user(); // Lấy thông tin user đang đăng nhập
            // $user = User::find($id);
            $product = Product::findOrFail($request->product_id);
            $requestedQuantity = $request->quantity;
            $availableQuantity = $product->quantity;


            if ($requestedQuantity > $availableQuantity) {
                return response()->json(['error' => 'Số lượng sản phẩm không đủ'], 400);
            }

            // Tìm kiếm sản phẩm trong giỏ hàng của user
            $cartItem = Cart::where('user_id', $request->user_id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($cartItem) {
                $totalCartQuantity = $cartItem->quantity;
                if ($totalCartQuantity >= $availableQuantity) {
                    return response()->json(['error' => 'Số lượng mặt hàng trong kho còn thấp'], 400);
                }
                // if ($totalCartQuantity == $availableQuantity) {
                //     return response()->json(['error' => 'Đã thêm hết số lượng sản phẩm vào giỏ hàng']);
                // }
                // if ($totalCartQuantity + $requestedQuantity > $availableQuantity) {
                //     $quantityItem = $availableQuantity - $totalCartQuantity;
                //     $cartItem->quantity += $quantityItem;
                //     $cartItem->save();
                //     return response()->json(['message' => 'Thêm sản phẩm vào giỏ hàng thành công']);
                // }

                $cartItem->quantity += $request->quantity;
                $cartItem->save();


            } else {
                // Nếu chưa có, tạo mới
                $cartItem = new Cart();
                $cartItem->user_id = $request->user_id;
                $cartItem->product_id = $request->product_id;
                $cartItem->quantity = $request->quantity;
                $cartItem->save();
            }
            return response()->json(['message' => 'Thêm sản phẩm vào giỏ hàng thành công']);


        } catch (\Exception $e) {
            // Xử lý các ngoại lệ
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $cartItems = $user->cartItems()->with('product')->get();
        $response = [
            'user' => $user,
            'cart' => $cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product->id,
                    'name' => $item->product->name,
                    'wheel' => $item->product->wheelsize,
                    'frame' => $item->product->framesize,
                    'color_id' => $item->product->color_id,
                    'image' => $item->product->image_url,
                    'productQuantity' => $item->product->quantity,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                ];
            }),
        ];

        return response()->json($response);
    }

    public function updateCartItem(Request $request, $userId, $cartItemId)
    {
        // Tìm người dùng
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Tìm mặt hàng trong giỏ hàng
        $cartItem = Cart::where('user_id', $userId)->find($cartItemId);
        if (!$cartItem) {
            return response()->json(['message' => 'CartItem not found'], 404);
        }

        // Cập nhật số lượng
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json(['message' => 'CartItem updated successfully', 'cart_item' => $cartItem]);
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function deleteCartItem($userId, $cartItemId)
    {
        // Tìm người dùng
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Tìm mặt hàng trong giỏ hàng
        $cartItem = Cart::where('user_id', $userId)->find($cartItemId);
        if (!$cartItem) {
            return response()->json(['message' => 'CartItem not found'], 404);
        }

        // Xóa mặt hàng khỏi giỏ hàng
        $cartItem->delete();

        return response()->json(['message' => 'CartItem deleted successfully']);
    }

}
