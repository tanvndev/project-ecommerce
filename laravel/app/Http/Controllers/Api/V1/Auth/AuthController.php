<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Classes\Stringee;
use App\Enums\ResponseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotRequest;
use App\Http\Requests\Auth\LoginOtpRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\Client\ClientUserResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\Interfaces\Auth\AuthServiceInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $authService;

    protected $currentUser = null;

    public function __construct(
        AuthServiceInterface $authService
    ) {
        $this->middleware('throttle:5,1')->only(['login', 'register', 'sendVerificationCode']);

        $this->currentUser = Auth::user();
        $this->authService = $authService;
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $this->verifyRecaptcha($request->input('g-recaptcha-response'));

            $response = $this->authService->register();

            return handleResponse($response, ResponseEnum::CREATED);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), true);
        }
    }

    /**
     * Log in an existing user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $response = $this->verifyRecaptcha($request->input('g-recaptcha-response'));

        if (! $response) {
            return errorResponse('Xác minh captcha không thành công', true);
        }

        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (! $user) {
            return errorResponse('Email hoặc mật khẩu không chính xác.', true);
        }

        if (! $user->hasVerifiedEmail()) {
            return errorResponse('Vui lòng xác nhận email của bạn trước khi đăng nhập.', true);
        }

        if ($token = JWTAuth::attempt($credentials)) {
            return $this->respondWithToken($token, 'Đăng nhập thành công.', $user);
        }

        return errorResponse('Email hoặc mật khẩu không chính xác.', true);
    }

    public function loginOtp(LoginOtpRequest $request): JsonResponse
    {
        $response = $this->verifyRecaptcha($request->input('g-recaptcha-response'));

        if (! $response) {
            return errorResponse('Xác minh captcha không thành công', true);
        }

        $credentials = $request->only('phone');

        $user = User::where('phone', $credentials['phone'])->first();

        $response = Stringee::verifyCode($request, $user);

        if ($response['status'] == 'error') {
            return errorResponse($response['messages'], true);
        }

        if (! $user->hasVerifiedEmail()) {
            return errorResponse('Vui lòng xác nhận email của bạn trước khi đăng nhập.', true);
        }

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token, 'Đăng nhập thành công.', $user);
    }

    /**
     * Verify the given reCAPTCHA token.
     *
     * @param  string  $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function verifyRecaptcha($token)
    {
        $secretKey = env('RECAPTCHA_SECRET_KEY');

        return Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $secretKey,
            'response' => $token,
        ])->json();
    }

    /**
     * Forgot password for a user.
     */
    public function forgotPassword(ForgotRequest $request): JsonResponse
    {
        $response = $this->authService->resetPassword();

        return handleResponse($response);
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        if ($this->currentUser->publish == 2) {
            return response()->json(null, ResponseEnum::FORBIDDEN);
        }

        if ($this->currentUser->email_verified_at == null) {
            return response()->json(null, ResponseEnum::FORBIDDEN);
        }

        $user =
            $this->currentUser->user_catalogue->id == User::ROLE_CUSTOMER
            ?
            new ClientUserResource($this->currentUser)
            :
            new UserResource($this->currentUser);

        return response()->json($user, ResponseEnum::OK);
    }

    /**
     * Refresh the token.
     */
    public function refreshToken(): JsonResponse
    {
        try {
            // Pass true as the first param to force the token to be blacklisted "forever".
            return $this->respondWithToken(Auth::refresh(true, true), 'Token đã được thay đổi');
        } catch (Exception $e) {
            return errorResponse('Token is Invalid', true);
        }
    }

    /**
     * Generate a response with a token.
     */
    private function respondWithToken(string $token, string $message, ?User $user = null): JsonResponse
    {
        return response()->json([
            'status'   => ResponseEnum::OK,
            'messages' => [$message],
            'data'     => [
                'access_token' => $token,
                'token_type'   => 'bearer',
                'catalogue'    => $user->user_catalogue->code ?? null,
                'expires_in'   => auth()->factory()->getTTL(),
            ],
        ], ResponseEnum::OK);
    }

    /**
     * Logs the user out of the application.
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return successResponse('Đăng xuất thành công.', [], true);
    }

    /**
     * Send a verification code to the user's phone number.
     */
    public function sendVerificationCode(Request $request): JsonResponse
    {
        if (auth()->check()) {
            $response = Stringee::sendVerificationCode($request, $this->currentUser);

            return handleResponse($response);
        }

        $phone = $request->input('phone');
        if (! $phone) {
            return errorResponse('Vui lòng nhập số điện thoại.', true);
        }
        $user = User::where('phone', $phone)->first();
        if (! $user) {
            return errorResponse('Số điện thoại không tồn tại trong hệ thống.', true);
        }

        $response = Stringee::sendVerificationCode($request, $user);

        return handleResponse($response);
    }

    /**
     * Verify the verification code that was sent to the user's phone number.
     */
    public function verifyCode(Request $request): JsonResponse
    {
        $response = Stringee::verifyCode($request, $this->currentUser);

        return handleResponse($response);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        $idToken = $request->input('id_token');

        // Xác minh id_token trực tiếp với Google
        $client = new \GuzzleHttp\Client;
        $response = $client->get('https://oauth2.googleapis.com/tokeninfo?id_token=' . $idToken);

        if ($response->getStatusCode() !== 200) {
            return response()->json(['error' => 'Invalid Google token'], 401);
        }

        $googleUser = json_decode($response->getBody());

        $existingUser = User::where('email', $googleUser->email)->first();

        if ($existingUser) {
            Auth::login($existingUser);
        } else {
            $newUser = User::create([
                'fullname'          => $googleUser->name,
                'email'             => $googleUser->email,
                'google_id'         => $googleUser->sub,
                'password'          => Hash::make(uniqid()),
                'ip'                => $request->ip(),
                'user_agent'        => $request->header('User-Agent'),
                'user_catalogue_id' => User::ROLE_CUSTOMER,
                'email_verified_at' => now(),
            ]);
            Auth::login($newUser);
        }

        $user = Auth::user();
        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token, 'Google login successful.', $user);
    }
}
