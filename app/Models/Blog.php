<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blogs';

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'category',
        'author',
        'read_time',
        'published_at',
        'image_url',
        'url'
    ];
}
