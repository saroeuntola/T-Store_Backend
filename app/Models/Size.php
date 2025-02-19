<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;
    protected $table ='size';
    protected $primaryKey = 'id';
    protected $fillable = [
        'size_name',
        'user_id',
];
public function product()
    {
        return $this->belongsToMany(Product::class, 'product_size');
    }
}
