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
        'artistname',
        'file',
        'image'
    ];
    protected $appends = ['file_data' ,'image_data'];  //Mutator

    public function getImageDataAttribute() {
        return url($this->image);
    }


    public function getFileDataAttribute(){
        return url($this->file);
    }
   

}