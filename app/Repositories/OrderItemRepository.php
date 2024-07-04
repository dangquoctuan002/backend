<?php

namespace App\Repositories;


use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class OrderItemRepository extends BaseRepository
{
    public function model()
    {
        return OrderItem::class;
    }
}