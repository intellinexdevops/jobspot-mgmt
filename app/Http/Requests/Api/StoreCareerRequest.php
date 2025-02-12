<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreCareerRequest extends FormRequest
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
            'company_id' => 'required|exists:companies,id',
            'workspace_id' => 'required|exists:workspaces,id',
            'location_id' => 'required|exists:locations,id',
            'employment_type_id' => 'required|exists:employment_type,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirement' => 'required|string',
            'facilities' => 'nullable',
            'deadline' => 'required|date',
            'status' => 'required|in:active,inactive,draft',
            'salary' => 'required|string',
            'unit' => 'required|string|max:255',
            'skills' => 'required|exists:skills,id',
            'experience_level_id' => 'required:exists:experience_level'
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'Company is required',
            'workspace_id.required' => 'Workspace is required',
            'location_id.required' => 'Location is required',
            'employment_type_id.required' => 'Employment type is required',
            'title.required' => 'Title is required',
            'description.required' => 'Description is required',
            'requirement.required' => 'Requirment is required',
            'deadline.required' => 'Deadline is required',
            'status.required' => 'Status is required',
            'salary.required' => 'Salary is required',
            'unit.required' => 'Unit is required',
            'skills.required' => 'Skills is required',
            'skills.exists' => 'Skills is not exists',
            'experience_level_id.required' => "Experience level is required.",
            'experience_level_id.exists' => "Could not found Experience level ID."
        ];
    }
}
