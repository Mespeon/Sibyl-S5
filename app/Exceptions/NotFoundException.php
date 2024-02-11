<?php

namespace App\Exceptions;

use App\Exceptions\BaseException;

class NotFoundException extends BaseException {
    public function __construct($message) {
        parent::__construct($message, 404);
    }
}
