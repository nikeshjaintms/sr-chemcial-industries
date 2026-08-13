<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportCountry extends Model
{
    protected $table = 'export_countries';

    protected $fillable = [
        'name',
        'code',
        'region',
        'flag_emoji',
        'details'
    ];
}
