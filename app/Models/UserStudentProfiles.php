<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserStudentProfiles extends Model {
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'course_id',
        'year_level',
        'section'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function setNewUserStudentProfile($data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        $this->save();
        return $this;
    }
}
