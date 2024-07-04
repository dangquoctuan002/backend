<?php

namespace App\Repositories;


use App\Models\Color;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class ColorRepository extends BaseRepository
{
    public function model()
    {
        return Color::class;
    }
}