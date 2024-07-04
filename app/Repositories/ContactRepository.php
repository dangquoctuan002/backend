<?php

namespace App\Repositories;

use App\Models\Contact;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class ContactRepository extends BaseRepository
{
    public function model()
    {
        return Contact::class;
    }
}