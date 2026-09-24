<?php

namespace App\Http\Controllers\Api\V1\ServiceRequest;

use App\Actions\ServiceRequest\CreateServiceRequestAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest\StoreServiceRequestApiRequest;
use App\Http\Resources\ServiceRequest\ServiceRequestResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    /**
     * Display a listing of customer's own service requests.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 10);
        $user = $request->user();

        $requests = $user->serviceRequests()
            ->with(['address', 'photographs', 'videos'])
            ->latest('id')
            ->paginate($perPage);

        return ApiResponse::paginated(
            data: ServiceRequestResource::collection($requests),
            message: 'Service requests retrieved successfully.'
        );
    }

    /**
     * Submit a new customer service request.
     */
    public function store(StoreServiceRequestApiRequest $request, CreateServiceRequestAction $action): JsonResponse
    {
        $serviceRequest = $action->execute($request->user(), $request->validated());

        return ApiResponse::success(
            data: (new ServiceRequestResource($serviceRequest))->resolve(),
            message: 'Service request submitted successfully.',
            statusCode: 201
        );
    }

    /**
     * Display details of a specific service request.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $serviceRequest = $request->user()
            ->serviceRequests()
            ->with(['address', 'photographs', 'videos'])
            ->findOrFail($id);

        return ApiResponse::success(
            data: (new ServiceRequestResource($serviceRequest))->resolve(),
            message: 'Service request details retrieved successfully.'
        );
    }
}
