<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class BookInventoryRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required', 'string',
            ],
            'model' => [
                'required', 'string',
            ],
            'serial' => [
                'required', 'string',
            ],
            'borrow' => [
                'required', 'string',
            ]
        ];
    }

    public function authorize()
    {
        return Gate::allows('task_access');
    }
}