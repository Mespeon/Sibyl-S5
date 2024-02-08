<?php
namespace App\Exceptions;

use App\Exceptions\BaseException;
use Illuminate\Support\Arr;

class UnprocessableEntityException extends BaseException {
    public function __construct($message = '', ...$errors) {
        if ($errors && is_array($errors)) {
            $errorRemap = Arr::map($errors[0], function ($item, $key) {
                return $item[0];
            });
            $errors = $errorRemap;
        }
        parent::__construct($message, 422, $errors);
    }
}