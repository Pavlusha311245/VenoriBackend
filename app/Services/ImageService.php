<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class ImageService
 * @package App\Services
 */
class ImageService{

    public function upload($image){
        $image->file('avatar');

        $image->validate([
            'image' => 'required|image|mimes:jpg,png'
        ]);

        $new_name = rand() . '.' . $image->getClientOriginalExtension();

        $image->move(public_path('storage/images'), $new_name);

        $pathImage = 'public/storage/images/' . $new_name;

        return $pathImage;
    }
}
?>
