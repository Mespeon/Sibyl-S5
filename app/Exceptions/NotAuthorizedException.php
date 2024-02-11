<?php

namespace App\Exceptions;

use App\Exceptions\BaseException;

class NotAuthorizedException extends BaseException {
    public function __construct($message) {
        parent::__construct($message, 401);
    }
}
