<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ReservationTimeRequest
 * @package App\Http\Requests
 */
class ReservationTimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'datetime' => 'required|date_format:d-m-Y g:i A',
            'people' => 'required|',
            'special' => 'required|max:',
            'staying' => 'required',
        ];
    }
}
