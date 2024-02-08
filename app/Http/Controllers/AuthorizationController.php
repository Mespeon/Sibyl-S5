<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

use App\Services\AuthorizationService;

use App\Models\User;

use App\Exceptions\UnprocessableEntityException;
use App\Exceptions\ServerErrorException;

class AuthorizationController extends Controller {
    protected $users, $authorizationService;

    public function __construct(
        User $users,
        AuthorizationService $authorizationService
    ) {
        $this->users = $users;
        $this->authorizationService = $authorizationService;

        $this->middleware('auth:api', [
            'except' => [
                'generateGuestAccessToken',
                'register',
                'login'
            ]
        ]);
    }

    /**
     * Generate Guest Access Token
     * 
     * @unauthenticated
     * @verb GET
     * @path api/auth/guest
     * @param Request
     * @return Response
     */
    public function generateGuestAccessToken(Request $request) {
        $response = $this->getBlankResponse();
        $status = 200;
        
        try {
            $this->validate($request, []);
        }
        catch (ValidationException $e) {
            throw new UnprocessableEntityException('Unable to process request due to incorrect input.', $e->errors());
        }

        try {
            // Generate custom claims.
            $claims = $this->authorizationService->generateGuestAccessTokenClaims([
                'subject' => sha1(Date::now()->toDateTimeString()),
                'validity' => 1
            ]);

            $token = $this->authorizationService->constructAccessToken($claims);

            $response = [
                'message' => 'Guest access token created.',
                'data' => [
                    'guest_access_token' => $token['token']
                ]
            ];
        }
        catch (\Exception $e) {
            throw new ServerErrorException('A server error has occurred while handling this request.', $request->path(), $e->getMessage());
        }

        return response()->json($response, $status);
    }

    /**
     * Register
     * 
     * @unauthenticated
     * @verb POST
     * @path api/auth/register
     * @param Request
     * @return Response
     */
    public function register(Request $request) {
        $response = $this->getBlankResponse();
        $status = 200;

        try {
            $this->validate($request, [
                // Desired username
                'username' => 'required_if:is_third_party_login,0|string|min:8|max:16|unique:users,deleted_at,NULL',
                // Desired password
                'password' => [
                    'required_if:is_third_party_login,0',
                    'string',
                    'min:8',
                    'max:32',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*#?&]/',
                    'confirmed'
                ],
                // Password confirmation - should match with password.
                'password_confirmation' => 'required_if:is_third_party_login,0|string|same:password',
                // First name of user
                'first_name' => 'required|string|max:100',
                // Optional - middle name of user
                'middle_name' => 'nullable|string|max:100',
                // Last name of user
                'last_name' => 'required|string|max:100',
                // Email address of user
                'email_address' => 'required|email',
                // Contact number of user
                'contact_number' => 'required|string',
                // Parent department of user
                'department' => 'required|numeric',
                // Role of registering user
                'role' => 'required|numeric',
                // Flag whether this registration is from a third-party login.
                'is_third_party_login' => 'required|boolean',
                // Course taken by the user. Required if role is Student.
                'course' => 'required_if:role,3|numeric',
                // Current year of the user. Required if role is Student.
                'year' => 'required_if:role,3|numeric',
                // Section of the user. Required if role is Student.
                'section' => 'required_if:role,3|string'
            ]);
        }
        catch (ValidationException $e) {
            throw new UnprocessableEntityException('Unable to process request due to incorrect input.', $e->errors());
        }

        try {
            // Attempt to create the account, profile, role, and department rows for this user.
            $createFullAccount = $this->authorizationService->createUserAccount($request);
            // If the registration is for a student, we may fork an additional call to built the student's student_profile.
        }
        catch (\Exception $e) {
            throw new ServerErrorException('A server error has occurred while handling this request.', $request->path(), $e->getMessage());
        }

        return response()->json($response, $status);
    }

    public function login(Request $request) {
        $response = $this->getBlankResponse();
        $status = 200;

        try {
            $this->validate($request, [
                'username' => 'required|string',
                'password' => 'required|string'
            ]);
        }
        catch (ValidationException $e) {
            throw new UnprocessableEntityException('Unable to process request due to incorrect input.', $e->errors());
        }

        try {

        }
        catch (\Exception $e) {
            throw new ServerErrorException('A server error has occurred while handling this request.', $request->path(), $e->getMessage());
        }

        return response()->json($response, $status);
    }
}