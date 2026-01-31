<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'base_url',
        'search_url',
        'selectors',
        'enabled',
    ];

    protected $casts = [
        'selectors' => 'array',
        'enabled' => 'boolean',
    ];
}
