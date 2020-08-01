<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\TimeType;

class TimeRecordRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'record' => ['filled', new TimeType],
            'created' =>'filled|date|date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
        'record.filled' =>'入力してください',
        'created.filled' =>'入力してください',
        'created.date' =>'正しい形式で入力してください'
        ];
    }
}
