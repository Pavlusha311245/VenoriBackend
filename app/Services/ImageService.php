<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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

        if (!$this->isExistsImage($collection, $new_name))
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

    /**
     * The private method whether an the image exists in storage
     * @param $collection
     * @param $nameImage
     */
    private function isExistsImage($collection, $imageName){
         return Storage::disk('public')->exists($collection . '/' . $imageName);
    }
}
