<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';
    protected $primaryKey = 'id';
    protected $fillable = [
    'name',
    'image',
    'description',
    'price',
    'category_id',
    'user_id',

];

    public function getUser (){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public  function getCategory (){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
 

      public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_size');
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class, 'product_color');
    }
}
