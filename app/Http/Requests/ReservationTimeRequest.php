<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ReservationTimeRequest
 *
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
            'datetime' => 'required|date_format:Y-m-d G:i:s',
            'people' => 'required|min:1',
            'staying' => 'required|min:0.5',
        ];
    }
}
