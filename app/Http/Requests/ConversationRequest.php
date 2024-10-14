<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConversationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable'],
            'description' => ['nullable'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
