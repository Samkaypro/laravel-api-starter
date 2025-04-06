<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class OAuthCallbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'provider' => ['required', 'string', 'in:google,facebook,github'],
            'state' => ['sometimes', 'string'],
            'code' => ['sometimes', 'string'],
            'error' => ['sometimes', 'string'],
            'error_description' => ['sometimes', 'string'],
        ];
    }
} 