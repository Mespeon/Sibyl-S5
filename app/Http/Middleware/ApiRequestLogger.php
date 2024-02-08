<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

use App\Models\ApiRequests;

class ApiRequestLogger {
    protected $apiRequests;

    public function __construct(ApiRequests $apiRequests) {
        $this->apiRequests = $apiRequests;
    }

    public function handle(Request $request, Closure $next) {
        return $next($request);
    }

    public function terminate(Request $request, $response) {
        $requestBody = $request->isMethod('post') ? $request->all() : $request->query();
        // Unset the following from the request body, if present.
        unset($requestBody['password'],
        $requestBody['password_confirmation'],
        $requestBody['current_password']
        );

        $logParams = [
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'request_ip' => $request->ip(),
            'request_header' => json_encode($request->header()),
            'request_body' => json_encode($requestBody),
            'agent' => $request->header('User-Agent'),
            'response' => json_encode($response),
            'response_status' => $response->status()
        ];

        $this->apiRequests->setNewApiRequestLog($logParams);
    }
}
