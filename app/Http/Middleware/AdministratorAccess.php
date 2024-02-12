<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;


use App\Services\AuthorizationService;
use App\Exceptions\NotAuthorizedException;
use App\Exceptions\ServerErrorException;

class AdministratorAccess
{
    protected $authorizationService;
    
    public function __construct(AuthorizationService $authorizationService) {
        $this->authorizationService = $authorizationService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $payload = Auth::payload();
            // Check if user has an admin role, or if it exists.
            if (!$this->authorizationService->findUserRole(['user_id' => $payload['sub'], 'role_id' => 1]) || !in_array(1, $payload['roles'])) {
                throw new NotAuthorizedException('User is not allowed to access the endpoint.');
            }
        }
        catch (JWTException | NotAuthorizedException $notAuthorized) {
            throw new NotAuthorizedException($notAuthorized->getMessage());
        }
        catch (\Exception $e) {
            throw new ServerErrorException('An error has occurred while authorizing admin access.', $request->path(), $e->getMessage());
        }
        
        return $next($request);
    }
}
