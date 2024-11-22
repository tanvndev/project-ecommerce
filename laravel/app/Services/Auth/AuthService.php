<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\Auth;

use App\Events\Auth\AuthForgotEvent;
use App\Events\Auth\AuthRegisteredEvent;
use App\Models\User;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Services\BaseService;
use App\Services\Interfaces\Auth\AuthServiceInterface;
use Illuminate\Support\Facades\Hash;

class AuthService extends BaseService implements AuthServiceInterface
{
    protected $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * Dang ky tai khoan moi.
     *
     * @return JsonResponse
     */
    public function register()
    {
        return $this->executeInTransaction(function () {
            $request = request();

            $user = null;
            // Kiem tra xem nguoi dung da dang ky truoc do chua
            $user = $this->userRepository->findByWhere(
                [
                    'email' => ['=', $request->email],
                ]
            );

            if ( ! empty($user)) {
                if ($user->hasVerifiedEmail()) {
                    return errorResponse(__('messages.auth.register.email_verified'));
                }
                $user->delete();
            }

            $user = $this->userRepository->create([
                'fullname'          => $request->fullname,
                'user_catalogue_id' => User::ROLE_CUSTOMER,
                'email'             => $request->email,
                'ip'                => $request->ip(),
                'user_agent'        => $request->header('User-Agent'),
                'password'          => Hash::make($request->password),
            ]);

            // Send email verification notification
            event(new AuthRegisteredEvent($user));

            return successResponse(__('messages.auth.register.success'));
        }, __('messages.auth.register.error'));
    }

    /**
     * Handle password reset for a user.
     *
     * @return JsonResponse
     */
    public function resetPassword()
    {
        return $this->executeInTransaction(function () {
            $user = $this->userRepository->findByWhere(
                [
                    'email' => ['=', request()->email],
                ]
            );
            $randomPassword = generateStrongPassword();
            $user->password = Hash::make($randomPassword);
            $user->save();
            $user->newPassword = $randomPassword;

            // Send email verification notification
            event(new AuthForgotEvent($user));

            return successResponse(__('messages.auth.reset_password.success'));
        }, __('messages.auth.reset_password.error'));
    }
}
