<?php


namespace App\Services;

/**
 * Class RadiusAroundLocationService
 * @package App\Services
 */
class RadiusAroundLocationService
{
    public function coordinates($lat, $lon, $distance)
    {
        $lon_start = $lon - $distance / abs(cos(deg2rad($lat)) * 110.0);
        $lon_end = $lon + $distance / abs(cos(deg2rad($lat)) * 110.0);
        $lat_start = $lat - ($distance / 110.0);
        $lat_end = $lat + ($distance / 110.0);

        $coords = ['lon_start' => $lon_start, 'lon_end' => $lon_end, 'lat_start' => $lat_start, 'lat_end' => $lat_end];

        return $coords;
    }
}
