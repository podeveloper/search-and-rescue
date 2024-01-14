<?php

namespace App\Filament\Candidate\Pages;

use App\Models\HealthProfile;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use libphonenumber\PhoneNumberType;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class MyHealthInfo extends Page
{
    protected static ?string $navigationIcon = 'fas-heart-pulse';

    protected static string $view = 'filament.candidate.pages.my-health-info';

    protected static ?string $navigationGroup = 'My Profile';

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('general.my_health_info');
    }

    public function getTitle(): string|Htmlable
    {
        return __('general.my_health_info');
    }

    public static function getLabel(): ?string
    {
        return __('general.my_health_info');
    }

    public ?array $healthProfileData = [];

    public function mount(): void
    {
        $this->fillForms();
    }

    protected function getForms(): array
    {
        return [
            'editHealthProfileForm',
        ];
    }

    public function editHealthProfileForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('general.emergency_info'))
                    ->description(__('general.fill_in_the_blanks'))
                    ->schema([
                        Select::make('blood_type')
                            ->required()
                            ->columnSpanFull()
                            ->options(Healthprofile::bloodTypes()),
                        TextInput::make('emergency_contact_name')
                            ->required()
                            ->string()
                            ->label(__('general.emergency_contact_name')),
                        PhoneInput::make('emergency_contact_phone')
                            ->defaultCountry('tr')
                            ->onlyCountries(['tr'])
                            ->displayNumberFormat(PhoneInputNumberType::NATIONAL)
                            ->validateFor(
                                country: 'TR',
                                type: PhoneNumberType::MOBILE | PhoneNumberType::FIXED_LINE, // default: null
                                lenient: true
                            ),
                    ]),
                Section::make(__('general.medical_info'))
                    ->schema([
                        Textarea::make('medications')
                            ->nullable()
                            ->label(__('general.medications')),
                        Textarea::make('allergies')
                            ->nullable()
                            ->label(__('general.allergies')),
                        Textarea::make('medical_conditions')
                            ->nullable()
                            ->label(__('general.medical_conditions')),
                        Textarea::make('vision_aids')
                            ->nullable()
                            ->label(__('general.vision_aids')),
                        Textarea::make('prosthetics')
                            ->nullable()
                            ->label(__('general.prosthetics')),
                        Textarea::make('other_health_information')
                            ->nullable()
                            ->label(__('general.other_health_information')),
                    ]),
            ])
            ->model($this->getUser())
            ->statePath('healthProfileData');
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

        $healthProfile = $user->healthProfile;
        $healthData = $healthProfile ? $healthProfile->attributesToArray() : [];

        $this->editHealthProfileForm->fill($healthData);
    }

    protected function getUpdateHealthProfileFormActions(): array
    {
        return [
            Action::make('updateHealthProfileAction')
                ->requiresConfirmation()
                ->label('Save')
                ->submit('editHealthProfileForm'),
        ];
    }

    public function updateHealthProfile(): void
    {
        try {
            $data = $this->editHealthProfileForm->getState();
            $this->handleRecordUpdate($this->getUser(), $data);
        } catch (Halt $exception) {
            return;
        }
        $this->sendSuccessNotification();
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $healthProfile = $record->healthProfile;

        if ($healthProfile)
        {
            $healthProfile->update($data);
        }else
        {
            $data['user_id'] = $record->id;
            HealthProfile::firstOrCreate($data);
        }

        return $record;
    }

    private function sendSuccessNotification(): void
    {
        Notification::make()
            ->success()
            ->title('Health Profile Updated')
            ->send();
    }
}
