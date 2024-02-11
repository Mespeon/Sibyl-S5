<?php
namespace App\Services;

use App\Models\UserRoles;

use App\Exceptions\ServerErrorException;
class PermissionsService {
    protected $userRoles;

    public function __construct(UserRoles $userRoles) {
        $this->userRoles = $userRoles;
    }
}