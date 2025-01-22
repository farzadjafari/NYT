<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BestSellersRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|array',
            'isbn.*' => 'string|regex:/^[a-zA-Z0-9]{10}([a-zA-Z0-9]{3})?$/',
            'title' => 'nullable|string|max:255',
            'offset' => 'nullable|integer|max:1000|multiple_of:20',
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.*.regex' => 'Each ISBN must be either a 10-digit or 13-digit string.',
            'offset.multiple_of' => 'The offset must be a multiple of 20.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'errors' => $validator->errors(),
        ], 422));
    }
}
