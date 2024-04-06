<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;
     
    protected $fillable=['playlist_name','status','image' , 'description'];

    protected $appends = ['image_data' ];  

    public function getImageDataAttribute() {
        return url($this->image);
    }

    public function songs() {
        return $this->belongsToMany(Song::class,'playlist_songs','playlist_id','song_id');
    }


}
