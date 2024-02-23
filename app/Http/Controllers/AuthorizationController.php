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
use App\Exceptions\NotFoundException;
use App\Jobs\DispatchForgotPasswordEmail;
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
                'login',
                'forgotPassword',
                'testRecordAttendance'
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
                'username' => 'required_if:is_third_party_login,0|string|min:8|max:16|unique:users',
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
                'first_name' => 'required|string|max:50',
                // Optional - middle name of user
                'middle_name' => 'nullable|string|max:50',
                // Last name of user
                'last_name' => 'required|string|max:50',
                // Email address of user
                'email_address' => 'required|email|unique:user_profiles',
                // Contact number of user
                'contact_number' => 'required|string',
                // School that this user belongs to
                'school' => 'required_if:role,3|required_if:role,4|numeric',
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
            // If user is registering as a student, create a student profile for them as well.
            $createFullAccount = $this->authorizationService->createUserAccount($request);
            $response = [
                'message' => 'Account registered.',
                'data' => $createFullAccount
            ];
        }
        catch (\Exception $e) {
            throw new ServerErrorException('A server error has occurred while handling this request.', $request->path(), $e->getMessage());
        }

        return response()->json($response, $status);
    }

    /**
     * Login
     * 
     * @unauthenticated
     * @verb POST
     * @path api/v1/login
     * @param Request
     * @return Response
     */
    public function login(Request $request) {
        $response = $this->getBlankResponse();
        $status = 200;

        try {
            $this->validate($request, [
                'username' => 'required|string',
                'password' => 'required_if:third_party_login,0|nullable|string',
                'third_party_login' => 'required|boolean',
                'third_party_type' => 'required_if:third_party_login,1|string'
            ]);
        }
        catch (ValidationException $e) {
            throw new UnprocessableEntityException('Unable to process request due to incorrect input.', $e->errors());
        }

        try {
            // Attempt to login the user.
            if (!$this->authorizationService->findUser($request->username)) {
                throw new NotFoundException('Account is not registered.');
            }

            $credentials = $request->only(['username', 'password']);
            if (!$token = auth()->attempt($credentials)) {
                $response['message'] = 'Incorrect username or password.';
                return response()->json($response, 401);
            }

            $accessToken = $this->authorizationService->respondWithToken($token);

            $response = [
                'message' => 'Successfully logged in.',
                'data' => $accessToken->original
            ];
        }
        catch (NotFoundException $notFound) {
            throw new NotFoundException($notFound->getMessage());
        }
        catch (\Exception $e) {
            throw new ServerErrorException('A server error has occurred while handling this request.', $request->path(), $e->getMessage());
        }

        return response()->json($response, $status);
    }

    /**
     * Logout
     * 
     * @authenticated
     * @verb POST
     * @path api/v1/auth/logout
     * @param Request
     * @return Response
     */
    public function logout(Request $request) {
        $response = $this->getBlankResponse();
        $status = 200;

        try {
            // Logout user then invalidate/blacklist the token.
            Auth::logout(true);
            $response['message'] = 'Logged out.';
        }
        catch (\Exception $e) {
            throw new ServerErrorException('A server error has occurred while handling this request.', $request->path(), $e->getMessage());
        }

        return response()->json($response, $status);
    }

    /**
     * Forgot Password
     * 
     * Begins a forgot password process flow for the user.
     * 
     * @unauthenticated
     * @verb POST
     * @path api/v1/auth/forgot-password
     * @param Request
     * @return Response
     */
    public function forgotPassword(Request $request) {
        $response = $this->getBlankResponse();
        $status = 200;

        try {
            // Validate request.
            $this->validate($request, [
                'email_address' => 'required|email'
            ]);

            // Check if email address exists.
            if (!$this->authorizationService->findUserByEmail($request->email_address)) {
                throw new NotFoundException('Email address is not registered, or may be incorrect.');
            }

            // Retrieve user data.
            $userProfile = $this->authorizationService->retrieveUserByEmail($request->email_address);

            // Generate a password reset token for this user.
            $passwordResetClaims = $this->authorizationService->generatePasswordResetAccessToken([
                'subject' => $userProfile->first()['user_id'],
                'validity' => 15
            ]);

            $passwordResetToken = $this->authorizationService->constructAccessToken($passwordResetClaims);
            $rehashedToken = md5($passwordResetToken['token']);

            // Record password reset token.
            $recordPasswordResetParams = [
                'user_id' => $userProfile->first()['user_id'],
                'email_address' => $request->email_address,
                'token' => $passwordResetToken['token']
            ];
            $this->authorizationService->setNewPasswordResetToken($recordPasswordResetParams);

            $response['message'] = 'An email containing a password reset link is sent to the address provided.';

            // Queue the email using jobs for asynchronous processing.
            DispatchForgotPasswordEmail::dispatch($request->email_address, $rehashedToken)->delay(Date::now()->addSeconds(5));
        }
        catch (ValidationException $ve) {
            throw new UnprocessableEntityException('Unable to process request due to incorrect input.', $ve->errors());
        }
        catch (NotFoundException $nfe) {
            throw new NotFoundException($nfe->getMessage());
        }
        catch (\Exception $e) {
            throw new ServerErrorException('A server error has occurred while handling this request.', $request->path(), $e->getMessage());
        }

        return response()->json($response, $status);
    }

    /**
     * Reset Password
     * 
     * Resets password from a forgot password process flow.
     * 
     * @authenticated
     * @verb POST
     * @path api/v1/reset-password
     * @param Request
     * @param Response
     */
    public function resetPassword(Request $request) {
        $response = $this->getBlankResponse();
        $status = 200;

        try {
            // Validate request.
            $this->validate($request, [
                // Desired password
                'password' => [
                    'required',
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
                'password_confirmation' => 'required|string|same:password',
            ]);

            // If it passes validation, take the user ID from the token then update the user's password.
            $userToken = Auth::payload()->get('sub');
            $updateUserPassword = $this->authorizationService->updateUserPassword([
                'user_id' => $userToken,
                'password' => $request->password
            ]);

            $response['message'] = 'Password has been reset. You may now login using your new password.';

            // Invalidate the sent token.
            Auth::invalidate(true);
        }
        catch (ValidationException $ve) {
            throw new UnprocessableEntityException('Unable to process request due to incorrect input.', $ve->errors());
        }
        catch (\Exception $e) {
            throw new ServerErrorException('A server error has occurred while handling this request.', $request->path(), $e->getMessage());
        }

        return response()->json($response, $status);
    }
}