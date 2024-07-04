<?php

namespace App\Http\Controllers;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Repositories\CouponRepository;

class CouponController extends Controller
{
    protected $couponRepository;
    public function __construct(CouponRepository $couponRepository)
    {
        $this->couponRepository = $couponRepository;
    }


    public function index(Request $request)
    {
        $coupon = $this->couponRepository->listWhere(
            [
                "page" => $request->get('page', 1),
                "limit" => 9
            ]
        );
        return response()->json($coupon);
    }

    public function show($id)
    {
        return Coupon::find($id);
    }

    public function store(Request $request)
    {
        // $start_date = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        // $end_date = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
    
        // $request->merge([
        //     'start_date' => $start_date,
        //     'end_date' => $end_date,
        // ]);

        $request->validate([
            'code' => 'required|string',
            'name' => 'required|string',
            'type' => 'required|in:fixed,percent,free_shipping',
            'value' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);
        $code = $request->code;
        // return Coupon::create($request->all());

        if (Coupon::where('code', $code)->exists()) {
            return response()->json(['status' => 0, 'message' => 'Mã giảm giá đã tồn tại'], 400);
        }

        $coupon = $this->couponRepository->createData($request->all());

        return response()->json(['status' => 1, 'data' => $coupon]);

    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found'], 404);
        }

        $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string',
            'type' => 'required|in:fixed,percent,free_shipping',
            'value' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $coupon->update($request->all());

        return $coupon;
    }

    public function destroy($id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found'], 404);
        }

        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted successfully']);
    }

    public function filterByType($type)
    {
        return Coupon::where('type', $type)->get();
    }

    public function validateCoupon(Request $request)
    {
        $code = $request->input('code');
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json(['error' => 'Mã giảm giá không tồn tại'], 404);
        }

        $currentDate = Carbon::now();
        if ($currentDate < $coupon->start_date || $currentDate > $coupon->end_date) {
            return response()->json(['error' => 'Mã giảm giá đã hết hạn'], 400);
        }

        return response()->json(['coupon' => $coupon], 200);
    }
}
