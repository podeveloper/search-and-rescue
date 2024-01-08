<?php

namespace App\Livewire;

use App\Helpers\VolunteerCardHelper;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class VolunteerCard extends Component implements HasForms, HasInfolists
{
    public $member;

    use InteractsWithInfolists;
    use InteractsWithForms;

    public function mount($member)
    {
        $this->member = $member;
    }

    public function memberInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->member)
            ->schema([
                ImageEntry::make('profile_photo')
                    ->defaultImageUrl(asset('img/avatar.png'))
                    ->label(false)
                    ->extraAttributes(['style' => 'justify-content: center'])
                    ->circular(),
                TextEntry::make('username')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large)
                    ->alignCenter()
                    ->formatStateUsing(fn (string $state): string => '@'.$state)
                    ->label(false),
                TextEntry::make('full_name')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large)
                    ->alignCenter()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->label(false),
                TextEntry::make('languages.name')
                    ->visible(fn(User $record) => $record->languages->count() > 0)
                    ->weight(FontWeight::Bold)
                    ->badge()
                    ->alignCenter()
                    ->extraAttributes([
                        'class' => 'p-2',
                    ]),
                TextEntry::make('')
                    ->weight(FontWeight::Bold)
                    ->alignCenter()
                    ->badge()
                    ->color('info')
                    ->default(function(User $record) {
                        return VolunteerCardHelper::getLabel(
                            'Download VCard',route('download.vcard',$record),
                            '_blank',
                            null,'center');
                    }),
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Personal Info')
                            ->schema([
                                TextEntry::make('phone')
                                    ->weight(FontWeight::Bold)
                                    ->alignCenter()
                                    ->icon('heroicon-m-phone')
                                    ->url(fn(User $record) => VolunteerCardHelper::getWhatsappUrl($record),fn(User $record) => VolunteerCardHelper::getWhatsappUrl($record) != '##')
                                    ->extraAttributes(['class' => 'p-2 bg-gray-200 rounded'])
                                    ->label(function(User $record) {
                                        return VolunteerCardHelper::getLabel(
                                            'Phone','https://api.whatsapp.com/send/?phone='.$record->phone,
                                            '_blank',
                                            'fa-brands fa-whatsapp','center');
                                    }),
                                TextEntry::make('email')
                                    ->weight(FontWeight::Bold)
                                    ->alignCenter()
                                    ->url(fn(User $record) => VolunteerCardHelper::getEmailUrl($record),fn(User $record) => VolunteerCardHelper::getEmailUrl($record) != '##')
                                    ->extraAttributes(['class' => 'p-2 bg-gray-200 rounded'])
                                    ->label(function(User $record) {
                                        return VolunteerCardHelper::getLabel(
                                            'Email','mailto:'.$record->email,
                                            null,
                                            'fa-regular fa-envelope','center');
                                    }),
                                TextEntry::make('address')
                                    ->visible(fn(User $record) => VolunteerCardHelper::getAddressString($record))
                                    ->default(fn(User $record) => VolunteerCardHelper::getAddressString($record))
                                    ->weight(FontWeight::Bold)
                                    ->alignCenter()
                                    ->url(fn(User $record) => VolunteerCardHelper::getAddressUrl($record),fn(User $record) => VolunteerCardHelper::getAddressUrl($record) != '##')
                                    ->extraAttributes(['class' => 'p-2 bg-gray-200 rounded'])
                                    ->label(function(User $record) {
                                        $url = VolunteerCardHelper::getAddressUrl($record);

                                        return VolunteerCardHelper::getLabel(
                                            'Address',$url,
                                            '_blank',
                                            'fa-solid fa-map-pin','center');
                                    }),
                                TextEntry::make('social_accounts')
                                    ->default(fn(User $record)=>VolunteerCardHelper::getOneLineSocialAccountsHtml($record))
                                    ->alignCenter()
                                    ->extraAttributes(['class' => 'p-2 bg-gray-200 rounded'])
                                    ->label(function() {
                                        return VolunteerCardHelper::getLabel(
                                            'Social Accounts','',
                                            '',
                                            'fa-solid fa-globe','center');
                                    }),
                            ]),
                        Tabs\Tab::make('Center Info')
                            ->schema([
                                TextEntry::make('center_phone')
                                    ->default(User::first()->phone)
                                    ->weight(FontWeight::Bold)
                                    ->alignCenter()
                                    ->icon('heroicon-m-phone')
                                    ->url(fn() => VolunteerCardHelper::getWhatsappUrl(User::first()),fn() => VolunteerCardHelper::getWhatsappUrl(User::first()) != '##')
                                    ->extraAttributes(['class' => 'p-2 bg-gray-200 rounded'])
                                    ->label(function() {
                                        return VolunteerCardHelper::getLabel(
                                            'Phone','https://api.whatsapp.com/send/?phone='.User::first()?->phone,
                                            '_blank',
                                            'fa-brands fa-whatsapp','center');
                                    }),
                                TextEntry::make('center_email')
                                    ->default(User::first()->email)
                                    ->weight(FontWeight::Bold)
                                    ->alignCenter()
                                    ->url(fn() => VolunteerCardHelper::getEmailUrl(User::first()),fn() => VolunteerCardHelper::getEmailUrl(User::first()) != '##')
                                    ->extraAttributes(['class' => 'p-2 bg-gray-200 rounded'])
                                    ->label(function() {
                                        return VolunteerCardHelper::getLabel(
                                            'Email','mailto:'.User::first()?->email,
                                            null,
                                            'fa-regular fa-envelope','center');
                                    }),
                                TextEntry::make('center_address')
                                    ->visible(fn() => VolunteerCardHelper::getAddressString(User::first()))
                                    ->default(fn() => VolunteerCardHelper::getAddressString(User::first()))
                                    ->weight(FontWeight::Bold)
                                    ->alignCenter()
                                    ->url(fn() => VolunteerCardHelper::getAddressUrl(User::first()),fn() => VolunteerCardHelper::getAddressUrl(User::first()) != '##')
                                    ->extraAttributes(['class' => 'p-2 bg-gray-200 rounded'])
                                    ->label(function() {
                                        $url = VolunteerCardHelper::getAddressUrl(User::first());

                                        return VolunteerCardHelper::getLabel(
                                            'Address',$url,
                                            '_blank',
                                            'fa-solid fa-map-pin','center');
                                    }),
                                TextEntry::make('social_accounts')
                                    ->default(fn()=>VolunteerCardHelper::getOneLineSocialAccountsHtml(User::first()))
                                    ->alignCenter()
                                    ->extraAttributes(['class' => 'p-2 bg-gray-200 rounded'])
                                    ->label(function() {
                                        return VolunteerCardHelper::getLabel(
                                            'Social Accounts','',
                                            '',
                                            'fa-solid fa-globe','center');
                                    }),
                            ]),
                        Tabs\Tab::make('Links')
                            ->schema([
                                // ...
                            ]),
                    ]),
                TextEntry::make('registered')
                    ->default('The person in this profile is a registered volunteer of our foundation.')
                    ->extraAttributes(['class' => 'p-2'])
                    ->label(false),
            ]);
    }

    public function render()
    {
        return view('livewire.volunteer-card');
    }
}
