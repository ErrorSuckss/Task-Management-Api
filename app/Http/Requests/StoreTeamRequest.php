<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeamRequest extends FormRequest
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
            'name' => 'required|max:100|string',
            'profile_pic' => ['nullable', 'image', 'mimes:png,jpg'],
            'team_leader_id' => [
                'required',
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
            'team_leader_id.exists' => 'The selected user must ne a Team Leader.',
            'name.required' => 'The team name is required.',
            'name.max' => 'The team name may not be greater than 100 characters.',
        ];
    }
}
