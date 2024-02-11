<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Services\AuthorizationService;
use App\Services\PermissionsService;

use App\Exceptions\UnprocessableEntityException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ServerErrorException;

class AdministratorController extends Controller {
    protected $authorizationService, $permissionsService;

    public function __construct(
        AuthorizationService $authorizationService,
        PermissionsService $permissionsService
    ) {
        $this->authorizationService = $authorizationService;
        $this->permissionsService= $permissionsService;

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
