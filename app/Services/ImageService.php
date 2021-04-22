<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

/**
 * Class ImageService uploading image it in storage
 * @package App\Services
 */
class ImageService
{
    /**
     * The method return path to image
     * @param $image
     * @param $collection
     * @return string
     */
    public function upload($image, $collection)
    {
        $new_name = rand() . '.' . $image->getClientOriginalExtension();
        $savePath = public_path('storage/' . $collection);

        if (!File::exists($savePath))
            File::makeDirectory($savePath);

        $image->move($savePath, $new_name);

        return 'storage/' . $collection . '/' . $new_name;
    }
}
