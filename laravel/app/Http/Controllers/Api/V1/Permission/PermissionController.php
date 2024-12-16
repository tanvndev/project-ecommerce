<?php

namespace App\Http\Controllers\Api\V1\Permission;

use App\Enums\ResponseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\Permission\PermissionCollection;
use App\Http\Resources\Permission\PermissionResource;
use App\Repositories\Interfaces\Permission\PermissionRepositoryInterface;
use App\Services\Interfaces\Permission\PermissionServiceInterface;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    protected $permissionService;

    protected $permissionRepository;

    public function __construct(
        PermissionServiceInterface $permissionService,
        PermissionRepositoryInterface $permissionRepository
    ) {
        $this->permissionService = $permissionService;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Display a listing of the permissions.
     */
    public function index(): JsonResponse
    {
        $this->authorizePermission('permissions.index');

        $paginator = $this->permissionService->paginate();
        $data = new PermissionCollection($paginator);

        return successResponse('', $data, true);
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        $this->authorizePermission('permissions.store');

        $response = $this->permissionService->create();

        return handleResponse($response, ResponseEnum::CREATED);
    }

    /**
     * Display the specified permission.
     */
    public function show(string $id): JsonResponse
    {
        $this->authorizePermission('permissions.show');

        $response = new PermissionResource($this->permissionRepository->findById($id));

        return successResponse('', $response, true);
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(UpdatePermissionRequest $request, string $id): JsonResponse
    {
        $this->authorizePermission('permissions.update');

        $response = $this->permissionService->update($id);

        return handleResponse($response);
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorizePermission('permissions.destroy');

        $response = $this->permissionService->destroy($id);

        return handleResponse($response, true);
    }
}
