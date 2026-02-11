<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function saveOnboarding(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'intention' => 'required|string|max:255',
            'daily_goal_minutes' => 'required|integer|min:1|max:1440',
            'reminder_time' => 'required|string|max:32',
            'preferred_language' => 'required|string|max:10',
            'notifications_enabled' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $onboardingData = [
            'intention' => (string) $request->input('intention'),
            'daily_goal_minutes' => (int) $request->input('daily_goal_minutes'),
            'reminder_time' => (string) $request->input('reminder_time'),
            'preferred_language' => (string) $request->input('preferred_language'),
            'notifications_enabled' => (bool) $request->boolean('notifications_enabled'),
        ];

        $user->forceFill([
            'onboarding_data' => $onboardingData,
            'onboarding_completed_at' => now(),
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Onboarding saved',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'onboarding_completed_at' => optional($user->onboarding_completed_at)->toISOString(),
                    'onboarding_completed' => ! empty($user->onboarding_completed_at),
                    'onboarding_data' => $user->onboarding_data,
                ],
            ],
        ], 200);
    }

    public function requestPasswordResetOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = Str::lower(trim((string) $request->input('email')));

        if (! app()->environment('testing')) {
            $mailer = (string) config('mail.default');
            if (in_array($mailer, ['log', 'array'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email sending is not configured',
                    'errors' => [
                        'mail' => ['Set MAIL_MAILER=smtp and configure SMTP credentials'],
                    ],
                ], 500);
            }

            if ($mailer === 'smtp') {
                $host = (string) config('mail.mailers.smtp.host');
                $port = (int) config('mail.mailers.smtp.port');
                $username = (string) config('mail.mailers.smtp.username');
                $password = (string) config('mail.mailers.smtp.password');

                if (empty($host) || $host === '127.0.0.1' || $port <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email sending is not configured',
                        'errors' => [
                            'mail' => ['MAIL_HOST / MAIL_PORT must be set to a real SMTP server'],
                        ],
                    ], 500);
                }

                if (empty($username) || empty($password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email sending is not configured',
                        'errors' => [
                            'mail' => ['MAIL_USERNAME / MAIL_PASSWORD must be set'],
                        ],
                    ], 500);
                }
            }
        }

        if (! User::where('email', $email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => [
                    'email' => ['No account found for this email address.'],
                ],
            ], 422);
        }

        $cooldownKey = "password_reset_otp_cooldown:{$email}";
        if (Cache::has($cooldownKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before requesting another code',
            ], 429);
        }

        $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $ttl = now()->addMinutes(10);

        try {
            Mail::raw(
                "Your password reset code is: {$otpCode}",
                function ($message) use ($email) {
                    $message->to($email)->subject('Password reset code');
                }
            );
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Failed to send password reset code',
                'errors' => [
                    'mail' => [config('app.debug') ? $e->getMessage() : 'Failed to send password reset code'],
                ],
            ], 500);
        }

        Cache::put("password_reset_otp_hash:{$email}", Hash::make($otpCode), $ttl);
        Cache::put("password_reset_otp_attempts:{$email}", 0, $ttl);
        Cache::forget("password_reset_token_hash:{$email}");
        Cache::put($cooldownKey, true, now()->addSeconds(60));

        return response()->json([
            'success' => true,
            'message' => 'Password reset code sent',
        ], 200);
    }

    public function verifyPasswordResetOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'otp_code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = Str::lower(trim((string) $request->input('email')));
        $otpCode = (string) $request->input('otp_code');

        if (! User::where('email', $email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => [
                    'email' => ['No account found for this email address.'],
                ],
            ], 422);
        }

        $otpHash = Cache::get("password_reset_otp_hash:{$email}");
        if (empty($otpHash)) {
            return response()->json([
                'success' => false,
                'message' => 'Password reset code expired or not requested',
            ], 422);
        }

        $attemptsKey = "password_reset_otp_attempts:{$email}";
        $attempts = (int) Cache::get($attemptsKey, 0);
        if ($attempts >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Too many verification attempts',
            ], 429);
        }

        if (! Hash::check($otpCode, $otpHash)) {
            Cache::put($attemptsKey, $attempts + 1, now()->addMinutes(10));

            return response()->json([
                'success' => false,
                'message' => 'Invalid password reset code',
            ], 401);
        }

        $resetToken = Str::random(64);
        $ttl = now()->addMinutes(10);

        Cache::put("password_reset_token_hash:{$email}", Hash::make($resetToken), $ttl);
        Cache::forget("password_reset_otp_hash:{$email}");
        Cache::forget("password_reset_otp_attempts:{$email}");

        return response()->json([
            'success' => true,
            'message' => 'Password reset code verified',
            'data' => [
                'reset_token' => $resetToken,
            ],
        ], 200);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'reset_token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = Str::lower(trim((string) $request->input('email')));
        $resetToken = (string) $request->input('reset_token');

        $tokenHash = Cache::get("password_reset_token_hash:{$email}");
        if (empty($tokenHash)) {
            return response()->json([
                'success' => false,
                'message' => 'Password reset session expired or not verified',
            ], 422);
        }

        if (! Hash::check($resetToken, $tokenHash)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password reset session',
            ], 401);
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => [
                    'email' => ['No account found for this email address.'],
                ],
            ], 422);
        }

        $user->forceFill([
            'password' => Hash::make((string) $request->input('password')),
        ])->save();

        Cache::forget("password_reset_token_hash:{$email}");

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully',
        ], 200);
    }

    public function requestRegistrationOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = Str::lower(trim((string) $request->input('email')));

        if (! app()->environment('testing')) {
            $mailer = (string) config('mail.default');
            if (in_array($mailer, ['log', 'array'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email sending is not configured',
                    'errors' => [
                        'mail' => ['Set MAIL_MAILER=smtp and configure SMTP credentials'],
                    ],
                ], 500);
            }

            if ($mailer === 'smtp') {
                $host = (string) config('mail.mailers.smtp.host');
                $port = (int) config('mail.mailers.smtp.port');
                $username = (string) config('mail.mailers.smtp.username');
                $password = (string) config('mail.mailers.smtp.password');

                if (empty($host) || $host === '127.0.0.1' || $port <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email sending is not configured',
                        'errors' => [
                            'mail' => ['MAIL_HOST / MAIL_PORT must be set to a real SMTP server'],
                        ],
                    ], 500);
                }

                if (empty($username) || empty($password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email sending is not configured',
                        'errors' => [
                            'mail' => ['MAIL_USERNAME / MAIL_PASSWORD must be set'],
                        ],
                    ], 500);
                }
            }
        }

        if (User::where('email', $email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => [
                    'email' => ['The email has already been taken.'],
                ],
            ], 422);
        }

        $cooldownKey = "registration_otp_cooldown:{$email}";
        if (Cache::has($cooldownKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before requesting another code',
            ], 429);
        }

        $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $ttl = now()->addMinutes(10);

        try {
            Mail::raw(
                "Your verification code is: {$otpCode}",
                function ($message) use ($email) {
                    $message->to($email)->subject('Verification code');
                }
            );
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Failed to send verification code',
                'errors' => [
                    'mail' => [config('app.debug') ? $e->getMessage() : 'Failed to send verification code'],
                ],
            ], 500);
        }

        Cache::put("registration_otp_hash:{$email}", Hash::make($otpCode), $ttl);
        Cache::put("registration_otp_attempts:{$email}", 0, $ttl);
        Cache::put($cooldownKey, true, now()->addSeconds(60));

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent',
        ], 200);
    }

    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'otp_code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = Str::lower(trim((string) $request->input('email')));
        $otpCode = (string) $request->input('otp_code');

        $otpHash = Cache::get("registration_otp_hash:{$email}");
        if (empty($otpHash)) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code expired or not requested',
            ], 422);
        }

        $attemptsKey = "registration_otp_attempts:{$email}";
        $attempts = (int) Cache::get($attemptsKey, 0);
        if ($attempts >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Too many verification attempts',
            ], 429);
        }

        if (! Hash::check($otpCode, $otpHash)) {
            Cache::put($attemptsKey, $attempts + 1, now()->addMinutes(10));

            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code',
            ], 401);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'password' => Hash::make($request->password),
                'role' => 'user', // Default role
            ]);

            $user->forceFill(['email_verified_at' => now()])->save();

            Cache::forget("registration_otp_hash:{$email}");
            Cache::forget("registration_otp_attempts:{$email}");

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'onboarding_completed_at' => optional($user->onboarding_completed_at)->toISOString(),
                        'onboarding_completed' => ! empty($user->onboarding_completed_at),
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if (! Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'onboarding_completed_at' => optional($user->onboarding_completed_at)->toISOString(),
                        'onboarding_completed' => ! empty($user->onboarding_completed_at),
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function google(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'nullable|string',
            'access_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $idToken = $request->input('id_token');
            $accessToken = $request->input('access_token');

            if (empty($idToken) && empty($accessToken)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => [
                        'token' => ['Either id_token or access_token is required'],
                    ],
                ], 422);
            }

            $email = null;
            $isEmailVerified = false;
            $name = null;

            if (! empty($idToken)) {
                $tokenInfoResponse = Http::timeout(10)->get(
                    'https://oauth2.googleapis.com/tokeninfo',
                    ['id_token' => $idToken]
                );

                if (! $tokenInfoResponse->ok()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid Google token',
                    ], 401);
                }

                $tokenInfo = $tokenInfoResponse->json();

                $expectedAud = config('services.google.client_id');
                $aud = $tokenInfo['aud'] ?? null;
                if (! empty($expectedAud) && $aud !== $expectedAud) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid Google token audience',
                    ], 401);
                }

                $email = $tokenInfo['email'] ?? null;
                $emailVerified = $tokenInfo['email_verified'] ?? null;
                $isEmailVerified = $emailVerified === true || $emailVerified === 'true';
                $googleName = $tokenInfo['name'] ?? null;
                $name = ! empty($googleName) ? $googleName : null;
            } else {
                $userInfoResponse = Http::timeout(10)
                    ->withToken($accessToken)
                    ->get('https://www.googleapis.com/oauth2/v3/userinfo');

                if (! $userInfoResponse->ok()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid Google token',
                    ], 401);
                }

                $userInfo = $userInfoResponse->json();
                $email = $userInfo['email'] ?? null;
                $emailVerified = $userInfo['email_verified'] ?? null;
                $isEmailVerified = $emailVerified === true || $emailVerified === 'true';
                $name = $userInfo['name'] ?? null;
            }

            if (empty($email) || ! $isEmailVerified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google account email is not verified',
                ], 401);
            }

            $name = ! empty($name) ? $name : Str::before($email, '@');

            $user = User::where('email', $email)->first();

            if (! $user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make(Str::random(32)),
                    'role' => 'user',
                ]);

                if ($isEmailVerified) {
                    $user->forceFill(['email_verified_at' => now()])->save();
                }
            } else {
                $updates = [];
                if (empty($user->name) && ! empty($name)) {
                    $updates['name'] = $name;
                }
                if ($isEmailVerified && empty($user->email_verified_at)) {
                    $updates['email_verified_at'] = now();
                }
                if (! empty($updates)) {
                    $user->forceFill($updates)->save();
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Google login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'onboarding_completed_at' => optional($user->onboarding_completed_at)->toISOString(),
                        'onboarding_completed' => ! empty($user->onboarding_completed_at),
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updates = [
            'name' => (string) $request->input('name'),
        ];

        if ($request->filled('password')) {
            $updates['password'] = Hash::make((string) $request->input('password'));
        }

        $user->forceFill($updates)->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'onboarding_completed_at' => optional($user->onboarding_completed_at)->toISOString(),
                    'onboarding_completed' => ! empty($user->onboarding_completed_at),
                ],
            ],
        ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout successful',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
