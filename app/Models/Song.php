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
        'playlist_id',
        'mood_id',
        'language_id',
        'genre_id',
        'music_director_id',
        'year',
    ];
    protected $appends = ['file_data' ,'image_data'];  //Mutator

    public function getImageDataAttribute() {
        return url($this->image);
    }


    public function getFileDataAttribute(){
        return url($this->file);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function album()
    {
        return $this->belongsTo(Album::class);

    }
   

}