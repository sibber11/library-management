<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckOutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'book_id' => 'required|exists:books,id',
            'member_id' => 'required|exists:members,id',
            // 'check_out_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            // 'check_in_date' => 'nullable|date',
        ];
    }
}
