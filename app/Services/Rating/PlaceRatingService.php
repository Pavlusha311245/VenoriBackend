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
    public function updatePlaceRatingAndReviewsCount(Review $review)
    {
        $reviews = Review::where('place_id', $review->place_id)->get();
        $place = Place::findOrFail($review->place_id);

        $count = count($reviews) == 0 ? 1 : count($reviews);
        $summaryRating = array_sum(array_column(json_decode($reviews), 'rating'));
        $rating = $summaryRating / $count;

        $place->update([
            'rating' => $rating,
            'reviewsCount' => $count,
        ]);
    }
}
