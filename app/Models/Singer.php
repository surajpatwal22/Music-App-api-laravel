<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Singer extends Model
{
    use HasFactory;
    protected $fillable=['name','status','Image'];

    protected $appends = ['image_data' , 'like_flag'];  

    public function getImageDataAttribute() {
        return url($this->Image);
    }

    
    public function getLikeFlagAttribute()
    {
        $user = Auth::guard('api')->user();
        if ($user) {  
            $likedSong = SingerLike::where('singer_id', $this->id)->where('user_id', $user->id)->first();
            return !is_null($likedSong);
        }
       
        return null; // No authenticated user
      
    }

    public function songs() {
        return $this->hasMany(Song::class);
    }

    
}

