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

    // Serializes and returns timestamps as is.
    protected function serializeDate($date) {
        return $date->format('Y-m-d H:i:s');
    }

    public function user_account() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function setNewUserProfile($data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        $this->save();
        return $this;
    }
}
