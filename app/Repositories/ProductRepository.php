<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class ProductRepository extends BaseRepository
{
    public function model()
    {
        return Product::class;
    }



    public function listGroup(array $att = [])
    {
        $query = $this->model()::query();

        if (isset($att['frame']) && $att['frame'] != '') {
            $query->where('framesize', '=', $att['frame']);
        }

        if (isset($att['color']) && $att['color'] != '') {
            $query->where('color_id', '=', $att['color']);
        }

        if (isset($att['wheel']) && $att['wheel'] != '') {
            $query->where('wheelsize', '=', $att['wheel']);
        }
        if (isset($att['group_id']) && $att['group_id'] != '') {
            $query->where('group_id', '=', $att['group_id']);
        }

        $dataResult = $query->get();

        // Tính toán tổng số trang

        return [
            "data" => $dataResult,
        ];
    }









}