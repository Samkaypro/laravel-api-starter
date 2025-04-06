<?php

namespace App\DTOs;

use Spatie\Permission\Models\Permission;

class PermissionDTO extends BaseDTO
{
    /**
     * Create a new PermissionDTO instance.
     *
     * @param int $id
     * @param string $name
     * @param string|null $created_at
     * @param string|null $updated_at
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
    ) {
    }

    /**
     * Create a PermissionDTO from a Permission model.
     *
     * @param Permission $permission
     * @return static
     */
    public static function fromPermission(Permission $permission): static
    {
        return new self(
            id: $permission->id,
            name: $permission->name,
            created_at: $permission->created_at?->toIso8601String(),
            updated_at: $permission->updated_at?->toIso8601String(),
        );
    }

    /**
     * Create a PermissionDTO from an array of permission data.
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 