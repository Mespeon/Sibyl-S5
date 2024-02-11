<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schools extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name'
    ];

    protected $hidden = ['deleted_at'];
}