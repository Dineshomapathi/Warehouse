<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreGuestRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'     => [
                'string',
                'required',
            ],
            'phone' => [
                'required', 
                'numeric',
            ],
            'email'    => [
                'required',
                'unique:guests',
            ]
        ];
    }

    public function authorize()
    {
        return Gate::allows('user_access');
    }
}