<?php


namespace App\Services\Rating;


use App\Models\Place;
use App\Models\Review;

/**
 * Class PlaceRatingService
 * @package App\Services\Rating
 */
class PlaceRatingService
{
    /**
     * Method counting place rating
     * @param Review $review
     */
    public function countPlaceRating(Review $review)
    {
        $reviews = Review::where('place_id', $review->place_id)->get();
        $place = Place::where('id', $review->place_id)->first();

        $count = count($reviews);
        $summaryRating = array_sum(array_column(json_decode($reviews), 'rating'));
        $rating = $summaryRating / $count;

        $this->insertRatingToDB($place, $rating);
    }

    /**
     * Method update place rating
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
