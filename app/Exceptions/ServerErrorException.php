<?php
namespace App\Exceptions;

use App\Exceptions\BaseException;

class ServerErrorException extends BaseException {
    public function __construct($message, $path, $trace) {
        parent::__construct($message, 500, null, null, null, $path, $trace);
    }
}