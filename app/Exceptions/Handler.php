<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Exceptions\BaseException;
use Throwable;

class Handler extends ExceptionHandler
{

    protected $dontReport = [
        BaseException::class
    ];

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (BaseException $exception, $request) {
            $response = [
                'code' => 0,
                'type' => '',
                'message' => $exception->getMessage(),
                'path' => $exception->getPath(),
                'trace' => $exception->getCustomTrace(),
                'errors' => $exception->getErrors()
            ];

            if (empty($exception->getCustomCode())) {
                unset($response['code']);
            }

            if (empty($exception->getCustomType())) {
                unset($response['type']);
            }

            if (empty($exception->getPath())) {
                unset($response['path']);
            }

            if (empty($exception->getCustomTrace())) {
                unset($response['trace']);
            }

            if (empty($exception->getErrors())) {
                unset($response['errors']);
            }

            return response()->json($response, $exception->getStatusCode());
        });
    }
}
