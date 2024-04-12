<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Services\AuthorizationService;
use App\Services\PermissionsService;
use App\Services\SelectionsService;
use App\Services\UserService;

use App\Exceptions\UnprocessableEntityException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ServerErrorException;

class AdministratorController extends Controller {
    protected $authorizationService, $permissionsService, $selectionService, $userService;

    public function __construct(
        AuthorizationService $authorizationService,
        PermissionsService $permissionsService,
        SelectionsService $selectionService,
        UserService $userService
    ) {
        $this->authorizationService = $authorizationService;
        $this->permissionsService= $permissionsService;
        $this->selectionService = $selectionService;
        $this->userService = $userService;

        $this->middleware('auth:api');
    }

    /**
     * Retrieve Roles
     * 
     * Gets all available roles in the system.
     * 
     * @authenticated
     * @verb GET
     * @path api/v1/admins/roles
     * @param Request
     * @param string role_id
     * @param string search
     * @param string filter_by
     * @param string order_by
     * @param string daterange
     * @param integer page
     * @param integer list_size
     * @return Response
     */
    public function retrieveRoles(Request $request) {
        $response = $this->getBlankResponse();
        $status = 200;

        try {
            $this->validate($request, [
                'role_id' => 'string',
                'search' => 'string',
                'filter_by' => 'string',
                'order_by' => 'string',
                'daterange' => 'string',
                'page' => 'numeric',
                'list_size' => 'numeric'
            ]);

            $roles = $this->selectionService->getRoles($request->all());
            $response = [
                'message' => 'User roles retrieved.',
                'data' => $roles
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
     * Create New Role
     * 
     * Creates a new role to which permissions could be attached.
     * 
     * @authenticated
     * @verb POST
     * @path api/v1/admin/roles/add
     * 
     * @param Request
     * @return Response
     */
    public function createNewRole(Request $request) {
        $response = $this->getBlankResponse();
        $status = 200;

        try {

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
     * Get Users
     * 
     * Retrieves a list of all system users.
     * 
     * @authenticated
     * @verb POST
     * @path api/v1/admin/users
     * 
     * @param Request
     * @param string search
     * @param string filter_by
     * @param string order_by
     * @param string daterange
     * @param string page
     * @param string list_size
     * @return Response
     */
    public function getUsers(Request $request) {
        $response = $this->getBlankResponse();
        $status = 418;

        try {
            $this->validate($request, [
                'search' => 'string',
                'filter_by' => 'string',
                'order_by' => 'string',
                'daterange' => 'string',
                'page' => 'numeric',
                'list_size' => 'numeric'
            ]);

            // Attempt to retrieve users.
            $usersList = $this->userService->retrieveUsersList($request->all(), $request->query());

            $response['message'] = 'Users list retrieved.';
            $response = array_merge($response, $usersList);
            $status = 200;
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
     * Create User
     * 
     * Creates a new user. This should follow the same procedure as registering for an account.
     * 
     * @authenticated
     * @verb POST
     * @path api/v1/admin/users/create
     * 
     * @param Request
     * @return Response
     */
    public function createUser(Request $request) {
        $response = $this->getBlankResponse();
        $status = 418;

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
                // Role of registering user
                'role' => 'required|numeric',
                // Flag whether this registration is from a third-party login.
                'is_third_party_login' => 'required|boolean'
            ]);

            // Attempt to create the account.
            $createFullAccount = $this->authorizationService->createUserAccount($request);
            $response = [
                'message' => 'Account created. A password reset may be required for the user.',
                'data' => $createFullAccount
            ];
            $status = 200;
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
     * Update User
     * 
     * Updates a user profile and account details. Performs the same function as a user profile update.
     * 
     * @authenticated
     * @verb POST
     * @path api/v1/admin/users/update
     * 
     * @param Request
     * @return Response
     */
    public function updateUser(Request $request) {
        $response = $this->getBlankResponse();
        $status = 418;

        try {
            $this->validate($request, [
                'user_id' => 'required',
                'first_name' => 'required|string|max:50',
                'middle_name' => 'nullable|string',
                'last_name' => 'required|string|max:50',
                'contact_number' => 'required|string'
            ]);

            $status = 200;
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
     * Update User Account Status
     * 
     * Changes a user account status with a value from a list of predefined account statuses.
     * 
     * @authenticated
     * @verb POST
     * @path api/v1/admin/users/deactivate
     * 
     * @param Request
     * @return Response
     */
    public function updateUserAccountStatus(Request $request) {
        $response = $this->getBlankResponse();
        $status = 418;

        try {
            $this->validate($request, [

            ]);
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
     * Remove User Account
     * 
     * Removes a user account from the system. This performs a soft delete on the user's account record in the users table,
     * and should be adjusted to include more tables, or have the delete action cascade to them.
     * 
     * @authenticated
     * @verb POST
     * @path api/v1/admin/users/remove
     * 
     * @param Request
     * @return Response
     */
    public function removeUserAccount(Request $request) {
        $response = $this->getBlankResponse();
        $status = 418;

        try {
            $this->validate($request, [

            ]);
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
     * Reset User Password
     * 
     * Resets a user password without any notifications to the user. This is only meant to be used as a last-resort to regain account access,
     * and should never be used to deny user access to the system. A possible use-case is a user contacted the admins to have their password reset;
     * however, this should only be done after verifying that the user actually owns the account.
     * 
     * @authenticated
     * @verb POST
     * @path api/v1/admin/users/reset-password
     * 
     * @param Request
     * @return Response
     */
    public function resetUserPassword(Request $request) {
        $response = $this->getBlankResponse();
        $status = 418;

        try {
            $this->validate($request, [

            ]);
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
