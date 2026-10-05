<?php

namespace App\Http\Requests\Admin;

use App\Models\Photo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'alt_text' => ['required', 'string', 'max:255'],
            'placement' => [
                'required',
                Rule::in(array_keys(Photo::PLACEMENTS)),
            ],
            'credit' => ['nullable', 'string', 'max:150'],
            'in_press_kit' => ['required', 'boolean'],
            'is_published' => ['required', 'boolean'],
            'sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:99999',
            ],
            'image' => [
                'bail',
                $this->isMethod('POST') ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:8192',
                'dimensions:min_width=600,min_height=600,max_width=4000,max_height=4000',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'alt_text' => 'image description',
            'image' => 'photo',
            'sort_order' => 'display order',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Choose a photo to upload.',
            'image.max' => 'The photo must be no larger than 8 MB.',
            'image.dimensions' => 'Both photo dimensions must be between 600 and 4000 pixels.',
            'image.mimes' => 'Upload a JPG, PNG or WebP photo.',
        ];
    }

    public function photoData(): array
    {
        return $this->safe()->except('image');
    }
}