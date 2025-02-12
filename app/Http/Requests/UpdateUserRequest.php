<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            "username" => "unique:users|nullable",
            "nickname" => "nullable",
            "email" => "unique:users|nullable",
            "gender" => "in:male,female,other",
            "verification" => "in:no,pending,verified",
            "status" => "in:active,inactive",
            "push_token" => "nullable",
            "location_id" => "nullable",
            "mobile" => "nullable",
            "bio" => "nullable"
        ];
    }

    public function messages(): array
    {
        return [
            "username.unique" => "Username already taken!",
            "email.unique" => "Email already taken!",
            "gender.in" => "Gender must be male, female, or other.",
            "verification.in" => "Verification must be no, pending, or verified",
            "status.in" => "Status must be active or inactive"
        ];
    }
}
