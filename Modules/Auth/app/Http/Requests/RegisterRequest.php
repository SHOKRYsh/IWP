<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string',
            'email' => ['required_without:phone|email|unique:users,email'],
            'country_code' => 'nullable|string',
            'phone' => ['required_without:email', 'string','unique:users,phone'],
            'password' => 'required|string|min:8',
            'profile_image' =>'nullable|image|mimes:png,jpg,svg',
            'gender' => ['nullable', Rule::in(['male','female'])],
            'marital_status' => ['nullable', Rule::in(['single','married','divorced','widowed'])],
            'life_style_id' => 'nullable|exists:life_styles,id',
            'life_element_ids' => 'nullable|array',
            'life_element_ids.*' => 'exists:life_elements,id',

            'children' => 'nullable|array',
            'children.*.name' => 'required|string|max:255',
            'children.*.gender' => 'nullable|in:male,female',
            'children.*.educational_stage' => 'nullable|string|max:255',
            'children.*.age' => 'nullable|integer|min:0|max:100',
            'children.*.extracurricular_activities' => 'nullable|boolean',
            'children.*.ballet_class' => 'nullable|string|max:255',
        ];

        return $rules;
    }


    public function authorize(): bool
    {
        return true;
    }
}
