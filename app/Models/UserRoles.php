<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRoles extends Model {
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'role_id'
    ];

    protected $hidden = ['deleted_at'];

    public function user() {
        return $this->belongsTo(UserProfiles::class, 'user_id', 'user_id');
    }

    public function role() {
        return $this->hasOne(Roles::class, 'id', 'role_id');
    }

    public function setNewUserRole($data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        $this->save();
        return $this;
    }
}
