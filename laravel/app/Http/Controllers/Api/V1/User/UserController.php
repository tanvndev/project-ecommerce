<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Enums\ResponseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserProfileRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Services\Interfaces\User\UserServiceInterface;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $userService;

    protected $userRepository;

    public function __construct(
        UserServiceInterface $userService,
        UserRepositoryInterface $userRepository
    ) {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $this->authorize('modules', 'users.index');

        $paginator = $this->userService->paginate(User::ROLE_CUSTOMER);
        $data = new UserCollection($paginator);

        return successResponse('', $data, true);
    }

    public function listStaff(): JsonResponse
    {
        $this->authorize('modules', 'users.staff.index');

        $paginator = $this->userService->paginate(User::ROLE_STAFF);
        $data = new UserCollection($paginator);

        return successResponse('', $data, true);
    }

    public function listAdmin(): JsonResponse
    {
        $this->authorize('modules', 'users.admin.index');

        $paginator = $this->userService->paginate(User::ROLE_ADMIN);
        $data = new UserCollection($paginator);

        return successResponse('', $data, true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->authorize('modules', 'users.store');

        $response = $this->userService->create();

        return handleResponse($response, ResponseEnum::CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize('modules', 'users.show');

        $response = new UserResource($this->userRepository->findById($id));

        return successResponse('', $response, true);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $this->authorize('modules', 'users.update');

        $response = $this->userService->update($id);

        return handleResponse($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize('modules', 'users.destroy');

        $response = $this->userService->destroy($id);

        return handleResponse($response);
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function updateProfile(UpdateUserProfileRequest $request): JsonResponse
    {
        $response = $this->userService->updateProfile();

        return handleResponse($response);
    }
}
