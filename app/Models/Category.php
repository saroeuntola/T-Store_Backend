<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'category';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'user_id'
];

    public function getUser (){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
