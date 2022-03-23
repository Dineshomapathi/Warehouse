<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateUserPasswordRequest extends FormRequest
{
    public function rules()
    {
        return [
            'password' => [
            ]
        ];
    }

    public function authorize()
    {
        return Gate::allows('user_access');
    }
}