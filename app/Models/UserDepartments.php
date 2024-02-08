<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDepartments extends Model {
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'department_id'
    ];

    protected $hidden = ['deleted_at'];

    public function user() {
        return $this->belongsTo(UserProfiles::class, 'user_id', 'user_id');
    }

    public function department() {
        return $this->hasOne(Departments::class, 'id', 'department_id');
    }

    public function setNewUserDepartment($data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        $this->save();
        return $this;
    }
}
