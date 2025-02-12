<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
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
            'user_id' => "required",
            'post_id' => "required",
            'company_id' => "required"
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => "User ID is required!",
            "post_id.required" => "Post ID is required!",
            "company_id.required" => "Company ID is required!"
        ];
    }
}
