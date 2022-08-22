<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'time' => 'required|integer',
            'description' => 'string|max:500',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'questions' => 'string',
        ];
    }

    public function message()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name field must be a string.',
            'name.max' => 'The name field may not be greater than 500 characters.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description field must be a string.',
            'description.max' => 'The description field may not be greater than 255 characters.',
            'image.image' => 'The image field must be an image.',
            'image.mimes' => 'The image field must be a file of type: jpeg, png, jpg, gif, svg.',
            'image.max' => 'The image field may not be greater than 5120 kilobytes.',
            'questions.string' => 'The questions field must be a json string.',
        ];
    }
}
