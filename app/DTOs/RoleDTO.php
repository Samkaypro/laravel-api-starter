<?php

namespace App\DTOs;

use Spatie\Permission\Models\Role;

class RoleDTO extends BaseDTO
{
    /**
     * Create a new RoleDTO instance.
     *
     * @param int $id
     * @param string $name
     * @param array $permissions
     * @param string|null $created_at
     * @param string|null $updated_at
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly array $permissions = [],
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
    ) {
    }

    /**
     * Create a RoleDTO from a Role model.
     *
     * @param Role $role
     * @param bool $includePermissions Include permissions data
     * @return static
     */
    public static function fromRole(Role $role, bool $includePermissions = true): static
    {
        $data = [
            'id' => $role->id,
            'name' => $role->name,
            'created_at' => $role->created_at?->toIso8601String(),
            'updated_at' => $role->updated_at?->toIso8601String(),
        ];

        if ($includePermissions) {
            $data['permissions'] = $role->permissions->pluck('name')->toArray();
        }

        return new self(...$data);
    }

    /**
     * Create a RoleDTO from an array of role data.
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' => $this->permissions,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 