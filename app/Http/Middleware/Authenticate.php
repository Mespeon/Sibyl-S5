<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class Authenticate extends Middleware {
    // Override the Authenticate handle method.
    public function handle($request, Closure $next, ...$guards) {
        if ($this->auth->guard('api')->guest()) {
            // Check if the issued guest token is valid.
            // TO-DO: validate an API key hash in header.
            // TO-DO: perform additional validation on guest access tokens.
            try {
                $authFacade = new AuthFacade;
                $audience = $authFacade::payload()->get('aud');
            }
            catch (JWTException $jwtException) {
                return response()->json([
                    'message' => 'Unauthenticated - authentication error.'
                ], 401);
            }
        }
        else {
            // Forward this request and token to the user authentication.
            $this->authenticate($request, $guards);
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string {
        // return $request->expectsJson() ? null : route('login');
        abort(response()->json([
            'message' => 'Unauthenticated - trying to access protected path and redirected.'
        ], 401));
    }

    // Returns a JSON response for unauthenticated requests to JWT-protected paths.
    protected function unauthenticated($request, array $guards) {
        abort(response()->json([
            'message' => 'Unauthenticated - trying to access protected endpoint.'
        ], 401));
    }
}
