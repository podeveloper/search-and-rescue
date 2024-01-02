<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class PhoneHelper
{
    public static function fixTurkishNumbers()
    {
        $replacements = [
            ["from" => "90905", "to" => "905"],
            ["from" => "9005", "to" => "905"],
            ["from" => "0905", "to" => "905"],
            ["from" => "09005", "to" => "905"],
            ["from" => "9000905", "to" => "905"],
            ["from" => "009005", "to" => "905"],
            ["from" => "009005", "to" => "905"],
            ["from" => "009000905", "to" => "905"],
        ];

        $caseStatements = array_map(function ($replacement) {
            return "WHEN phone LIKE '{$replacement['from']}%' THEN CONCAT('{$replacement['to']}', SUBSTRING(phone, " . (strlen($replacement['from']) + 1) . ")) ";
        }, $replacements);

        $caseStatement = implode("", $caseStatements);

        User::whereRaw("LENGTH(phone) > 12")
            ->update(['phone' => DB::raw("CASE $caseStatement ELSE phone END")]);
    }
}
