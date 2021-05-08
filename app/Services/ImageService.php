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
        $new_name = md5(file_get_contents($image)) . '.' . $image->getClientOriginalExtension();
        $savePath = public_path('storage/' . $collection);

        if (!File::exists($savePath))
            File::makeDirectory($savePath);

        $image->move($savePath, $new_name);

        return 'storage/' . $collection . '/' . $new_name;
    }

    /**
     * The method delete image
     * @param $pathToImage
     */
    public function delete($pathToImage)
    {
        File::delete($pathToImage);
    }

}
