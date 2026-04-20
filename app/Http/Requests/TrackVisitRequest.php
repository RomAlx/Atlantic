<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'path' => ['required', 'string', 'max:255'],
            'route_name' => ['nullable', 'string', 'max:64'],
            'product_slug' => ['nullable', 'string', 'max:255'],
            'support_article_slug' => ['nullable', 'string', 'max:255'],
        ];
    }
}
