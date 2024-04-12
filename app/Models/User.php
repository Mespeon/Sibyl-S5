<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject {
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'status_id',
        'account_verified_at',
        'last_login',
        'last_password_change'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'account_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'last_password_change' => 'datetime',
        'password' => 'hashed',
    ];

    // Serializes and returns timestamps as is.
    protected function serializeDate($date) {
        return $date->format('Y-m-d H:i:s');
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        // Encode user roles into token.
        $role = $this->load('role:user_id,role_id');
        $roleRemapped = array_map(function ($item) {
            return $item['role_id'];
        }, $role['role']->toArray());

        return [
            'roles' => $roleRemapped
        ];
    }

    public function account_status() {
        return $this->hasOne(AccountStatuses::class, 'id', 'status_id')->select([
            'id',
            'key',
            'name'
        ]);
    }

    public function role() {
        return $this->hasMany(UserRoles::class, 'user_id', 'id')->select([
            'user_roles.id',
            'user_id',
            'role_id'
        ]);
    }

    public function profile() {
        return $this->hasOne(UserProfiles::class, 'user_id', 'id')->select([
            'id',
            'user_id',
            'first_name',
            'middle_name',
            'last_name',
            'email_address',
            'contact_number'
        ]);
    }

    public function student_profile() {
        return $this->hasOne(UserStudentProfiles::class, 'user_id', 'id');
    }

    public function setNewUser($data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        $this->save();
        return $this;
    }

    public function deleteUserAccount($data) {
        return $this->where('id', $data['user_id'])->delete();
    }
}
