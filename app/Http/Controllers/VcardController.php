<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class VcardController extends Controller
{
    public function downloadVCard($id)
    {
        $user = User::findOrFail($id);

        $vCardString = "BEGIN:VCARD\r\n";
        $vCardString .= "VERSION:3.0\r\n";
        $vCardString .= "FN:" . $user->full_name . "\r\n";
        $vCardString .= "TEL:" . $user->phone . "\r\n";
        $vCardString .= "END:VCARD\r\n";

        $vCardFileName = $user->full_name . '_contact.vcf';

        $headers = array(
            "Content-type"        => "text/vcard",
            "Content-Disposition" => "attachment; filename=$vCardFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        return Response::make($vCardString, 200, $headers);
    }
}
