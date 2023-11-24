<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class FilamentRequest
{
    public static function get(Request $request): object|null
    {
        try {
            $contentArray = json_decode($request->getContent(),true);
            $snapshot = json_decode($contentArray["components"][0]["snapshot"],true);
            $parameters = $snapshot["data"]["data"][0];

            $result = [];
            foreach ($parameters as $key => $value)
            {
                $result[$key] = is_array($value) ? $value[0] : $value;
            }

            return (object) $result;
        }catch (\Exception $exception)
        {
            return null;
        }
    }
}
