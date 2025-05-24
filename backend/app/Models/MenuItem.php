<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_available',
        'image_path',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'float',
    ];

    // ✅ Inverse many-to-many relationship with Order
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'item_order', 'item_id', 'order_id');
    }
}
