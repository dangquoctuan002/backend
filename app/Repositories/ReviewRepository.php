<?php

namespace App\Repositories;

use App\Models\Review;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class ReviewRepository extends BaseRepository
{
    public function model()
    {
        return Review::class;
    }
}