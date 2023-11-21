<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'full_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (count(preg_split('/\s+/', $value)) !== 3) {
                        $fail('Не является корректным ФИО');
                    }
                },
            ],
            'birth_date' => ['required', 'date'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'comment' => ['required', 'string', 'max:255'],
        ];
    }
}
