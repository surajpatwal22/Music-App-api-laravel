<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Album extends Model
{
    use HasFactory;

    protected $fillable=['name','status','image'];

    protected $appends = ['image_data' ,'like_flag' ];  

    public function getImageDataAttribute() {
        return url($this->image);
    }


    public function songs() {
        return $this->hasMany(Song::class);
    }
}
