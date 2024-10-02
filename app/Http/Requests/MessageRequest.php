<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'conversation_id' => ['nullable', 'exists:conversations,id'],
            'receiver_id' => ['nullable','exists:users,id'],
            'content' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422));
    }
}
