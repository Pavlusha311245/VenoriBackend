<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class ImageService uploading image it in storage
 * @package App\Services
 */
class ImageService
{
    /**
     * The method return path to image
     * @param $image
     * @return string
     *
     */
    public function upload($image, $collection)
    {
        $new_name = rand() . '.' . $image->getClientOriginalExtension();

        $image->move(public_path('storage/' . $collection), $new_name);

        return 'storage/' . $collection . '/' . $new_name;
    }
}
?>
