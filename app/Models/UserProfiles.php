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

    public function userAccount() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function setNewUserProfile($data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        $this->save();
        return $this;
    }
}
