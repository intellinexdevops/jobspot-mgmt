<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
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
            "company_name" => "required|max:255",
            "user_id" => "required",
            "industry_id" => "required",
            "location_id" => "required",
            "since" => "required",
            "status" => "in:active,inactive",
            "employment_size_id" => "required",
        ];
    }

    public function messages(): array
    {
        return [
            "company_name.required" => "Company Name is required!",
            "user_id.required" => "User ID must be enter.",
            "industry_id.required" => "Industry ID is required!",
            "location_id.required" => "Location ID is required!",
            "since.required" => "Start date is required!",
            "status.in" => "Status must be active or inactive",
            "employment_size_id" => "Employment Size ID is required!",
        ];
    }
}
