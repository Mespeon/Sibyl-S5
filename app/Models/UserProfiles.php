<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfiles extends Model {
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'email_address',
        'contact_number'
    ];

    protected $hidden = ['deleted_at'];

    public function user_account() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function student_profile() {
        return $this->hasOne(UserStudentProfiles::class, 'user_id', 'user_id');
    }

    public function faculty_profile() {
        return $this->hasOne(UserFacultyProfiles::class, 'user_id', 'user_id');
    }

    public function setNewUserProfile($data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        $this->save();
        return $this;
    }
}
