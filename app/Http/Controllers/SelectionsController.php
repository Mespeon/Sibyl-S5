<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

use App\Services\SelectionsService;

use App\Exceptions\UnprocessableEntityException;
use App\Exceptions\ServerErrorException;

class SelectionsController extends Controller {
    protected $selectionService;

    public function __construct(SelectionsService $selectionService) {
        $this->selectionService = $selectionService;
    }

    /**
     * Get Selection
     * 
     * @unauthenticated
     * @verb GET
     * @path api/v1/selections
     * @param Request
     * @return Response
     */
    public function getSelection(Request $request) {
        $response = $this->getBlankResponse();
        $status = 200;

        try {
            $this->validate($request, [
                'get' => [
                    'required',
                    Rule::in([
                        'roles',
                        'statuses',
                        'departments',
                        'courses',
                        'schools',
                        'countries',
                        'regions',
                        'provinces',
                        'municipalities',
                        'barangays'
                    ])
                ],
                'search' => 'sometimes|string',
                'country_id' => 'required_if:get,regions',
                'region_code' => 'required_if:get,provinces',
                'province_code' => 'required_if:get,municipalities',
                'municipality_code' => 'required_if:get,barangays'
            ]);
        }
        catch (ValidationException $e) {
            throw new UnprocessableEntityException('Unable to process request due to incorrect input.', $e->errors());
        }

        try {
            // Get the desired collection.
            $collection = $request->get;
            switch ($collection) {
                case 'roles':
                    $response['data'] = $this->selectionService->getRoles($request->all());
                    break;

                case 'statuses':
                    $response['data'] = $this->selectionService->getAccountStatuses($request->all());
                    break;
                
                case 'departments':
                    $response['data'] = $this->selectionService->getDepartments($request->all());
                    break;

                case 'courses':
                    $response['data'] = $this->selectionService->getCourses($request->all());
                    break;

                case 'schools':
                    $response['data'] = $this->selectionService->getSchools($request->all());
                    break;

                case 'countries':
                    $response['data'] = $this->selectionService->getCountries($request->all());
                    break;

                case 'regions':
                    $response['data'] = $this->selectionService->getRegions($request->all());
                    break;

                case 'provinces':
                    $response['data'] = $this->selectionService->getProvinces($request->all());
                    break;

                case 'municipalities':
                    $response['data'] = $this->selectionService->getMunicipalities($request->all());
                    break;

                case 'barangays':
                    $response['data'] = $this->selectionService->getBarangays($request->all());
                    break;

                default:
                    $response['message'] = 'No collection retrieved.';
            }

            $response['message'] = 'Collection retrieved.';
            $response['collection'] = $request->get;
        }
        catch (\Exception $e) {
            throw new ServerErrorException('A server error has occurred while handling this request.', $request->path(), $e->getMessage());
        }

        return response()->json($response, $status);
    }
}
