<?php

namespace App\DTOs;

class ResponseDTO extends BaseDTO
{
    /**
     * Create a new ResponseDTO instance.
     *
     * @param mixed $data The response data
     * @param bool $success Whether the response was successful
     * @param string|null $message Optional message
     * @param array $meta Optional metadata
     */
    public function __construct(
        public readonly mixed $data,
        public readonly bool $success = true,
        public readonly ?string $message = null,
        public readonly array $meta = [],
    ) {
    }

    /**
     * Create a ResponseDTO from an array of data.
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            data: $data['data'] ?? [],
            success: $data['success'] ?? true,
            message: $data['message'] ?? null,
            meta: $data['meta'] ?? [],
        );
    }

    /**
     * Create a success response.
     *
     * @param mixed $data
     * @param string|null $message
     * @param array $meta
     * @return static
     */
    public static function success(mixed $data = [], ?string $message = null, array $meta = []): static
    {
        return new self(
            data: $data,
            success: true,
            message: $message,
            meta: $meta,
        );
    }

    /**
     * Create an error response.
     *
     * @param string $message
     * @param mixed $data
     * @param array $meta
     * @return static
     */
    public static function error(string $message, mixed $data = [], array $meta = []): static
    {
        return new self(
            data: $data,
            success: false,
            message: $message,
            meta: $meta,
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $response = [
            'success' => $this->success,
            'data' => $this->data,
        ];

        if ($this->message) {
            $response['message'] = $this->message;
        }

        if (!empty($this->meta)) {
            $response['meta'] = $this->meta;
        }

        return $response;
    }
} 