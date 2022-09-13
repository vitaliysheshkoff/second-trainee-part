<?php

namespace App\Http\Requests;

use App\Rules\FirstAndSecondName;
use Illuminate\Foundation\Http\FormRequest;

class AddUserPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|min:4|max:100|unique:my_users',
            'name' => new FirstAndSecondName()
        ];
    }
}
