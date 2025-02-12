<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest
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
            "username" => "required | unique:users",
            "email" => "required | email | unique:users",
            "gender"=> "in:male,female,other",
            "password" => "required | confirmed",
            "mobile" => "min:8 | max:16",
            "verification" => "in:no,pending,verified|required",
            "status"=> "in:active,inactive|required",
        ];
    }

    public function messages(): array
    {
        return [
            "username.required" => "Name is required",
            "username.unique"=> "Username is already exist!",
            "email.required" => "Email is required",
            "email.unique"=> "Email is already exist!",
            "email.email" => "Email must be a valid email address",
            "password.required" => "Password is required",
            "password.confirmed" => "Password confirmation do not match",
            "gender.in"=> "Gender should be male, female, or other",
            "verification.in"=> "Verification must be no, pending or verified!",
            "status.in"=> "Status must be active or inactive!",
        ];
    }
}
