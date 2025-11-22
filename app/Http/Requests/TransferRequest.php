<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to_user_id' => ['required', 'integer', 'exists:users,id', Rule::notIn([$this->user()->id])],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'to_user_id.not_in' => 'Cannot transfer to yourself',
            'amount.min' => 'Amount must be at least 0.01',
        ];
    }
}
