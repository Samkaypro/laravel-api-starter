<?php

namespace App\Http\Controllers\API;

use App\DTOs\ResponseDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Success response method.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function sendResponse(mixed $data, string $message = 'Operation successful', int $code = 200): JsonResponse
    {
        $response = ResponseDTO::success($data, $message);
        return response()->json($response->toArray(), $code);
    }

    /**
     * Error response method.
     *
     * @param string $message
     * @param array $errors
     * @param int $code
     * @return JsonResponse
     */
    public function sendError(string $message = 'Operation failed', array $errors = [], int $code = 404): JsonResponse
    {
        $response = ResponseDTO::error($message, $errors);
        return response()->json($response->toArray(), $code);
    }
} 