<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSchools extends Model {
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'school_id'
    ];

    protected $hidden = ['deleted_at'];

    public function school() {
        return $this->belongsTo(Schools::class, 'school_id', 'id');
    }

    public function setNewUserSchool($data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        $this->save();
        return $this;
    }
}
