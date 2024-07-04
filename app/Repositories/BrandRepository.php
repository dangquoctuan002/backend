<?php

namespace App\Repositories;

use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class BrandRepository extends BaseRepository
{
    public function model()
    {
        return Brand::class;
    }
}