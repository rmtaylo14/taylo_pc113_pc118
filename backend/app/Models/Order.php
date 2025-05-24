<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // ✅ These fields must match the DB columns defined in the migration
    protected $fillable = [
        'user_id',
        'status',
    ];

    // ✅ Relationship to items (many-to-many)
    public function items()
    {
        return $this->belongsToMany(MenuItem::class, 'item_order', 'order_id', 'item_id');
        //                          ^ model      ^ pivot table   ^ this model's key   ^ related model's key
    }


    // ✅ Relationship to user (many-to-one)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
