<?php

namespace App\Filament\Candidate\Pages;

use App\Models\City;
use App\Models\Country;
use App\Models\ForestFireFightingCertificate;
use App\Models\FirstAidCertificate;
use App\Models\RadioCertificate;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DemoDataSeeder;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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

class MyCertificates extends Page
{
    protected static ?string $navigationIcon = 'fas-award';

    protected static string $view = 'filament.candidate.pages.my-certificates';

    protected static ?string $navigationGroup = 'My Profile';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('general.my_certificates');
    }

    public function getTitle(): string|Htmlable
    {
        return __('general.my_certificates');
    }

    public static function getLabel(): ?string
    {
        return __('general.my_certificates');
    }

    public ?array $firstAidCertificateData = [];
    public ?array $radioCertificateData = [];

    public function mount(): void
    {
        $this->fillForms();
    }

    protected function getForms(): array
    {
        return [
            'editCertificatesForm',
        ];
    }

    public function editCertificatesForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('general.radio_info'))
                    ->description(__('general.fill_in_the_blanks'))
                    ->schema([
                        TextInput::make('call_sign')
                            ->required()
                            ->maxLength(255)
                            ->label(__('general.call_sign')),
                        TextInput::make('radio_net_sign')
                            ->nullable()
                            ->maxLength(255)
                            ->label(__('general.radio_net_sign')),
                        TextInput::make('radio_licence_number')
                            ->nullable()
                            ->maxLength(255)
                            ->label(__('general.licence_number')),
                        Select::make('licence_class')
                            ->required()
                            ->options(RadioCertificate::classifications())
                            ->label(__('general.licence_class')),
                        DatePicker::make('radio_date_of_issue')
                            ->required()
                            ->label(__('general.date_of_issue')),
                        DatePicker::make('radio_expiration_date')
                            ->required()
                            ->label(__('general.expiration_date')),
                    ]),
                Section::make(__('general.ff_fighter_info'))
                    ->schema([
                        TextInput::make('registration_number')
                            ->required()
                            ->maxLength(255)
                            ->label(__('general.registration_number')),
                        Select::make('work_area_city_id')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->options(fn(): Collection => City::query()->orderBy('name')
                                ->where('country_id', Country::where('name', 'Türkiye')->first()->id)
                                ->pluck('name', 'id'))
                            ->exists('cities','id')
                            ->label(__('general.work_area_city')),
                        TextInput::make('directorate')
                            ->required()
                            ->maxLength(255)
                            ->label(__('general.directorate')),
                        Select::make('duty')
                            ->required()
                            ->options(ForestFireFightingCertificate::duties())
                            ->label(__('general.duty')),
                    ]),
                Section::make(__('general.first_aid_info'))
                    ->schema([
                        TextInput::make('first_aid_licence_number')
                            ->required()
                            ->maxLength(255)
                            ->label(__('general.licence_number')),
                        TextInput::make('training_institution')
                            ->required()
                            ->maxLength(255)
                            ->label(__('general.training_institution')),
                        DatePicker::make('first_aid_date_of_issue')
                            ->required()
                            ->label(__('general.date_of_issue')),
                        DatePicker::make('first_aid_expiration_date')
                            ->required()
                            ->label(__('general.expiration_date')),
                    ]),
            ])
            ->model($this->getUser())
            ->statePath('firstAidCertificateData');
    }

    protected function getUser(): Authenticatable & Model
    {
        $user = User::with(['radioCertificate','firstAidCertificate'])->find(Filament::auth()->user()->id);

        if (!$user instanceof Model) {
            throw new \Exception('The authenticated user object must be instance of User Model');
        }

        return $user;
    }

    protected function fillForms(): void
    {
        $user = $this->getUser();

        $radioCertificate = $user->radioCertificate;
        $radioData = $radioCertificate ? $radioCertificate->attributesToArray() : [];
        $radioData["radio_licence_number"] = $radioData["licence_number"] ?? null;
        $radioData["radio_date_of_issue"] = $radioData["date_of_issue"] ?? null;
        $radioData["radio_expiration_date"] = $radioData["expiration_date"] ?? null;

        $forestFireFightingCertificate = $user->forestFireFightingCertificate;
        $fffData = $forestFireFightingCertificate ? $forestFireFightingCertificate->attributesToArray() : [];

        $firstAidCertificate = $user->firstAidCertificate;
        $firstAidData = $firstAidCertificate ? $firstAidCertificate->attributesToArray() : [];
        $firstAidData["first_aid_licence_number"] = $firstAidData["licence_number"] ?? null;
        $firstAidData["first_aid_date_of_issue"] = $firstAidData["date_of_issue"] ?? null;
        $firstAidData["first_aid_expiration_date"] = $firstAidData["expiration_date"] ?? null;

        $data = array_merge($radioData,$fffData,$firstAidData);

        $this->editCertificatesForm->fill($data);
    }

    protected function getUpdateCertificatesFormActions(): array
    {
        return [
            Action::make('updateCertificatesAction')
                ->requiresConfirmation()
                ->label('Save')
                ->submit('editCertificatesForm'),
        ];
    }

    public function updateCertificates(): void
    {
        try {
            $data = $this->editCertificatesForm->getState();
            $this->handleRecordUpdate($this->getUser(), $data);
        } catch (Halt $exception) {
            return;
        }
        $this->sendSuccessNotification();
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['user_id'] = $record->id;

        $radioData = collect($data)
            ->only(['call_sign','radio_net_sign','radio_licence_number','licence_class','radio_date_of_issue','radio_expiration_date','user_id'])
            ->toArray();

        $radioData["licence_number"] = $radioData["radio_licence_number"];
        $radioData["date_of_issue"] = $radioData["radio_date_of_issue"];
        $radioData["expiration_date"] = $radioData["radio_expiration_date"];
        $keysToRemove = array('radio_licence_number', 'radio_date_of_issue', 'radio_expiration_date');
        $radioData = array_diff_key($radioData, array_flip($keysToRemove));
        $radioCertificate = $record->radioCertificate;
        $radioCertificate ? $radioCertificate->update($radioData) : RadioCertificate::firstOrCreate($radioData);

        $fffData = collect($data)
            ->only(['registration_number','work_area_city_id','directorate','duty','user_id'])
            ->toArray();

        $forestFireFightingCertificate = $record->forestFireFightingCertificate;
        $forestFireFightingCertificate ? $forestFireFightingCertificate->update($fffData) : ForestFireFightingCertificate::firstOrCreate($fffData);

        $firstAidData = collect($data)
            ->only(['first_aid_licence_number','training_institution','first_aid_date_of_issue','first_aid_expiration_date','user_id'])
            ->toArray();

        $firstAidData["licence_number"] = $firstAidData["first_aid_licence_number"];
        $firstAidData["date_of_issue"] = $firstAidData["first_aid_date_of_issue"];
        $firstAidData["expiration_date"] = $firstAidData["first_aid_expiration_date"];
        $keysToRemove = array('first_aid_licence_number', 'first_aid_date_of_issue', 'first_aid_expiration_date');
        $firstAidData = array_diff_key($firstAidData, array_flip($keysToRemove));
        $firstAidCertificate = $record->firstAidCertificate;
        $firstAidCertificate ? $firstAidCertificate->update($firstAidData) : FirstAidCertificate::firstOrCreate($firstAidData);

        return $record;
    }

    private function sendSuccessNotification(): void
    {
        Notification::make()
            ->success()
            ->title('Certificates Updated')
            ->send();
    }
}
