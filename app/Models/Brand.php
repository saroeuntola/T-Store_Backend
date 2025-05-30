<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $table = 'brand';
    protected $primaryKey = 'id';
    protected $fillable = [
         'brand_name',
    'user_id',
    'brand_image'
    ];

    public function getUser (){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
