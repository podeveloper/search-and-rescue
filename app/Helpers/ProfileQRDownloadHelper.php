<?php

namespace App\Helpers;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Response;

class ProfileQRDownloadHelper
{
    public static function svg(User $record)
    {
        if (!$record->username) {
            return null;
        }

        $imageContent = self::generateSVGContent($record->username);
        $filename = $record->username.'_qr_code';

        $headers = [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.'.'svg'.'"',
        ];

        // Generate and return the image file as a download response
        return Response::stream(
            function () use ($imageContent) {
                echo $imageContent;
            },
            200,
            $headers
        );
    }

    public static function generateSVGContent($username)
    {
        if ($username)
        {
            $renderer = new ImageRenderer(
                new RendererStyle(125),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);

            return $writer->writeString(route('volunteers.profile-card',$username));
        }

        return null;
    }

    public static function png(User $record)
    {
        return 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='
            .route('volunteers.profile-card',$record->username);
    }
}
