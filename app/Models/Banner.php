<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'banner';
    protected $primaryKey = 'id';
    protected $fillable = [
        'banner_image',
        'user_id'
    ];

        public function getUser (){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
