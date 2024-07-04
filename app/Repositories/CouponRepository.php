<?php

namespace App\Repositories;

use App\Models\Coupon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class CouponRepository extends BaseRepository
{
    public function model()
    {
        return Coupon::class;
    }
}