<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Validation\ValidationException;

use App\Services\AuthorizationService;
use App\Services\UserService;
use App\Exceptions\UnprocessableEntityException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ServerErrorException;
use App\Exceptions\NotAuthorizedException;

class UserController extends Controller {

    public function __construct(
        protected AuthorizationService $authorizationService,
        protected UserService $userService
    ) {
        $this->middleware('auth:api', [
            'except' => []
        ]);
    }
    
    /**
     * Get Profile
     * 
     * Retrieves user profile.
     * 
     * @authenticated
     * @verb GET
     * @path api/v1/user/profile
     * @param Request
     * @return Response
     */
    public function getProfile(Request $request) {
        $response = $this->getBlankResponse();
        $status = 200;

        try {
            $this->validate($request, []);

            // Retrieve user ID from payload.
            $userId = Auth::payload()->get('sub');
            $roles = Auth::payload()->get('roles');

            $request['user_id'] = $userId;
            $request['roles'] = $roles;
            $profile = $this->userService->profile($request->all());
            $response = [
                'message' => 'Profile retrieved.',
                'data' => $profile
            ];
        }
        catch (ValidationException $ve) {
            throw new UnprocessableEntityException('Unable to process request due to incorrect input.', $ve->errors());
        }
        catch (\Exception $e) {
            throw new ServerErrorException('A server error has occurred while handling this request.', $request->path(), $e->getMessage());
        }

        return response()->json($response, $status);
    }

    /**
     * Update Profile
     * 
     * Updates a user profile.
     * 
     * @authenticated
     * @verb POST
     * @path api/v1/user/profile/update
     * @param Request $request
     * @return Response
     */
    public function updateProfile(Request $request) {
        $response = $this->getBlankResponse();
        $status = 200;

        try {
            $this->validate($request, [
                'first_name' => 'required|string|max:50',
                'middle_name' => 'nullable|string',
                'last_name' => 'required|string|max:50',
                'contact_number' => 'required|string'
            ]);

            // Get user ID from payload.
            $userId = Auth::payload()->get('sub');

            // Update profile.
            $updateProfile = $this->userService->updateProfile($userId, $request->all());
            
            $response = [
                'message' => 'Profile updated.',
                'data' => $updateProfile
            ];
        }
        catch (ValidationException $ve) {
            throw new UnprocessableEntityException('Unable to process request due to incorrect input.', $ve->errors());
        }
        catch (\Exception $e) {
            throw new ServerErrorException('A server error has occurred while handling this request.', $request->path(), $e->getMessage());
        }

        return response()->json($response, $status);
    }

    /**
     * Change Password
     * 
     * Changes an account's password from an authenticated state.
     * 
     * Force logouts a user once completed.
     * 
     * @authenticated
     * @verb POST
     * @path api/v1/user/account/change-password
     * @param Request
     * @return Response
     */
    public function changePassword(Request $request) {
        $response = $this->getBlankResponse();
        $status = 200;

        try {
            $this->validate($request, [
                // Old password
                'old_password' => 'required|string',
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
                'password_confirmation' => 'required|string|same:password'
            ]);

            // Get user ID from token.
            $userId = Auth::payload()->get('sub');

            // Validate existing password with the one sent.
            $verify = $this->authorizationService->verifyPassword([
                'user_id' => $userId,
                'current_password' => $request->old_password
            ]);
            if (!$verify) {
                throw new NotAuthorizedException('Old password is incorrect.');
            }

            // Change password.
            $changePassword = $this->authorizationService->updateUserPassword([
                'user_id' => $userId,
                'password' => $request->password
            ]);

            // Return a success response then invalidate current token.
            $response['message'] = 'Your password has been changed. Please log in again using your new password.';
            Auth::logout(true);
        }
        catch (NotAuthorizedException $nae) {
            throw new NotAuthorizedException($nae->getMessage());
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
