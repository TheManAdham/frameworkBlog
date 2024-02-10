<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $casts = [
        'published_at' => 'datetime',
    ];

    use HasFactory;

    protected $fillable = ['title', 'body', 'name', 'published_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

}


