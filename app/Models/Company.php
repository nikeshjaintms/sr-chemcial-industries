<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'name',
        'short_name',
        'tagline',
        'about',
        'mission',
        'vision',
        'address',
        'phone_primary',
        'phone_secondary',
        'email_primary',
        'email_secondary',
        'website_url',
        'logo_url',
        'services',
        'highlights',
        'logistics_info',
        'compliance_info'
    ];

    protected $casts = [
        'services' => 'array',
        'highlights' => 'array'
    ];
}
