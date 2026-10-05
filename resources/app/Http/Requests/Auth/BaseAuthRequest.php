<?php

namespace App\Http\Requests\Auth;

use App\Support\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseAuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt according to project API standards.
     */
    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors()->toArray();
        $firstError = $validator->errors()->first();

        // If validation failed on password confirmation or policy, customize error code
        $errorCode = 'ERR_VALIDATION_FAILED';
        if ($validator->errors()->has('password') || $validator->errors()->has('new_password') || $validator->errors()->has('confirm_password')) {
            $errorCode = 'ERR_PASSWORD_POLICY_FAILED';
        }

        $response = ApiResponse::error(
            message: $firstError ?: 'The given data was invalid.',
            errorCode: $errorCode,
            statusCode: 422,
            errors: $errors
        );

        throw new HttpResponseException($response);
    }
}
