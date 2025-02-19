<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $table = 'order';
    protected $fillable = [
        'user_id', 'product_id', 'qty', 'total_price', 'color', 'size', 'status'
    ];

    // Cast color and size to array automatically
    protected $casts = [
        'color' => 'array',
        'size' => 'array',
    ];
     public function getUser (){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function getProduct (){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
