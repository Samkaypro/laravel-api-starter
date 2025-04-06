<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\DTOs\AuthResponseDTO;
use App\DTOs\TokenDTO;
use App\DTOs\UserDTO;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints for user authentication"
 * )
 */
class AuthController extends BaseController
{
    /**
     * The token service instance.
     *
     * @var TokenService
     */
    protected $tokenService;

    /**
     * Create a new AuthController instance.
     *
     * @param TokenService $tokenService
     * @return void
     */
    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Register a new user
     * 
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     operationId="register",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     description="Returns user data and access token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Password123!"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="Password123!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="access_token", type="string"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="message", type="string", example="User registered successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign the default role (usually 'user')
        $role = Role::where('name', 'user')->first();
        if ($role) {
            $user->assignRole($role);
        }

        // Generate device name from user agent
        $device = substr($request->userAgent() ?? 'unknown', 0, 255);
        
        // Create token
        $tokenData = $this->tokenService->createUserToken($user, "register_{$device}");

        // Create response DTO
        $responseDto = AuthResponseDTO::fromUserAndToken($user, $tokenData);
        
        return $this->sendResponse($responseDto->toArray(), 'User registered successfully.');
    }

    /**
     * Login user and create token
     * 
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     operationId="login",
     *     tags={"Authentication"},
     *     summary="Login and get access token",
     *     description="Returns user data and access token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Password123!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="access_token", type="string"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="message", type="string", example="User logged in successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid login credentials."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many attempts",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Too many login attempts. Please try again in 60 seconds."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // This will handle rate limiting and authentication
            $request->authenticate();
            
            $user = User::where('email', $request->email)->firstOrFail();
            
            // Generate device name from user agent
            $device = substr($request->userAgent() ?? 'unknown', 0, 255);
            
            // Create token
            $tokenData = $this->tokenService->createUserToken($user, "login_{$device}");
            
            // Create response DTO
            $responseDto = AuthResponseDTO::fromUserAndToken($user, $tokenData);
            
            return $this->sendResponse($responseDto->toArray(), 'User logged in successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->sendError($e->getMessage(), ['email' => $e->errors()['email'] ?? []], $e->status);
        }
    }

    /**
     * Logout user (Revoke the token)
     * 
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     operationId="logout",
     *     tags={"Authentication"},
     *     summary="Logout and invalidate token",
     *     description="Invalidates the user's current access token",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="User logged out successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->tokenService->revokeCurrentToken($request->user());
            return $this->sendResponse([], 'User logged out successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Logout failed. ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Refresh token
     * 
     * @OA\Post(
     *     path="/api/v1/auth/refresh",
     *     operationId="refreshToken",
     *     tags={"Authentication"},
     *     summary="Refresh access token",
     *     description="Invalidates the current token and issues a new one",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="access_token", type="string"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="message", type="string", example="Token refreshed successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Store the current token name to use for the new token
            $currentTokenName = $user->currentAccessToken()->name;
            
            // Revoke current token
            $this->tokenService->revokeCurrentToken($user);
            
            // Create new token with same name
            $tokenData = $this->tokenService->createUserToken($user, $currentTokenName . '_refreshed');
            
            // Create response DTO
            $responseDto = AuthResponseDTO::fromUserAndToken($user, $tokenData);
            
            return $this->sendResponse($responseDto->toArray(), 'Token refreshed successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Token refresh failed. ' . $e->getMessage(), [], 500);
        }
    }
} 