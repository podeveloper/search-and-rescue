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

    public static function getQueryParams()
    {
        $uri = request()->getRequestUri();
        parse_str(parse_url($uri, PHP_URL_QUERY), $queryParams);
        return $queryParams;
    }

    public static function getFilterValue(string $filterKey)
    {
        $queryParams = self::getQueryParams();

        $value = null;

        if (isset($queryParams['tableFilters'][$filterKey])) {
            if (isset($queryParams['tableFilters'][$filterKey][$filterKey])) {
                $value = $queryParams['tableFilters'][$filterKey][$filterKey];
            }elseif (isset($queryParams['tableFilters'][$filterKey]["values"])) {
                $value = $queryParams['tableFilters'][$filterKey]["values"][0];
            }elseif (isset($queryParams['tableFilters'][$filterKey]["isActive"])) {
                $value = $queryParams['tableFilters'][$filterKey]["isActive"];
            }
        }

        return match ($value){
            "null" => null,
            "false" => false,
            "true" => true,
            default => $value,
        };
    }
}
