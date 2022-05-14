<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'profile_id'
    ];

    protected $hidden = ['pivot'];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
