<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class UserRepository extends BaseRepository
{
    public function model()
    {
        return User::class;
    }
}