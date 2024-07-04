<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Order;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class ProductController extends Controller
{
    protected $productRepository;
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index(Request $request)
    {

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $category = $request->get('category');
        $brand = $request->get('brand');
        $color = $request->get('color');
        $star = $request->get('star');
        $range = $request->get('range');
        $orderBy = $request->get('orderBy', 'price'); // Default order by price
        $keyword = $request->get('keyword'); // Keyword for searching

        $filters = [
            'page' => $page,
            'limit' => $limit,
            'category' => $category,
            'brand' => $brand,
            'color' => $color,
            'star' => $star,
            'range' => $range,
            'keyword' => $keyword, // Add keyword to filters
        ];

        $products = $this->productRepository->listWhere($filters, [], $orderBy);
        return response()->json($products);
    }

    public function show($id)
    {
        try {
            // Lấy sản phẩm từ cơ sở dữ liệu theo ID
            $product = Product::findOrFail($id);

            // Add image_url to the product
            // $product->image_url = url('storage/images/' . $product->name);

            // Trả về phản hồi JSON với chi tiết sản phẩm
            return response()->json(['status' => 1, 'data' => $product], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Trả về phản hồi nếu sản phẩm không tồn tại
            return response()->json(['status' => 0, 'message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            // Ghi lại lỗi và trả về phản hồi chung cho lỗi máy chủ
            \Log::error('Error fetching product: ' . $e->getMessage());
            return response()->json(['status' => 0, 'message' => 'An error occurred while fetching the product details'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:products|max:255',
            'name' => 'required',
            'image' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'color_id' => 'required|integer',
            'brand_id' => 'required|integer',
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $product = Product::create($request->all());

        // return response()->json($product, 201);
        return response()->json(['status' => 1, 'data' => $product], 201);

    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|max:255|unique:products,code,' . $id,
            'name' => 'required',
            'image' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'color_id' => 'required|integer',
            'brand_id' => 'required|integer',
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $product->update($request->all());

        return response()->json($product, 200);
    }

    // public function store(Request $request)
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'code' => 'required|string|unique:products,code',
    //             'name' => 'required|string',
    //             'title' => 'required|string',
    //             'price' => 'required|numeric|min:0',
    //             'quantity' => 'required|integer|min:0',
    //             'color' => 'required|string|in:#FF0000,#00FF00,#0000FF,#FFFF00,#000000,#FFFFFF',
    //             'category_id' => 'required|exists:categories,id',
    //             'product_id' => 'required|exists:products,id',
    //             'description' => 'string|nullable',
    //             'image' => 'required|string',
    //         ]);

    //         $product = $this->productRepository->createData($validatedData);

    //         return response()->json(['status' => 1, 'data' => $product], 201);
    //     } catch (ValidationException $e) {
    //         return response()->json(['status' => 0, 'errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         \Log::error('Product creation failed: ' . $e->getMessage());
    //         return response()->json(['status' => 0, 'message' => 'Product creation failed'], 500);
    //     }
    // }

    // public function update(Request $request, $id)
    // {
    //     return $this->productRepository->updateWhere($request->all(), $id);
    // }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        // $relatedDataCount = $product->products()->count();

        // if ($relatedDataCount > 0) {
        //     return response()->json(['status' => 0, 'message' => 'This product cannot be deleted because there is associated data'], 400);
        // }

        $product = $this->productRepository->deleteWhere($id);

        return response()->json(['status' => 1, 'message' => 'The product has been successfully deleted']);
    }

    public function getGroupProducts($id)
    {
        // Lấy sản phẩm theo id
        $product = Product::find($id);

        // Lấy danh sách các sản phẩm có cùng group_id
        $groupProducts = Product::where('group_id', $product->group_id)->get();

        // Trả về dữ liệu
        return response()->json($groupProducts);
    }

    public function getTopFavouriteProducts()
    {
        $products = Product::orderBy('favourite', 'desc')->take(5)->get();
        return response()->json($products);
    }
    public function getTopRatedProducts()
    {
        // Lấy 9 sản phẩm có tổng đánh giá cao nhất
        $topRatedProducts = Product::orderBy('total_rating', 'desc')->take(9)->get();
        return response()->json($topRatedProducts);
    }
    public function getTopSellingProducts()
    {
        // Lấy 10 sản phẩm bán chạy nhất
        $topSellingProducts = Product::orderBy('sold', 'desc')->take(10)->get();
        return response()->json($topSellingProducts);
    }
    public function getOrderedProducts($userId)
    {
        $orderedProducts = Order::where('user_id', $userId)->with('orderItems.product')->get();

        return response()->json($orderedProducts);

        // $user = User::findOrFail($userId);
        //  $orderedProducts = $user->orders()
        //                          ->with('order_items.product') // Load các sản phẩm liên quan
        //                          ->get();

        //  return response()->json($orderedProducts);
    }
}
