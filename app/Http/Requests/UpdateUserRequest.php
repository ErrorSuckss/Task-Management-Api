<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],

            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($this->route('user')->id),
            ],

            'role' => [
                'sometimes',
                'string',
                Rule::in(['admin', 'team_leader', 'user']),
            ],

            'team_leader_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'team_leader');
                }),
            ],

            'password' => [
                'sometimes',
                'confirmed',
                Password::min(8),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name may not be greater than 255 characters.',

            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'email.unique' => 'This email is already taken.',

            'role.string' => 'The role must be a valid string.',
            'role.in' => 'The selected role is invalid. Allowed roles: admin, team_leader, user.',

            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters.',

            'team_leader_id.integer' => 'The team leader ID must be a number.',
            'team_leader_id.exists' => 'The selected team leader does not exist or is not a team leader.',
        ];
    }
}
