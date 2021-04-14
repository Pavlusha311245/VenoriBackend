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
            'date' => 'date_format:Y-m-d|after:yesterday',
            'people' => 'numeric|min:0|not_in:0',
            'staying' => 'numeric|min:0.5|not_in:0',
        ];
    }
}
