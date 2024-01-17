<?php

namespace App\Filament\Pages;

use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use libphonenumber\PhoneNumberType;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class EditProfile extends Page
{
    use InteractsWithForms, InteractsWithActions;

    protected static ?string $navigationIcon = 'fas-user';

    protected static ?string $navigationGroup = 'My Profile';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.candidate.pages.edit-profile';

    public function getTitle(): string|Htmlable
    {
        return __('general.edit_profile');
    }

    public static function getNavigationLabel(): string
    {
        return __('general.my_profile_info');
    }

    /**
     * @return string|null
     */
    public static function getLabel(): ?string
    {
        return __('general.edit_profile');
    }

    public ?array $profileData = [];
    public ?array $addressData = [];

    public function mount(): void
    {
        $this->fillForms();
    }

    protected function getForms(): array
    {
        return [
            'editProfileForm',
            'editAddressForm',
        ];
    }

    public function editProfileForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('general.personal_info'))
                    ->description(__('general.fill_in_the_blanks'))
                    ->schema([
                        TextInput::make('national_id_number')
                            ->required()
                            ->numeric()
                            ->label(__('general.national_id_number')),
                        TextInput::make('passport_number')
                            ->nullable()
                            ->string()
                            ->label(__('general.passport_number')),
                        TextInput::make('name')
                            ->required()
                            ->string()
                            ->label(__('general.name')),
                        TextInput::make('surname')
                            ->required()
                            ->string()
                            ->label(__('general.surname')),
                        DatePicker::make('date_of_birth')
                            ->formatStateUsing(function (User $record) {
                                $dateOfBirth = $record->date_of_birth;
                                if (!$dateOfBirth) {
                                    return null;
                                }
                                $carbonInstance = Carbon::createFromFormat('Y-m-d', $dateOfBirth);
                                if ($carbonInstance === false) {
                                    return null;
                                }
                                if ($carbonInstance->year < 1940) {
                                    return null;
                                }
                                return $dateOfBirth;
                            })
                            ->required()
                            ->label(__('general.date_of_birth')),
                        Select::make('marital_status')
                            ->options([
                                'single' => __('general.single'),
                                'married' => __('general.married'),
                            ])
                            ->required()
                            ->label(__('general.marital_status')),
                        Select::make('gender_id')
                            ->relationship('gender','name')
                            ->required()
                            ->label(__('general.gender_singular')),
                        Select::make('nationality_id')
                            ->relationship('nationality','name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->exists('nationalities', 'id')
                            ->label(__('general.nationality_singular')),
                    ]),
                Section::make(__('general.contact_info'))
                    ->schema([
                        PhoneInput::make('phone')
                            ->defaultCountry('tr')
                            ->onlyCountries(['tr'])
                            ->displayNumberFormat(PhoneInputNumberType::NATIONAL)
                            ->validateFor(
                                country: 'TR',
                                type: PhoneNumberType::MOBILE | PhoneNumberType::FIXED_LINE, // default: null
                                lenient: true
                            ),
                        TextInput::make('email')
                            ->disabled()
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label(__('general.email')),
                        Select::make('country_id')
                            ->required()
                            ->options(Country::all(['id', 'name'])->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->exists('countries', 'id')
                            ->label(__('general.country_singular')),
                        Select::make('city_id')
                            ->options(fn(Get $get): Collection => City::query()
                                ->where('country_id', $get('country_id'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->exists('cities', 'id')
                            ->label(__('general.city_singular')),
                        Select::make('district_id')
                            ->options(fn(Get $get): Collection => District::query()
                                ->where('city_id', $get('city_id'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->exists('districts', 'id')
                            ->label(__('general.district_singular')),
                        TextInput::make('full_address')
                            ->required()
                            ->label(__('general.full_address')),
                    ]),
                Section::make(__('general.educational_info'))
                    ->schema([
                        Select::make('education_level_id')
                            ->relationship('educationLevel','name')
                            ->required()
                            ->exists('education_levels', 'id')
                            ->label(__('general.education_level_singular')),
                        Select::make('languages')
                            ->relationship('languages','name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label(__('general.language_plural')),
                    ]),
                Section::make(__('general.other_info'))
                    ->schema([
                        Select::make('occupation_id')
                            ->relationship('occupation','name')
                            //->getOptionLabelFromRecordUsing(fn($record, $livewire) => __('occupation.'.$record->name))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->exists('occupations', 'id')
                            ->label(__('general.occupation_singular')),
                        TextInput::make('organisation_text')
                            ->label(__('general.organisation_singular')),
                        Select::make('referral_source_id')
                            ->relationship('referralSource', 'name')
                            ->nullable()
                            ->exists('referral_sources', 'id')
                            ->label(__('general.referral_source_question')),
                    ]),
            ])
            ->model($this->getUser())
            ->statePath('profileData');
    }

    public function editAddressForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Address Info')
                    ->schema([
                        Select::make('country_id')
                            ->options(Country::all(['id', 'name'])->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->exists('countries', 'id')
                            ->label(__('general.country_singular')),
                        Select::make('city_id')
                            ->options(fn(Get $get): Collection => City::query()
                                ->where('country_id', $get('country_id'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->exists('cities', 'id')
                            ->label(__('general.city_singular')),
                        Select::make('district_id')
                            ->options(fn(Get $get): Collection => District::query()
                                ->where('city_id', $get('city_id'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->exists('districts', 'id')
                            ->label(__('general.district_singular')),
                    ]),
            ])
            ->model($this->getUser())
            ->statePath('addressData');
    }


    protected function getUser(): Authenticatable & Model
    {
        $user = Filament::auth()->user();

        if (!$user instanceof Model) {
            throw new \Exception('The authenticated user object must be instance of User Model');
        }

        return $user;
    }

    protected function fillForms(): void
    {
        $user = $this->getUser();

        $userData = $user->attributesToArray();

        $address = $user->addresses->first();
        if ($address)
        {
            $addressData = $address->attributesToArray();
            $this->editAddressForm->fill($addressData);

            $userData = array_merge($userData,$addressData);
        }

        $this->editProfileForm->fill($userData);

    }

    protected function getUpdateProfileFormActions(): array
    {
        return [
            Action::make('updateProfileAction')
                ->requiresConfirmation()
                ->label('Save')
                ->submit('editProfileForm'),
        ];
    }

    protected function getUpdateAddressFormActions(): array
    {
        return [
            Action::make('updateAddressAction')
                ->requiresConfirmation()
                ->label('Save')
                ->submit('editAddressForm'),
        ];
    }

    public function updateProfile(): void
    {
        try {
            $data = $this->editProfileForm->getState();
            $this->handleRecordUpdate($this->getUser(), $data);
        } catch (Halt $exception) {
            return;
        }
        $this->sendSuccessNotification();
    }

    public function updateAddress(): void
    {
        try {
            $user = $this->getUser();
            $address = $user->addresses->first();
            if ($address == null) Address::create(['user_id',$user->id]);

            $data = $this->editAddressForm->getState();
            $this->handleAddressUpdate($address, $data);
        } catch (Halt $exception) {
            return;
        }
        $this->sendSuccessNotification();
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $userData = collect($data)->except(['country_id','city_id','district_id','full_address'])->toArray();
        $record->update($userData);
        $record->load('addresses');

        $addressData = collect($data)->only(['country_id','city_id','district_id','full_address'])->toArray();
        $addressData['user_id'] = $record->id;

        $address = $record->addresses?->first();
        if ($address)
        {
            $address->update($addressData);
        }else
        {
            Address::firstOrCreate($addressData);
        }

        return $record;
    }

    protected function handleAddressUpdate(Model $record, array $data): Model
    {
        $addressData = collect($data)->only(['country_id','city_id','district_id'])->toArray();
        $record->update($addressData);
        return $record;
    }

    private function sendSuccessNotification(): void
    {
        Notification::make()
            ->success()
            ->title('Profile Updated')
            ->send();
    }
}
