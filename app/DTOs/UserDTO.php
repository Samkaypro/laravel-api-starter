<?php

namespace App\DTOs;

use App\Models\User;

class UserDTO extends BaseDTO
{
    /**
     * Create a new UserDTO instance.
     *
     * @param int $id
     * @param string $name
     * @param string $email
     * @param string|null $email_verified_at
     * @param array|null $roles
     * @param array|null $permissions
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $email_verified_at = null,
        public readonly ?array $roles = [],
        public readonly ?array $permissions = [],
    ) {
    }

    /**
     * Create a UserDTO from a User model.
     *
     * @param User $user
     * @param bool $includePermissions Include roles and permissions data
     * @return static
     */
    public static function fromUser(User $user, bool $includePermissions = true): static
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
        ];

        if ($includePermissions) {
            $data['roles'] = $user->roles->pluck('name')->toArray();
            $data['permissions'] = $user->getAllPermissions()->pluck('name')->toArray();
        }

        return new self(...$data);
    }

    /**
     * Create a UserDTO from an array of user data.
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            email: $data['email'],
            email_verified_at: $data['email_verified_at'] ?? null,
            roles: $data['roles'] ?? [],
            permissions: $data['permissions'] ?? [],
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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'roles' => $this->roles,
            'permissions' => $this->permissions,
        ];
    }
} 