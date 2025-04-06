<?php

namespace App\DTOs;

use App\Models\User;

class UserProfileDTO extends UserDTO
{
    /**
     * Create a new UserProfileDTO instance.
     *
     * @param int $id
     * @param string $name
     * @param string $email
     * @param string|null $phone
     * @param string|null $address
     * @param string|null $profile_picture
     * @param string|null $email_verified_at
     * @param array|null $roles
     * @param array|null $permissions
     * @param string|null $created_at
     * @param string|null $updated_at
     */
    public function __construct(
        int $id,
        string $name,
        string $email,
        public readonly ?string $phone = null,
        public readonly ?string $address = null,
        public readonly ?string $profile_picture = null,
        ?string $email_verified_at = null,
        ?array $roles = [],
        ?array $permissions = [],
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
    ) {
        parent::__construct($id, $name, $email, $email_verified_at, $roles, $permissions);
    }

    /**
     * Create a UserProfileDTO from a User model.
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
            'phone' => $user->phone,
            'address' => $user->address,
            'profile_picture' => $user->profile_picture,
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            'created_at' => $user->created_at?->toIso8601String(),
            'updated_at' => $user->updated_at?->toIso8601String(),
        ];

        if ($includePermissions) {
            $data['roles'] = $user->roles->pluck('name')->toArray();
            $data['permissions'] = $user->getAllPermissions()->pluck('name')->toArray();
        }

        return new self(...$data);
    }

    /**
     * Create a UserProfileDTO from an array of user data.
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
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            profile_picture: $data['profile_picture'] ?? null,
            email_verified_at: $data['email_verified_at'] ?? null,
            roles: $data['roles'] ?? [],
            permissions: $data['permissions'] ?? [],
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'phone' => $this->phone,
            'address' => $this->address,
            'profile_picture' => $this->profile_picture,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
} 