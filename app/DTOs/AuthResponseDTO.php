<?php

namespace App\DTOs;

use App\Models\User;

class AuthResponseDTO extends BaseDTO
{
    /**
     * Create a new AuthResponseDTO instance.
     *
     * @param UserDTO $user
     * @param TokenDTO $token
     */
    public function __construct(
        public readonly UserDTO $user,
        public readonly TokenDTO $token,
    ) {
    }

    /**
     * Create an AuthResponseDTO from user and token data.
     *
     * @param User $user
     * @param array $tokenData
     * @return static
     */
    public static function fromUserAndToken(User $user, array $tokenData): static
    {
        return new self(
            user: UserDTO::fromUser($user),
            token: TokenDTO::fromArray($tokenData),
        );
    }

    /**
     * Create an AuthResponseDTO from an array of data.
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            user: UserDTO::fromArray($data['user']),
            token: TokenDTO::fromArray([
                'access_token' => $data['access_token'],
                'token_type' => $data['token_type'],
                'expires_at' => $data['expires_at'],
            ]),
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user' => $this->user->toArray(),
            'access_token' => $this->token->access_token,
            'token_type' => $this->token->token_type,
            'expires_at' => $this->token->expires_at,
        ];
    }
} 