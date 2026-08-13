<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    protected $table = 'certifications';

    protected $fillable = [
        'title',
        'issuer',
        'description',
        'document_url',
        'icon'
    ];
}
