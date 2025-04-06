<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Service class for handling user tokens
 */
class TokenService
{
    /**
     * Create a new access token for a user
     *
     * @param User $user User to create token for
     * @param string|null $device Device name or identifier
     * @param array $abilities Token abilities
     * @return array Token data including access_token, token_type, and expires_at
     */
    public function createUserToken(User $user, ?string $device = null, array $abilities = ['*']): array
    {
        // Create a meaningful token name using the device info or fallback to timestamp
        $tokenName = $device ?? 'token_' . time();
        
        // Get expiration time
        $expiration = $this->getTokenExpiration();
        
        // Create the token with the given abilities and expiration
        $token = $user->createToken($tokenName, $abilities, $expiration);
        
        return [
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiration->toIso8601String()
        ];
    }
    
    /**
     * Revoke all tokens for a user
     *
     * @param User $user User whose tokens should be revoked
     * @return void
     */
    public function revokeAllTokens(User $user): void
    {
        // Use DB facade to delete all tokens for the user
        DB::table('personal_access_tokens')
            ->where('tokenable_id', $user->id)
            ->where('tokenable_type', get_class($user))
            ->delete();
    }
    
    /**
     * Revoke the current access token
     *
     * @param User $user User whose current token should be revoked
     * @return void
     */
    public function revokeCurrentToken(User $user): void
    {
        // Access token ID directly for deletion
        $token = $user->currentAccessToken();
        if ($token) {
            DB::table('personal_access_tokens')
                ->where('id', $token->id)
                ->delete();
        }
    }
    
    /**
     * Revoke tokens by device name
     *
     * @param User $user User whose tokens should be revoked
     * @param string $device Device identifier in the token name
     * @return void
     */
    public function revokeTokensByDevice(User $user, string $device): void
    {
        // Use DB facade to delete tokens with matching name pattern
        DB::table('personal_access_tokens')
            ->where('tokenable_id', $user->id)
            ->where('tokenable_type', get_class($user))
            ->where('name', 'like', "%{$device}%")
            ->delete();
    }
    
    /**
     * Get token expiration time
     *
     * @return Carbon
     */
    protected function getTokenExpiration(): Carbon
    {
        // Get expiration from config or use default (7 days)
        $expirationMinutes = Config::get('sanctum.expiration', 10080); // 7 days = 10080 minutes
        return now()->addMinutes($expirationMinutes);
    }
} 