<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Arr;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // This variable is only intended to be used as a tracking variable for messages used in responses.
    protected $responseMessages = [
        '422' => 'Unable to process request due to incorrect input.',
        '500' => 'A server error has occurred while handling this request.',
    ];

    public function getBlankResponse() {
        return ['message' => ''];
    }
}
