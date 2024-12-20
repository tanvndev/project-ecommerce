<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\User;

use App\Models\User;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Services\BaseService;
use App\Services\Interfaces\User\UserServiceInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }

    public function paginate($userCatalogueId = null)
    {
        $request = request();

        $condition = [
            'search'       => addslashes($request->search),
            'publish'      => $request->publish,
            'searchFields' => ['fullname', 'email', 'phone'],
        ];

        if ($userCatalogueId) {
            $condition['where'] = [
                'user_catalogue_id' => $userCatalogueId,
            ];
        }

        $select = ['id', 'fullname', 'email', 'phone', 'publish', 'user_catalogue_id', 'image'];
        $pageSize = $request->pageSize;

        $data = $pageSize && $request->page
            ? $this->userRepository->pagination($select, $condition, $pageSize, [], [], ['user_catalogue'])
            : $this->userRepository->all($select);

        return $data;
    }

    public function create()
    {
        return $this->executeInTransaction(function () {
            $payload = request()->except('_token', '_method');
            $payload = $this->formatPayload($payload);
            $this->userRepository->create($payload);

            return successResponse(__('messages.create.success'));
        }, __('messages.create.error'));
    }

    private function formatPayload(array $payload): array
    {
        $request = request();
        $payload['password']          = Hash::make($payload['password']);
        $payload['user_agent']        = $request->header('User-Agent');
        $payload['ip']                = $request->ip();
        $payload['email_verified_at'] = now()->toDateTimeString();

        return $payload;
    }

    public function update($id)
    {
        return $this->executeInTransaction(function () use ($id) {

            if ($id == User::ROLE_ADMIN) {
                throw new Exception('AuthorizationException', 500);
            }

            $payload = request()->except('_token', '_method');
            $this->userRepository->update($id, $payload);

            return successResponse(__('messages.update.success'));
        }, __('messages.update.error'));
    }

    public function destroy($id)
    {
        return $this->executeInTransaction(function () use ($id) {
            if ($id == User::ROLE_ADMIN) {
                throw new Exception('AuthorizationException', 403);
            }

            $this->userRepository->delete($id);

            return successResponse(__('messages.delete.success'));
        }, __('messages.delete.error'));
    }

    public function updateProfile()
    {
        return $this->executeInTransaction(function () {

            $payload = request()->except('_token', '_method');
            $user = Auth::user();

            if ( ! request()->has('fullname') && ! request()->has('birthday')) {
                $verificationCode = Cache::get('verification_code_' . $user->phone);

                if ( ! $verificationCode) {
                    return errorResponse(__('messages.auth.invalid_code.error'));
                }
            }

            $this->userRepository->update($user->id, $payload);

            Cache::forget('verification_code_' . $user->phone);

            return successResponse(__('messages.auth.update_profile.success'));
        }, __('messages.auth.update_profile.error'));
    }
}
