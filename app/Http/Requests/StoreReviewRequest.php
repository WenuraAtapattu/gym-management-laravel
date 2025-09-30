<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow guests to post reviews if they provide name and email
        if (!$this->user()) {
            return true;
        }
        
        // If user is logged in, ensure email is verified
        return $this->user()->hasVerifiedEmail();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['required', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'content' => ['nullable', 'string', 'max:2000'], // For backward compatibility
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // 5MB max per image
        ];

        // Add guest user fields if user is not logged in
        if (!$this->user()) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'email', 'max:255'];
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // If 'content' is provided but 'comment' is not, copy the value
        if ($this->has('content') && !$this->has('comment')) {
            $this->merge([
                'comment' => $this->content,
            ]);
        }
        // If 'comment' is provided but 'content' is not, copy the value
        elseif ($this->has('comment') && !$this->has('content')) {
            $this->merge([
                'content' => $this->comment,
            ]);
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'Please select a rating',
            'rating.integer' => 'Rating must be a whole number',
            'rating.min' => 'Rating must be at least 1 star',
            'rating.max' => 'Rating cannot be more than 5 stars',
            'title.required' => 'Please provide a title for your review',
            'title.max' => 'Title cannot be longer than 255 characters',
            'comment.max' => 'Review cannot be longer than 2000 characters',
            'images.*.image' => 'Each file must be an image',
            'images.*.mimes' => 'Supported image formats are: jpeg, png, jpg, gif',
            'images.*.max' => 'Each image must be less than 5MB',
        ];
    }
}
