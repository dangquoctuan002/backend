<?php

namespace App\Repositories;

use App\Models\Cart;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class CartRepository extends BaseRepository
{
    public function model()
    {
        return Cart::class;
    }
}