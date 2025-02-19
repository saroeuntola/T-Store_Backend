<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    protected $table = 'color';
    protected $primaryKey = 'id';
    protected $fillable = [
        'color_name',
        'user_id'
    ];

 public function product()
    {
        return $this->belongsToMany(Product::class, 'product_color');
    }
}
