<?php
namespace App\Exceptions;

use Exception;

class BaseException extends Exception {
    protected $code, $statusCode, $type, $errors, $path, $trace;

    public function __construct($message, $statusCode = 500, $errors = [], $code = 0, $type = '', $path = '', $trace = '') {
        parent::__construct($message, $statusCode);
        $this->code = $code;
        $this->statusCode = $statusCode;
        $this->type = $type;
        $this->errors = $errors;
        $this->path = $path;
        $this->trace = $trace;
    }

    public function getCustomCode() {
        return $this->code;
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function getCustomType() {
        return $this->type;
    }

    public function getPath() {
        return $this->path;
    }

    public function getCustomTrace() {
        return $this->trace;
    }

    public function getErrors() {
        return $this->errors;
    }
}