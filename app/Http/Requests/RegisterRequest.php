<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.string'   => 'Name must be a valid string.',
            'name.max'      => 'Name may not be greater than 255 characters.',

            'email.required' => 'Email is required.',
            'email.email'    => 'Please provide a valid email address.',
            'email.unique'   => 'This email is already registered.',

            'password.required'   => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min'        => 'Password must be at least 8 characters long.',
        ];
    }
}
