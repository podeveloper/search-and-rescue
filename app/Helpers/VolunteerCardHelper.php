<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\HtmlString;

class VolunteerCardHelper
{
    public static function getLabel($label, $href = null, $target = null, $icon = null, $justify = null, $textColor = null, $rounded = true)
    {
        $href ??= '##';
        $target ??= '';
        $justify ??= 'center';
        $textColor ??= 'gray';

        $htmlString = '<a href="'.$href.'" target="'.$target.'" style="justify-content: '.$justify.'"
                        class="text-'.$textColor.'-800 font-bold py-2 px-4 '.$rounded.' inline-flex items-center">';
        $htmlString .= $icon ? '<i class="'.$icon.'">&nbsp;</i>' : '';
        $htmlString .='<span>'.ucwords($label).'</span></a>';

        return new HtmlString($htmlString);
    }

    public static function getAddressString(User $record): string | null
    {
        $addressString = null;
        $address = $record->addresses->first();
        if ($address) {
            $addressString = '';
            // Show Only First User's (Foundation's) Full Address on Public Page
            if ($record->id == User::first()->id) $addressString .= $address->full_address ? $address->full_address . ', ' : '';
            $addressString .= $address->district?->name ? $address->district?->name . ', ' : '';
            $addressString .= $address->city?->name ? $address->city?->name . ', ' : '';
            $addressString .= $address->country?->name ?? '';
        }
        return $addressString;
    }


    public static function getAddressUrl(User $record): string
    {
        $addressString = self::getAddressString($record);
        return $addressString ? 'https://www.google.com/maps/search/'.$addressString : '##';
    }

    public static function getWhatsappUrl(User $record): string
    {
        return $record->phone ? 'https://api.whatsapp.com/send/?phone='.$record->phone : '##';
    }

    public static function getEmailUrl(User $record): string
    {
        return $record->email ? 'mailto:'.$record->email: '##';
    }

    public static function getSocialAccountName(User $record, $platform): string
    {
        $account = $record->socialAccounts->where('platform',$platform)->first();
        return $account ? '@'.$account->username : '##';
    }

    public static function getSocialUrl(User $record, $platform): string
    {
        $url = '##';
        $account = $record->socialAccounts->where('platform',$platform)->first();
        if ($account) {
            $url = match ($platform) {
                'facebook' => 'https://www.facebook.com/'.$account->username,
                'twitter' => 'https://twitter.com/'.$account->username,
                'instagram' => 'https://www.instagram.com/'.$account->username,
                'telegram' => 'https://t.me/'.$account->username,
            };
        }

        return $url;
    }

    public static function getOneLineSocialAccountsHtml(User $record)
    {
        $facebookIcon = asset("css/fontawesome/svgs/brands/facebook-colored.svg");
        $twitterIcon = asset("css/fontawesome/svgs/brands/twitter-colored.svg");
        $instagramIcon = asset("css/fontawesome/svgs/brands/instagram-colored.svg");
        $telegramIcon = asset("css/fontawesome/svgs/brands/telegram-colored.svg");

        $facebookLink = self::getSocialUrl($record, 'facebook');
        $twitterLink = self::getSocialUrl($record, 'twitter');
        $instagramLink = self::getSocialUrl($record, 'instagram');
        $telegramLink = self::getSocialUrl($record, 'telegram');

        return new HtmlString('
        <ul style="list-style: none; padding: 0; margin: 0; display: flex;">
            <li style="margin-right: 10px;">
                <a href="' . $facebookLink . '" target="' . ($facebookLink != '##' ? '_blank' : '') . '">
                    <img src="' . $facebookIcon . '" alt="Facebook" style="width: 30px;">
                </a>
            </li>
            <li style="margin-right: 10px;">
                <a href="' . $twitterLink . '" target="' . ($twitterLink != '##' ? '_blank' : '') . '">
                    <img src="' . $twitterIcon . '" alt="Twitter" style="width: 30px;">
                </a>
            </li>
            <li style="margin-right: 10px;">
                <a href="' . $instagramLink . '" target="' . ($instagramLink != '##' ? '_blank' : '') . '">
                    <img src="' . $instagramIcon . '" alt="Instagram" style="width: 30px;">
                </a>
            </li>
            <li>
                <a href="' . $telegramLink . '" target="' . ($telegramLink != '##' ? '_blank' : '') . '">
                    <img src="' . $telegramIcon . '" alt="Telegram" style="width: 30px;">
                </a>
            </li>
        </ul>
        ');
    }
}
