<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    public function getLikesCountAttribute()
    {
        return 5;
    }

    public function getDislikesCountAttribute()
    {
        return 2;
    }
}
