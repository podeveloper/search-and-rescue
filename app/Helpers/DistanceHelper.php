<?php

namespace App\Helpers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class DistanceHelper
{
    public static function generateDistanceMatrixUrl($origin, $destination, $apiKey)
    {
        // Encode the origin and destination addresses
        $encodedOrigin = urlencode($origin);
        $encodedDestination = urlencode($destination);

        // Build the URL
        $url = "https://api.distancematrix.ai/maps/api/distancematrix/json?origins={$encodedOrigin}&destinations={$encodedDestination}&key={$apiKey}";

        return $url;
    }

    public static function getDistanceMatrix($origin, $destination)
    {
        $apiKey = config('services.distance_matrix.apiKey');
        $url = self::generateDistanceMatrixUrl($origin, $destination, $apiKey);

        $response = Http::get($url);

        if ($response->successful()) {
            $decodedResponse = $response->json();

            if ($decodedResponse) {
                $data = $response->json()["rows"][0]["elements"][0];
                $array = [
                    'distance' => $data['distance']['value'] ?? null,
                    'duration' => $data['duration']['value'] ?? null,
                ];
            } else {
                return dd("Failed to decode JSON");
            }
        } else {
            return dd("HTTP request failed");
        }

        return $array;
    }

    public static function updateDistanceAndDuration(Address $address)
    {
        $origin = self::getAddressString($address);
        $destination = self::getAddressString(User::first()->addresses->first());

        $result = DistanceHelper::getDistanceMatrix($origin, $destination);

        if ($result) {
            $address->distance_from_center = $result['distance'] ?? null;
            $address->estimated_time_of_arrival = $result['duration'] ?? null;
        }
    }

    public static function getAddressString(Address $address): string | null
    {
        $addressString = '';

        if ($address) {
            $addressString .= $address->full_address ? $address->full_address . ', ' : '';
            $addressString .= $address->district?->name ? $address->district?->name . ', ' : '';
            $addressString .= $address->city?->name ? $address->city?->name . ', ' : '';
            $addressString .= $address->country?->name ?? '';
        }
        return $addressString;
    }
}
