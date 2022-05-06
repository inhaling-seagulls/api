<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'pseudo',
        'contact',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
