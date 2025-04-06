<?php

namespace App\DTOs;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserCollectionDTO extends BaseDTO
{
    /**
     * Create a new UserCollectionDTO instance.
     * 
     * @param array $users Array of UserDTO objects
     * @param array $meta Pagination or metadata
     */
    public function __construct(
        public readonly array $users,
        public readonly array $meta = [],
    ) {
    }

    /**
     * Create a UserCollectionDTO from a collection of users.
     *
     * @param Collection $users
     * @param bool $includePermissions
     * @return static
     */
    public static function fromCollection(Collection $users, bool $includePermissions = true): static
    {
        $userDtos = [];
        foreach ($users as $user) {
            $userDtos[] = UserDTO::fromUser($user, $includePermissions)->toArray();
        }

        return new self(users: $userDtos);
    }

    /**
     * Create a UserCollectionDTO from a paginator of users.
     *
     * @param LengthAwarePaginator $paginator
     * @param bool $includePermissions
     * @return static
     */
    public static function fromPaginator(LengthAwarePaginator $paginator, bool $includePermissions = true): static
    {
        $userDtos = [];
        foreach ($paginator->items() as $user) {
            $userDtos[] = UserDTO::fromUser($user, $includePermissions)->toArray();
        }

        return new self(
            users: $userDtos,
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
     * Create a UserCollectionDTO from an array of user data.
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            users: $data['users'] ?? [],
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
            'data' => $this->users,
        ];

        if (!empty($this->meta)) {
            $result['meta'] = $this->meta;
        }

        return $result;
    }
} 