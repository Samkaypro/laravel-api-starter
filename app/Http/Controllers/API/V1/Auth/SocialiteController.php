<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\DTOs\AuthResponseDTO;
use App\Http\Controllers\API\BaseController;
use App\Models\User;
use App\Services\TokenService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

/**
 * @OA\Tag(
 *     name="Social Authentication",
 *     description="API Endpoints for social authentication"
 * )
 */
class SocialiteController extends BaseController
{
    /**
     * The token service instance.
     *
     * @var TokenService
     */
    protected $tokenService;

    /**
     * Create a new SocialiteController instance.
     *
     * @param TokenService $tokenService
     * @return void
     */
    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }
    
    /**
     * Redirect to provider for authentication
     * 
     * @OA\Get(
     *     path="/api/v1/auth/{provider}/redirect",
     *     operationId="redirectToProvider",
     *     tags={"Social Authentication"},
     *     summary="Get OAuth redirect URL",
     *     description="Returns the URL to redirect the user to for OAuth authentication",
     *     @OA\Parameter(
     *         name="provider",
     *         in="path",
     *         required=true,
     *         description="The OAuth provider (google, facebook, github)",
     *         @OA\Schema(type="string", enum={"google", "facebook", "github"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="redirect_url", type="string", example="https://accounts.google.com/o/oauth2/auth?response_type=code&client_id=...")
     *             ),
     *             @OA\Property(property="message", type="string", example="Redirect URL generated successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid provider",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid provider."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     *
     * @param string $provider
     * @return JsonResponse
     */
    public function redirectToProvider(string $provider): JsonResponse
    {
        // Validate the provider
        if (!in_array($provider, ['google', 'facebook', 'github'])) {
            return $this->sendError('Invalid provider.', [], 400);
        }
        
        // Generate the provider redirect URL
        $redirectUrl = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
        
        return $this->sendResponse(['redirect_url' => $redirectUrl], 'Redirect URL generated successfully.');
    }
    
    /**
     * Handle provider callback
     * 
     * @OA\Get(
     *     path="/api/v1/auth/{provider}/callback",
     *     operationId="handleProviderCallback",
     *     tags={"Social Authentication"},
     *     summary="Handle OAuth callback",
     *     description="Processes the callback from the OAuth provider",
     *     @OA\Parameter(
     *         name="provider",
     *         in="path",
     *         required=true,
     *         description="The OAuth provider (google, facebook, github)",
     *         @OA\Schema(type="string", enum={"google", "facebook", "github"})
     *     ),
     *     @OA\Parameter(
     *         name="code",
     *         in="query",
     *         required=true,
     *         description="The authorization code returned by the provider",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentication successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="User authenticated successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid provider",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid provider."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Authentication failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="OAuth authentication failed."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="error", type="string")
     *             )
     *         )
     *     )
     * )
     *
     * @param string $provider
     * @return JsonResponse
     */
    public function handleProviderCallback(string $provider): JsonResponse
    {
        // Validate the provider
        if (!in_array($provider, ['google', 'facebook', 'github'])) {
            return $this->sendError('Invalid provider.', [], 400);
        }
        
        try {
            // Get user data from provider
            $providerUser = Socialite::driver($provider)->stateless()->user();
            
            // Check if this social account already exists
            $user = User::where('provider', $provider)
                ->where('provider_id', $providerUser->getId())
                ->first();
                
            // If not, check if user with this email already exists
            if (!$user) {
                $user = User::where('email', $providerUser->getEmail())->first();
                
                if ($user) {
                    // Update existing user with provider info
                    $user->update([
                        'provider' => $provider,
                        'provider_id' => $providerUser->getId(),
                    ]);
                } else {
                    // Create a new user
                    $user = User::create([
                        'name' => $providerUser->getName(),
                        'email' => $providerUser->getEmail(),
                        'password' => Hash::make(Str::random(16)),
                        'provider' => $provider,
                        'provider_id' => $providerUser->getId(),
                        'profile_picture' => $providerUser->getAvatar(),
                    ]);
                    
                    // Assign default user role
                    $user->assignRole('user');
                }
            }
            
            // Generate device name from provider
            $device = "oauth_{$provider}";
            
            // Create token using TokenService
            $tokenData = $this->tokenService->createUserToken($user, $device);
            
            // Create response DTO
            $responseDto = AuthResponseDTO::fromUserAndToken($user, $tokenData);
            
            return $this->sendResponse($responseDto->toArray(), 'User authenticated successfully.');
            
        } catch (Exception $e) {
            return $this->sendError('OAuth authentication failed.', ['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Handle direct token request with provider token
     * 
     * @OA\Post(
     *     path="/api/v1/auth/{provider}/token",
     *     operationId="handleProviderToken",
     *     tags={"Social Authentication"},
     *     summary="Authenticate with provider token",
     *     description="Authenticates a user using a token from an OAuth provider",
     *     @OA\Parameter(
     *         name="provider",
     *         in="path",
     *         required=true,
     *         description="The OAuth provider (google, facebook, github)",
     *         @OA\Schema(type="string", enum={"google", "facebook", "github"})
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"access_token"},
     *             @OA\Property(property="access_token", type="string", example="ya29.a0AfH6SMBzA7...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentication successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="User authenticated successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid provider or token",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid provider."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Authentication failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="OAuth authentication failed."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="error", type="string")
     *             )
     *         )
     *     )
     * )
     * 
     * @param Request $request
     * @param string $provider
     * @return JsonResponse
     */
    public function handleProviderToken(Request $request, string $provider): JsonResponse
    {
        // Validate the provider
        if (!in_array($provider, ['google', 'facebook', 'github'])) {
            return $this->sendError('Invalid provider.', [], 400);
        }
        
        $request->validate([
            'access_token' => 'required|string',
        ]);
        
        try {
            // Get user data from provider using the token
            $providerUser = Socialite::driver($provider)->userFromToken($request->access_token);
            
            // Check if this social account already exists
            $user = User::where('provider', $provider)
                ->where('provider_id', $providerUser->getId())
                ->first();
                
            // If not, check if user with this email already exists
            if (!$user) {
                $user = User::where('email', $providerUser->getEmail())->first();
                
                if ($user) {
                    // Update existing user with provider info
                    $user->update([
                        'provider' => $provider,
                        'provider_id' => $providerUser->getId(),
                    ]);
                } else {
                    // Create a new user
                    $user = User::create([
                        'name' => $providerUser->getName(),
                        'email' => $providerUser->getEmail(),
                        'password' => Hash::make(Str::random(16)),
                        'provider' => $provider,
                        'provider_id' => $providerUser->getId(),
                        'profile_picture' => $providerUser->getAvatar(),
                    ]);
                    
                    // Assign default user role
                    $user->assignRole('user');
                }
            }
            
            // Generate device name from provider
            $device = "oauth_{$provider}_token";
            
            // Create token using TokenService
            $tokenData = $this->tokenService->createUserToken($user, $device);
            
            // Create response DTO
            $responseDto = AuthResponseDTO::fromUserAndToken($user, $tokenData);
            
            return $this->sendResponse($responseDto->toArray(), 'User authenticated successfully.');
            
        } catch (Exception $e) {
            return $this->sendError('OAuth authentication failed.', ['error' => $e->getMessage()], 500);
        }
    }
} 