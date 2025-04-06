<?php

namespace App\DTOs;

abstract class BaseDTO
{
    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    abstract public function toArray(): array;

    /**
     * Create a new DTO from an array.
     *
     * @param array $data
     * @return static
     */
    abstract public static function fromArray(array $data): static;
} 