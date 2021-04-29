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
        $reviews = Review::findOrFail($review->place_id)->get();
        $place = Place::findOrFail($review->place_id)->first();

        $count = count($reviews);
        $summaryRating = array_sum(array_column(json_decode($reviews), 'rating'));
        $rating = $summaryRating / $count;

        $this->insertRatingToDB($place, $rating, count($reviews));
    }

    /**
     * Method update place rating
     * @param $place
     * @param $rating
     */
    private function insertRatingToDB($place, $rating, $reviewsCount)
    {
        $place->update([
            'rating' => $rating,
            'reviewsCount' => $reviewsCount,
        ]);
        $place->save();
    }
}
