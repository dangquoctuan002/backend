<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\ListImageController;
use App\Models\ProductDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/upload-image', [ImageUploadController::class, 'uploadImage']);

// Route::post('/login', [UserController::class, 'login']);
// Route::post('login', [UserController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::put('/{id}/password', [UserController::class, 'changePassword']);
    Route::post('/{id}/update-avatar', [UserController::class, 'updateAvatar']);
    // Route::post('change-password', [UserController::class, 'changePassword']);

});
// Route::post('/users', [UserController::class, 'store']);
// Route::put('/users/{user}', [UserController::class, 'update']);

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    Route::get('/{id}/group', [ProductController::class, 'getGroupProducts']);
});
Route::get('/favourite', [ProductController::class, 'getTopFavouriteProducts']);
Route::get('top-rated-products', [ProductController::class, 'getTopRatedProducts']);
Route::get('top-selling-products', [ProductController::class, 'getTopSellingProducts']);
Route::get('orders/{userId}/products', [ProductController::class, 'getOrderedProducts']);

Route::get('/user/{id}/orders', [OrderController::class, 'getUserOrders']);
Route::post('/order/{id}/cancel', [OrderController::class, 'cancelOrder']);
Route::post('/order/{id}/oke', [OrderController::class, 'okeOrder']);

Route::prefix('colors')->group(function () {
    Route::get('/', [ColorController::class, 'index']);
    Route::post('/', [ColorController::class, 'store']);
    Route::get('/{id}', [ColorController::class, 'show']);
    Route::put('/{id}', [ColorController::class, 'update']);
    Route::delete('/{id}', [ColorController::class, 'destroy']);
});

Route::prefix('carts')->group(function () {
    Route::get('/{id}', [CartController::class, 'show']);
    Route::post('/additem', [CartController::class, 'addItem']);
    Route::put('user/{userId}/cart/{cartItemId}', [CartController::class, 'updateCartItem']);
    Route::delete('user/{userId}/cart/{cartItemId}', [CartController::class, 'deleteCartItem']);

});


Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
    Route::post('/products-by-category', [CategoryController::class, 'getProductsByCategory']);
});

Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::put('/{id}', [OrderController::class, 'update']);
    Route::delete('/{id}', [OrderController::class, 'destroy']);
});
Route::get('/order/{id}', [OrderController::class, 'getOrderDetail']);

Route::prefix('order_items')->group(function () {
    Route::get('/', [OrderItemController::class, 'index']);
    Route::post('/', [OrderItemController::class, 'store']);
    Route::get('/{id}', [OrderItemController::class, 'show']);
    Route::put('/{id}', [OrderItemController::class, 'update']);
    Route::delete('/{id}', [OrderItemController::class, 'destroy']);
});

Route::prefix('reviews')->group(function () {
    Route::get('/', [ReviewController::class, 'index']);
    Route::post('/', [ReviewController::class, 'store']);
    Route::get('/{id}', [ReviewController::class, 'show']);
    Route::put('/{id}', [ReviewController::class, 'update']);
    Route::delete('/{id}', [ReviewController::class, 'destroy']);
    Route::get('/{product_id}/reviews', [ReviewController::class, 'getReviews']);
});

Route::prefix('brands')->group(function () {
    Route::get('/', [BrandController::class, 'index']);
    Route::post('/', [BrandController::class, 'store']);
    Route::get('/{id}', [BrandController::class, 'show']);
    Route::put('/{id}', [BrandController::class, 'update']);
    Route::delete('/{id}', [BrandController::class, 'destroy']);
});

Route::prefix('coupons')->group(function () {
    Route::get('/', [CouponController::class, 'index']);
    Route::get('/{id}', [CouponController::class, 'show']);
    Route::post('/', [CouponController::class, 'store']);
    Route::put('/{id}', [CouponController::class, 'update']);
    Route::delete('/{id}', [CouponController::class, 'destroy']);
    Route::get('/type/{type}', [CouponController::class, 'filterByType']);
    Route::post('/validate-coupon', [CouponController::class, 'validateCoupon']);
});
Route::post('/contacts', [ContactController::class, 'store']);

Route::prefix('contacts')->group(function () {
    Route::get('/', [ContactController::class, 'index']);
    Route::get('/{id}', [ContactController::class, 'show']);
    Route::put('/{id}', [ContactController::class, 'update']);
    Route::delete('/{id}', [ContactController::class, 'destroy']);
});