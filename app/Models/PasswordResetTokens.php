<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PasswordResetTokens extends Model {
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'email_address',
        'token'
    ];

    protected $hidden = ['deleted_at'];

    public function user() {
        return $this->belongsTo(Users::class, 'user_id', 'id');
    }
}
