<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SingerLike extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id','singer_id'
    ];

    public function singer()
    {
        return $this->belongsTo(Singer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
