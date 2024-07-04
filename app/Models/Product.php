<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code',
        'name',
        'image',
        'title',
        'description',

        'price',
        'discount',
        'quantity',
        'favourite',
        'rating',
        'total_rating',
        'sold',
        'group_id',
        'status',

        'color_id',
        'brand_id',
        'category_id',

    ];
    
    protected $appends = ['image_url'];

    // Accessor for image_url
    public function getImageUrlAttribute()
    {
        return url('storage/images/products/' . $this->image);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function review()
    {
        return $this->hasMany(Review::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
   
}

