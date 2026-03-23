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
        ];

        return $rules;
    }


    public function authorize(): bool
    {
        return true;
    }
}
