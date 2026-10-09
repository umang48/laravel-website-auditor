<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreWebsiteRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // Ensures it's a valid URL format (e.g., must have http/https)
            'url'  => ['required', 'url', 'max:255'], 
        ];
    }

    public function messages(): array
    {
        return [
            'url.url' => 'Please provide a valid URL, including http:// or https://',
        ];
    }
}
