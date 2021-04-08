<?php

namespace App\Services\PlaceMenu;

use App\Models\Place;
use App\Models\ProductsOfPlace;
use App\Models\Product;
use App\Models\Category;

/**
 * Class PlaceMenuService
 *
 * @package App\Services\PlaceMenu
 */
class PlaceMenuService
{
    /**
     * Method counting place rating
     *
     * @param Place $place
     */
    public function createMenu(Place $place)
    {
        $productsOfPlace = ProductsOfPlace::where('place_id', $place->id)->get();
    }

    /**
     * Method update place rating
     *
     * @param $place
     * @param $rating
     */
    private function insertRatingToDB($place, $rating)
    {
        $place->update([
            'rating' => $rating,
        ]);
        $place->save();
    }
}
