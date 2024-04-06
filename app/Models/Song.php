<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'subtitle',
        'file',
        'image',
        'status',
        'singer_id',
        'album_id',
        'mood_id',
        'language_id',
        'genre_id',
        'year',
    ];
    protected $appends = ['file_data', 'image_data', 'like_flag'];
    //Mutator

    public function getImageDataAttribute() {
        return url($this->image);
    }


    public function getFileDataAttribute(){
        return url($this->file);
    }

    public function getLikeFlagAttribute()
    {
        $user = Auth::guard('api')->user();
        if ($user) {  
            $likedSong = Like::where('song_id', $this->id)->where('user_id', $user->id)->first();
            return !is_null($likedSong);
        }
       
        return null; // No authenticated user
      
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function album()
    {
        return $this->belongsTo(Album::class);

    }
    public function singer()
    {
        return $this->belongsTo(Singer::class);
    }

    public function mood()
    {
        return $this->belongsTo(Mood::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
    public function playlist()
    {
        return $this->belongsToMany(Playlist::class,'playlist_songs', 'song_id','playlist_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }


   

}

