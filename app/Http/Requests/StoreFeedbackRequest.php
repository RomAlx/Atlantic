<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'consent' => ['accepted'],
            'source_page' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'имя',
            'phone' => 'телефон',
            'email' => 'email',
            'message' => 'сообщение',
            'consent' => 'согласие',
            'source_page' => 'страница',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Укажите имя.',
            'name.string' => 'Имя указано некорректно.',
            'name.max' => 'Имя не должно быть длиннее :max символов.',
            'phone.required' => 'Укажите телефон.',
            'phone.string' => 'Телефон указан некорректно.',
            'phone.max' => 'Телефон не должен быть длиннее :max символов.',
            'email.required' => 'Укажите email.',
            'email.email' => 'Введите корректный адрес email.',
            'email.max' => 'Email не должен быть длиннее :max символов.',
            'message.required' => 'Введите сообщение.',
            'message.string' => 'Сообщение указано некорректно.',
            'message.max' => 'Сообщение не должно быть длиннее :max символов.',
            'consent.accepted' => 'Подтвердите согласие на обработку данных.',
            'source_page.string' => 'Страница указана некорректно.',
            'source_page.max' => 'Страница не должна быть длиннее :max символов.',
        ];
    }
}
