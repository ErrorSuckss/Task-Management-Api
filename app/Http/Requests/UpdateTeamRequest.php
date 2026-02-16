<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:100'],
            'team_leader_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'team_leader');
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'The team name must be a string.',
            'name.max' => 'The team name may not be greater than 100 characters.',
            'team_leader_id.exists' => 'The selected user must be a Team Leader.',
            'team_leader_id.integer' => 'The team leader ID must be a valid integer.',
        ];
    }
}
