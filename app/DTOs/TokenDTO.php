<?php

namespace App\DTOs;

class TokenDTO extends BaseDTO
{
    /**
     * Create a new TokenDTO instance.
     *
     * @param string $access_token
     * @param string $token_type
     * @param string $expires_at
     */
    public function __construct(
        public readonly string $access_token,
        public readonly string $token_type,
        public readonly string $expires_at,
    ) {
    }

    /**
     * Create a new TokenDTO from an array of token data.
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            access_token: $data['access_token'],
            token_type: $data['token_type'] ?? 'Bearer',
            expires_at: $data['expires_at'],
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
            'access_token' => $this->access_token,
            'token_type' => $this->token_type,
            'expires_at' => $this->expires_at,
        ];
    }
} 