<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class External extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [

        'name',
        'eprice',

    ];
}
