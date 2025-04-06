<?php

namespace App\DTOs;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RoleCollectionDTO extends BaseDTO
{
    /**
     * Create a new RoleCollectionDTO instance.
     * 
     * @param array $roles Array of RoleDTO objects
     * @param array $meta Pagination or metadata
     */
    public function __construct(
        public readonly array $roles,
        public readonly array $meta = [],
    ) {
    }

    /**
     * Create a RoleCollectionDTO from a collection of roles.
     *
     * @param Collection $roles
     * @param bool $includePermissions
     * @return static
     */
    public static function fromCollection(Collection $roles, bool $includePermissions = true): static
    {
        $roleDtos = [];
        foreach ($roles as $role) {
            $roleDtos[] = RoleDTO::fromRole($role, $includePermissions)->toArray();
        }

        return new self(roles: $roleDtos);
    }

    /**
     * Create a RoleCollectionDTO from a paginator of roles.
     *
     * @param LengthAwarePaginator $paginator
     * @param bool $includePermissions
     * @return static
     */
    public static function fromPaginator(LengthAwarePaginator $paginator, bool $includePermissions = true): static
    {
        $roleDtos = [];
        foreach ($paginator->items() as $role) {
            $roleDtos[] = RoleDTO::fromRole($role, $includePermissions)->toArray();
        }

        return new self(
            roles: $roleDtos,
            meta: [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
        );
    }

    /**
     * Create a RoleCollectionDTO from an array of role data.
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            roles: $data['roles'] ?? [],
            meta: $data['meta'] ?? [],
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [
            'data' => $this->roles,
        ];

        if (!empty($this->meta)) {
            $result['meta'] = $this->meta;
        }

        return $result;
    }
} 