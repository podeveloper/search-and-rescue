<?php

namespace App\Filament\Coordinator\Resources;

use App\Events\ApplicationApproved;
use App\Events\ApplicationRejected;
use App\Filament\Coordinator\Pages\Active;
use App\Filament\Coordinator\Pages\Applicants;
use App\Filament\Coordinator\Pages\Archived;
use App\Filament\Coordinator\Pages\Candidates;
use App\Filament\Coordinator\Pages\Duplicates;
use App\Filament\Coordinator\Pages\Expired;
use App\Filament\Coordinator\Pages\Inactive;
use App\Filament\Coordinator\Pages\Rejecteds;
use App\Filament\Coordinator\Pages\Trashed;
use App\Filament\Coordinator\Pages\Volunteers;
use App\Filament\Coordinator\Resources\UserResource\RelationManagers\AddressesRelationManager;
use App\Filament\Coordinator\Resources\UserResource\RelationManagers\DriverLicencesRelationManager;
use App\Filament\Coordinator\Resources\UserResource\RelationManagers\FirstAidCertificateRelationManager;
use App\Filament\Coordinator\Resources\UserResource\RelationManagers\ForestFireFightingCertificateRelationManager;
use App\Filament\Coordinator\Resources\UserResource\RelationManagers\HealthProfileRelationManager;
use App\Filament\Coordinator\Resources\UserResource\RelationManagers\RadioCertificateRelationManager;
use App\Filament\Coordinator\Resources\UserResource\RelationManagers\RegistrationQuestionAnswersRelationManager;
use App\Filament\Coordinator\Resources\UserResource\RelationManagers\SocialAccountsRelationManager;
use App\Filament\Coordinator\Resources\UserResource\RelationManagers\VehiclesRelationManager;
use App\Filament\Reference\Resources\UserResource\RelationManagers\CertificatesRelationManager;
use App\Filament\Reference\Resources\UserResource\RelationManagers\DrivingEquipmentsRelationManager;
use App\Filament\Reference\Resources\UserResource\RelationManagers\EquipmentsRelationManager;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Candidate\Resources;
use App\Helpers\ProfileQRDownloadHelper;
use App\Helpers\RoleHelper;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\DriverLicence;
use App\Models\DrivingEquipment;
use App\Models\Equipment;
use App\Models\HealthProfile;
use App\Models\RadioCertificate;
use App\Models\Todo;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleBrand;
use App\Models\VehicleCategory;
use App\Models\VehicleModel;
use App\Traits\NavigationLocalizationTrait;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use libphonenumber\PhoneNumberType;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

use STS\FilamentImpersonate\Tables\Actions\Impersonate;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class UserResource extends Resource
{
    use NavigationLocalizationTrait;
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Human Resources';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name', 'surname', 'full_name', 'email', 'phone','nationality.name','educationLevel.name','occupation.name','occupation_text','organisation.name','organisation_text','languages.name','tags.name','categories.name','certificates.title','addresses.country.name','addresses.city.name','addresses.district.name',
            'driverLicences.class',
            'firstAidCertificate.training_institution',
            'healthProfile.medications','healthProfile.allergies','healthProfile.medical_conditions','healthProfile.vision_aids',
            'healthProfile.prosthetics','healthProfile.emergency_contact_name','healthProfile.emergency_contact_phone','healthProfile.blood_type',
            'radioCertificate.call_sign','radioCertificate.radio_net_sign','radioCertificate.licence_class',
            'vehicles.category.name','vehicles.brand.name','vehicles.model.name','vehicles.color','vehicles.licence_plate',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('profile_photo')
                    ->columnSpanFull()
                    ->image()
                    ->openable()
                    ->previewable()
                    ->maxSize(10000)
                    ->label(__('general.profile_photo'))
                    ->saveUploadedFileUsing(function ($file, $get) {
                        $fullName = strtolower(str_replace(' ', '_', $get('name') . ' ' . $get('surname')));
                        return $file->storeAs('profile-photos',
                            $fullName . '_' . now()->toDateString() . '_profile_photo' . '.png',
                            'public');
                    }),
                Forms\Components\FileUpload::make('resume')
                    ->columnSpanFull()
                    ->acceptedFileTypes(['application/pdf'])
                    ->openable()
                    ->previewable()
                    ->maxSize(10000)
                    ->label(__('general.resume'))
                    ->saveUploadedFileUsing(function ($file, $get) {
                        $fullName = strtolower(str_replace(' ', '_', $get('name') . ' ' . $get('surname')));
                        return $file->storeAs("resumes", $fullName . '_' . now()->toDateString() . '_resume.pdf');
                    }),
                Forms\Components\Section::make('Personal Info')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->disabled(),
                        Forms\Components\TextInput::make('national_id_number')
                            ->required()
                            ->numeric()
                            ->label(__('general.national_id_number')),
                        Forms\Components\TextInput::make('passport_number')
                            ->nullable()
                            ->string()
                            ->label(__('general.passport_number')),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->string()
                            ->label(__('general.name')),
                        Forms\Components\TextInput::make('surname')
                            ->required()
                            ->string()
                            ->label(__('general.surname')),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->formatStateUsing(function ($record) {
                                if (!$record || !$record->date_of_birth) {
                                    return null;
                                }

                                $dateOfBirth = $record->date_of_birth;
                                $carbonInstance = Carbon::createFromFormat('Y-m-d', $dateOfBirth);

                                if ($carbonInstance === false || $carbonInstance->year < 1940) {
                                    return null;
                                }

                                return $dateOfBirth;
                            })
                            ->nullable()
                            ->label(__('general.date_of_birth')),
                        Forms\Components\Select::make('marital_status')
                            ->options([
                                'single' => 'Single',
                                'married' => 'Married',
                            ])
                            ->nullable()
                            ->label(__('general.marital_status')),
                        Forms\Components\Select::make('gender_id')
                            ->relationship('gender', 'name')
                            ->nullable()
                            ->exists('genders', 'id')
                            ->label(__('general.gender_singular')),
                        Forms\Components\Select::make('nationality_id')
                            ->relationship('nationality', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->exists('nationalities', 'id')
                            ->label(__('general.nationality_singular')),
                    ]),
                Forms\Components\Section::make('Contact & Account Info')
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
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label(__('general.email')),
                        Forms\Components\TextInput::make('username')
                            ->nullable()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label(__('general.username')),
                    ]),
                Forms\Components\Section::make('Educational Info')
                    ->schema([
                        Forms\Components\Select::make('education_level_id')
                            ->relationship('educationLevel', 'name')
                            ->nullable()
                            ->exists('education_levels', 'id')
                            ->label(__('general.education_level_singular')),
                        Forms\Components\Select::make('languages')
                            ->relationship('languages', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label(__('general.language_plural')),
                    ]),
                Forms\Components\Section::make('Other Info')
                    ->schema([
                        Forms\Components\Select::make('occupation_id')
                            ->relationship('occupation', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->exists('occupations', 'id')
                            ->label(__('general.occupation_singular')),
                        Forms\Components\TextInput::make('occupation_text')
                            ->nullable()
                            ->string()
                            ->visible(auth()->user()?->is_admin || auth()->user()?->hasRole(['coordinator'])),
                        Forms\Components\Select::make('organisation_id')
                            ->relationship('organisation', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->exists('organisations', 'id')
                            ->label(__('general.organisation_singular')),
                        Forms\Components\TextInput::make('organisation_text')
                            ->nullable()
                            ->string()
                            ->visible(auth()->user()?->is_admin || auth()->user()?->hasRole(['coordinator'])),

                    ]),
                Forms\Components\Section::make('Panel Info')
                    ->schema([
                        Forms\Components\Select::make('events')
                            ->relationship('events', 'title')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label(__('general.event_plural')),
                        Forms\Components\Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label(__('general.tag_plural')),
                        Forms\Components\Select::make('categories')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->string()
                                    ->label(__('general.name')),
                            ])
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label(__('general.user_category_plural')),
                        Forms\Components\Select::make('certificates')
                            ->relationship('certificates', 'title')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label(__('general.certificate_plural')),
                        Forms\Components\TextInput::make('reference_name')
                            ->nullable()
                            ->string(),
                        Forms\Components\Select::make('reference_id')
                            ->label('Reference Person')
                            ->relationship('reference', 'full_name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->exists('users', 'id')
                            ->label(__('general.user_reference')),
                        Forms\Components\Select::make('referral_source_id')
                            ->relationship('referralSource', 'name')
                            ->nullable()
                            ->exists('referral_sources', 'id')
                            ->label(__('general.referral_source_singular')),
                        Forms\Components\Textarea::make('note')
                            ->nullable()
                            ->label(__('general.note'))
                            ->visible(auth()->user()?->is_admin || auth()->user()?->hasRole(['coordinator'])),
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label(__('general.role_plural')),
                        Forms\Components\Toggle::make('is_admin')
                            ->onIcon('heroicon-m-bolt')
                            ->offIcon('heroicon-m-user')
                            ->inline(false)
                            ->label(__('general.user_is_admin'))
                            ->visible(auth()->user()->is_admin),
                        Forms\Components\Toggle::make('is_active')
                            ->onIcon('heroicon-m-bolt')
                            ->offIcon('heroicon-m-user')
                            ->inline(false)
                            ->label(__('general.user_is_active'))
                            ->visible(auth()->user()->hasRole(['coordinator'])),
                        Forms\Components\Hidden::make('password')
                            ->default(function ($record) {
                                return (!$record || !$record->password) ? Hash::make(Str::random(8)) : $record->password;
                            })
                            ->disabled(function ($record) {
                                return ($record || $record?->password);
                            })
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->weight(FontWeight::Bold)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->disk('public')
                    ->visibility('private')
                    ->defaultImageUrl(asset('img/avatar.png'))
                    ->width(50)
                    ->height(50)
                    ->url(fn(User $record): string => $record->profile_photo ? Storage::disk('public')->url($record->profile_photo) : asset('img/avatar.png'), true)
                    ->circular()
                    ->toggleable()
                    ->label(__('general.profile_photo')),
                Tables\Columns\IconColumn::make('resume')
                    ->icon('heroicon-o-identification')
                    ->url(function(User $record){
                        if ($record->resume)
                        {
                            $pathParts = explode('/', $record->resume);
                            $folder = $pathParts[0];
                            $fileName = end($pathParts);
                            return route('files.show',['folder' => $folder,'filename' => $fileName]);
                        }
                        return asset('img/none.png');
                    }, true)
                    ->toggleable()
                    ->label(__('general.resume')),
                Tables\Columns\TextColumn::make('national_id_number')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.national_id_number')),
                Tables\Columns\TextColumn::make('passport_number')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.passport_number')),
                Tables\Columns\TextColumn::make('name')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Name copied')
                    ->copyMessageDuration(1500)
                    ->label(__('general.name')),
                Tables\Columns\TextColumn::make('surname')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Surname copied')
                    ->copyMessageDuration(1500)
                    ->label(__('general.surname')),
                Tables\Columns\TextColumn::make('full_name')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Full name copied')
                    ->copyMessageDuration(1500)
                    ->label(__('general.full_name')),
                Tables\Columns\TextColumn::make('phone')
                    ->icon('heroicon-m-phone')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Phone number copied')
                    ->copyMessageDuration(1500)
                    ->label(__('general.phone')),
                Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500)
                    ->label(__('general.email')),
                Tables\Columns\TextColumn::make('username')
                    ->icon('heroicon-m-user')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Username address copied')
                    ->copyMessageDuration(1500)
                    ->label(__('general.username')),
                Tables\Columns\TextColumn::make('gender.name')
                    ->icon('heroicon-m-finger-print')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.gender_singular')),
                Tables\Columns\TextColumn::make('nationality.name')
                    ->icon('heroicon-m-flag')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.nationality_singular')),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->icon('heroicon-m-calendar-days')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.date_of_birth')),
                Tables\Columns\TextColumn::make('marital_status')
                    ->icon('heroicon-m-puzzle-piece')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.marital_status')),
                Tables\Columns\TextColumn::make('educationLevel.name')
                    ->icon('heroicon-m-academic-cap')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.education_level_singular')),
                Tables\Columns\TextColumn::make('referralSource.name')
                    ->icon('heroicon-m-megaphone')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.referral_source_singular')),
                Tables\Columns\TextColumn::make('occupation.name')
                    ->icon('heroicon-m-briefcase')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.occupation_singular')),
                Tables\Columns\TextColumn::make('organisation_text')
                    ->icon('heroicon-m-building-office-2')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.organisation_singular')),
                Tables\Columns\TextColumn::make('address_country')
                    ->icon('heroicon-m-map')
                    ->getStateUsing(function (User $record) {
                        return $record->addresses->first()?->country?->name;
                    })
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.country_singular')),
                Tables\Columns\TextColumn::make('address_city')
                    ->icon('heroicon-m-map-pin')
                    ->getStateUsing(function (User $record) {
                        return $record->addresses->first()?->city?->name;
                    })
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.city_singular')),
                Tables\Columns\TextColumn::make('address_district')
                    ->icon('heroicon-m-map-pin')
                    ->getStateUsing(function (User $record) {
                        return $record->addresses->first()?->district?->name;
                    })
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.district_singular')),
                Tables\Columns\TextColumn::make('address_full')
                    ->getStateUsing(function (User $record) {
                        return $record->addresses->first()?->full_address;
                    })
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.full_address')),
                Tables\Columns\TextColumn::make('addresses.distance_from_center')
                    ->getStateUsing(function (User $record) {
                        $address = $record->addresses->first();
                        return $address?->distance_from_center ?  number_format($address->distance_from_center / 1000,2) : 0;
                    })
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.distance_km')),
                Tables\Columns\TextColumn::make('estimated_time_of_arrival')
                    ->getStateUsing(function (User $record) {
                        $address = $record->addresses->first();
                        return $address?->estimated_time_of_arrival ?  number_format($address->estimated_time_of_arrival / 60,2) : 0;
                    })
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.estimated_time_min')),
                Tables\Columns\TextColumn::make('equipments.name')
                    ->badge()
                    ->toggleable()
                    ->label(__('general.equipments')),
                Tables\Columns\TextColumn::make('drivingEquipments.name')
                    ->badge()
                    ->toggleable()
                    ->label(__('general.driving_equipments')),
                Tables\Columns\TextColumn::make('is_active')
                    ->formatStateUsing(fn(User $record) => $record->is_active ? 'Active' : 'Inactive')
                    ->wrap()
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.user_is_active')),
                Tables\Columns\TextColumn::make('languages.name')
                    ->icon('heroicon-m-language')
                    ->badge()
                    ->toggleable()
                    ->searchable()
                    ->label(__('general.language_plural')),
                Tables\Columns\TextColumn::make('roles.name')
                    ->icon('heroicon-m-check')
                    ->badge()
                    ->toggleable()
                    ->label(__('general.role_plural')),
                Tables\Columns\TextColumn::make('events.title')
                    ->icon('heroicon-m-swatch')
                    ->badge()
                    ->toggleable()
                    ->label(__('general.event_plural')),
                Tables\Columns\TextColumn::make('tags.name')
                    ->icon('heroicon-m-hashtag')
                    ->badge()
                    ->toggleable()
                    ->label(__('general.tag_plural')),
                Tables\Columns\TextColumn::make('categories.name')
                    ->icon('heroicon-m-rectangle-stack')
                    ->badge()
                    ->toggleable()
                    ->label(__('general.user_category_plural')),
                Tables\Columns\TextColumn::make('healthProfile.blood_type')
                    ->badge()
                    ->toggleable()
                    ->label(__('general.blood_type')),
                Tables\Columns\TextColumn::make('driverLicences.class')
                    ->badge()
                    ->toggleable()
                    ->label(__('general.driver_licence_classes')),
                Tables\Columns\TextColumn::make('vehicles')
                    ->formatStateUsing(function (User $record){
                        return new HtmlString($record->vehicles->map(fn($vehicle)=>$vehicle->combined)->implode('<br>'));
                    })
                    ->badge()
                    ->toggleable()
                    ->label(__('general.vehicles')),
                Tables\Columns\TextColumn::make('radioCertificate.call_sign')
                    ->badge()
                    ->toggleable()
                    ->label(__('general.radio_certificate_call_sign')),
                Tables\Columns\TextColumn::make('radioCertificate.licence_class')
                    ->badge()
                    ->toggleable()
                    ->label(__('general.radio_certificate_licence_class')),
                Tables\Columns\TextColumn::make('forest_fire_fighter')
                    ->default(fn(User $record)=> $record->forestFireFightingCertificate ? __('general.exist') : __('general.not_exist'))
                    ->badge()
                    ->toggleable()
                    ->label(__('general.forest_fire_fighter')),
                Tables\Columns\TextColumn::make('first_aid_certificate')
                    ->default(fn(User $record)=> $record->firstAidCertificate ? __('general.exist') : __('general.not_exist'))
                    ->badge()
                    ->toggleable()
                    ->label(__('general.first_aid_certificate')),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.user_last_login_date')),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.created_date')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.updated_date')),
                Tables\Columns\TextColumn::make('note')
                    ->limit(20)
                    ->wrap()
                    ->icon('heroicon-o-user')
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.note')),
            ])
            ->filters([
                Tables\Filters\Filter::make('national_id_number')
                    ->form([
                        Forms\Components\TextInput::make('national_id_number')
                            ->label(__('general.national_id_number')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $national_id_number = $data['national_id_number'];
                        return $national_id_number ? $query->where('national_id_number', 'like', '%' . $national_id_number . '%') : $query;
                    }),
                Tables\Filters\Filter::make('passport_number')
                    ->form([
                        Forms\Components\TextInput::make('passport_number')
                            ->label(__('general.passport_number')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $passport_number = $data['passport_number'];
                        return $passport_number ? $query->where('passport_number', 'like', '%' . $passport_number . '%') : $query;
                    }),
                Tables\Filters\Filter::make('name')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label(__('general.name')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $name = $data['name'];
                        return $name ? $query->where('name', 'like', '%' . $name . '%') : $query;
                    }),
                Tables\Filters\Filter::make('surname')
                    ->form([
                        Forms\Components\TextInput::make('surname')
                            ->label(__('general.surname')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $surname = $data['surname'];
                        return $surname ? $query->where('surname', 'like', '%' . $surname . '%') : $query;
                    }),
                Tables\Filters\Filter::make('phone')
                    ->form([
                        Forms\Components\TextInput::make('phone')
                            ->label(__('general.phone')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $phone = $data['phone'];
                        return $phone ? $query->where('phone', 'like', '%' . $phone . '%') : $query;
                    }),
                Tables\Filters\Filter::make('email')
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->label(__('general.email')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $email = $data['email'];
                        return $email ? $query->where('email', 'like', '%' . $email . '%') : $query;
                    }),
                Tables\Filters\Filter::make('username')
                    ->form([
                        Forms\Components\TextInput::make('username')
                            ->label(__('general.username')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $username = $data['username'];
                        return $username ? $query->where('username', 'like', '%' . $username . '%') : $query;
                    }),
                Tables\Filters\SelectFilter::make('gender')
                    ->relationship('gender', 'name')
                    ->multiple()
                    ->preload()
                    ->label(__('general.gender_singular')),
                Tables\Filters\SelectFilter::make('nationality')
                    ->relationship('nationality', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('general.nationality_singular')),
                Tables\Filters\Filter::make('country_id')
                    ->form([
                        Forms\Components\Select::make('country_id')
                            ->label('Country')
                            ->options(Country::all()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->label(__('general.country_singular')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $country_id = $data['country_id'];
                        return $country_id ? $query->whereHas('addresses', function ($q) use ($country_id) {
                            $q->where('country_id', '=', $country_id);
                        }) : $query;
                    }),
                Tables\Filters\SelectFilter::make('city_id')
                    ->relationship('addresses.city', 'name')
                    ->searchable()
                    ->label(__('general.city_singular')),
                Tables\Filters\SelectFilter::make('district')
                    ->relationship('addresses.district', 'name')
                    ->searchable()
                    ->label(__('general.district_singular')),
                Tables\Filters\Filter::make('max_distance')
                    ->form([
                        Forms\Components\TextInput::make('max_distance')
                            ->numeric()
                            ->label(__('general.max_distance')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $distance = (double) $data['max_distance'];
                        $distance = $distance != null ? $distance * 1000 : null;
                        return $distance != null ? $query->whereHas('addresses', function ($query) use ($distance) {
                            $query->where('distance_from_center', '<=', $distance);
                        }) : $query;
                    }),
                Tables\Filters\Filter::make('max_estimated_time')
                    ->form([
                        Forms\Components\TextInput::make('max_estimated_time')
                            ->numeric()
                            ->label(__('general.estimated_time_min')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $max_estimated_time = (double) $data['max_estimated_time'];
                        $max_estimated_time = $max_estimated_time != null ? $max_estimated_time * 60 : null;
                        return $max_estimated_time != null ? $query->whereHas('addresses', function ($query) use ($max_estimated_time) {
                            $query->where('estimated_time_of_arrival', '<=', $max_estimated_time);
                        }) : $query;
                    }),
                Tables\Filters\SelectFilter::make('educationLevel')
                    ->relationship('educationLevel', 'name')
                    ->multiple()
                    ->preload()
                    ->label(__('general.education_level_singular')),
                Tables\Filters\SelectFilter::make('referralSource')
                    ->relationship('referralSource', 'name')
                    ->multiple()
                    ->preload()
                    ->label(__('general.referral_source_singular')),
                Tables\Filters\SelectFilter::make('occupation')
                    ->relationship('occupation', 'name')
                    ->multiple()
                    ->preload()
                    ->label(__('general.occupation_singular')),
                Tables\Filters\SelectFilter::make('organisation')
                    ->relationship('organisation', 'name')
                    ->multiple()
                    ->preload()
                    ->label(__('general.organisation_singular')),
                Tables\Filters\SelectFilter::make('marital_status')
                    ->options([
                        'single' => 'Single',
                        'married' => 'Married',
                    ])
                    ->multiple()
                    ->label(__('general.marital_status')),
                Tables\Filters\SelectFilter::make('languages')
                    ->relationship('languages', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('general.language_plural')),
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('general.role_plural')),
                Tables\Filters\SelectFilter::make('events')
                    ->relationship('events', 'title')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('general.event_plural')),
                Tables\Filters\SelectFilter::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('general.tag_plural')),
                Tables\Filters\SelectFilter::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('general.user_category_plural')),
                Tables\Filters\Filter::make('has_equipments')
                    ->form([
                        Forms\Components\Select::make('equipments')
                            ->options(Equipment::all()->pluck('name','id'))
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label(__('general.has_equipments')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $equipments = $data['equipments'];
                        return $equipments
                            ? $query
                                ->whereHas('equipments', function ($query) use ($equipments) {
                                    $query->whereIn('id', $equipments);
                                }, '=', count($equipments))
                            : $query;
                    }),
                Tables\Filters\Filter::make('has_no_equipments')
                    ->form([
                        Forms\Components\Select::make('missing_equipments')
                            ->options(Equipment::all()->pluck('name','id'))
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label(__('general.has_no_equipments')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $missing_equipments = $data['missing_equipments'];
                        return $missing_equipments
                            ? $query->whereDoesntHave('equipments', function ($query) use ($missing_equipments) {
                                $query->whereIn('id', $missing_equipments);
                            }) : $query;
                    }),
                Tables\Filters\Filter::make('has_driving_equipments')
                    ->form([
                        Forms\Components\Select::make('driving_equipments')
                            ->options(DrivingEquipment::all()->pluck('name','id'))
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label(__('general.has_driving_equipments')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $driving_equipments = $data['driving_equipments'];
                        return $driving_equipments
                            ? $query
                                ->whereHas('drivingEquipments', function ($query) use ($driving_equipments) {
                                    $query->whereIn('id', $driving_equipments);
                                }, '=', count($driving_equipments))
                            : $query;
                    }),
                Tables\Filters\Filter::make('has_no_driving_equipment')
                    ->form([
                        Forms\Components\Select::make('driving_equipment')
                            ->options(DrivingEquipment::all()->pluck('name','id'))
                            ->preload()
                            ->multiple()
                            ->searchable()
                            ->label(__('general.has_no_driving_equipments')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $driving_equipment = $data['driving_equipment'];
                        return $driving_equipment
                            ? $query->whereDoesntHave('drivingEquipments', function ($query) use ($driving_equipment) {
                                $query->whereIn('id', $driving_equipment);
                            }) : $query;
                    }),
                Tables\Filters\Filter::make('min_age')
                    ->form([
                        Forms\Components\TextInput::make('min_age')->numeric()
                            ->label(__('general.user_min_age')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $age = $data['min_age'];
                        return $age ? $query->where('date_of_birth', '<=', now()->subYears($age)) : $query;
                    }),
                Tables\Filters\Filter::make('max_age')
                    ->form([
                        Forms\Components\TextInput::make('max_age')->numeric()
                            ->label(__('general.user_max_age')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $age = $data['max_age'];
                        return $age ? $query->where('date_of_birth', '>=', now()->subYears($age)) : $query;
                    }),
                Tables\Filters\Filter::make('blood_type')
                    ->form([
                        Forms\Components\Select::make('blood_type')
                            ->options(Healthprofile::bloodTypes())
                            ->label(__('general.blood_type')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $blood_type = $data['blood_type'];
                        return $blood_type
                            ? $query->whereHas('healthProfile', function ($query) use ($blood_type) {
                                $query->where('blood_type', '=', $blood_type);
                            }) : $query;
                    }),
                Tables\Filters\Filter::make('vehicle_category')
                    ->form([
                        Forms\Components\Select::make('vehicle_category')
                            ->options(VehicleCategory::all()->pluck('name', 'id'))
                            ->searchable()
                            ->label(__('general.vehicle_category')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $vehicle_category = $data['vehicle_category'];
                        return $vehicle_category
                            ? $query->whereHas('vehicles', function ($query) use ($vehicle_category) {
                                $query->where('category_id', '=', $vehicle_category);
                            }) : $query;
                    }),
                Tables\Filters\Filter::make('vehicle_brand')
                    ->form([
                        Forms\Components\Select::make('vehicle_brand')
                            ->options(VehicleBrand::all()->pluck('name', 'id'))
                            ->searchable()
                            ->label(__('general.vehicle_brand')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $vehicle_brand = $data['vehicle_brand'];
                        return $vehicle_brand
                            ? $query->whereHas('vehicles', function ($query) use ($vehicle_brand) {
                                $query->where('brand_id', '=', $vehicle_brand);
                            }) : $query;
                    }),
                Tables\Filters\Filter::make('vehicle_model')
                    ->form([
                        Forms\Components\Select::make('vehicle_model')
                            ->options(VehicleModel::all()->pluck('name', 'id'))
                            ->searchable()
                            ->label(__('general.vehicle_model')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $vehicle_model = $data['vehicle_model'];
                        return $vehicle_model
                            ? $query->whereHas('vehicles', function ($query) use ($vehicle_model) {
                                $query->where('model_id', '=', $vehicle_model);
                            }) : $query;
                    }),
                Tables\Filters\Filter::make('vehicle_color')
                    ->form([
                        Forms\Components\Select::make('vehicle_color')
                            ->options(Vehicle::colors())
                            ->label(__('general.vehicle_color')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $vehicle_color = $data['vehicle_color'];
                        return $vehicle_color
                            ? $query->whereHas('vehicles', function ($query) use ($vehicle_color) {
                                $query->where('color', '=', $vehicle_color);
                            }) : $query;
                    }),
                Tables\Filters\Filter::make('licence_plate')
                    ->form([
                        Forms\Components\TextInput::make('licence_plate')
                            ->label(__('general.licence_plate')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $licence_plate = $data['licence_plate'];
                        return $licence_plate
                            ? $query->whereHas('vehicles', function ($query) use ($licence_plate) {
                                $query->where('licence_plate', 'like', '%' . $licence_plate . '%');
                            }) : $query;
                    }),
                Tables\Filters\Filter::make('driver_licence_class')
                    ->form([
                        Forms\Components\Select::make('driver_licence_class')
                            ->options(DriverLicence::classifications())
                            ->label(__('general.driver_licence_class')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $driver_licence_class = $data['driver_licence_class'];
                        return $driver_licence_class
                            ? $query->whereHas('driverLicences', function ($query) use ($driver_licence_class) {
                                $query->where('class', '=', $driver_licence_class);
                            }) : $query;
                    }),
                Tables\Filters\Filter::make('radio_call_sign')
                    ->form([
                        Forms\Components\TextInput::make('radio_call_sign')
                            ->label(__('general.radio_call_sign')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $radio_call_sign = $data['radio_call_sign'];
                        return $radio_call_sign
                            ? $query->whereHas('radioCertificate', function ($query) use ($radio_call_sign) {
                                $query->where('call_sign', 'like', '%' . $radio_call_sign . '%');
                            }) : $query;
                    }),
                Tables\Filters\Filter::make('radio_licence_class')
                    ->form([
                        Forms\Components\Select::make('radio_licence_class')
                            ->options(RadioCertificate::classifications())
                            ->label(__('general.radio_licence_class')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $radio_licence_class = $data['radio_licence_class'];
                        return $radio_licence_class
                            ? $query->whereHas('radioCertificate', function ($query) use ($radio_licence_class) {
                                $query->where('licence_class', '=', $radio_licence_class);
                            }) : $query;
                    }),
                Tables\Filters\Filter::make('forest_fire_fighter')
                    ->form([
                        Forms\Components\Select::make('forest_fire_fighter')
                            ->options(['yes' => __('general.exist'), 'no' => __('general.not_exist')])
                            ->label(__('general.forest_fire_fighter')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $status = $data['forest_fire_fighter'];
                        return match ($status)
                        {
                            'yes' => $query->whereHas('forestFireFightingCertificate'),
                            'no' => $query->whereDoesntHave('forestFireFightingCertificate'),
                            default => $query,
                        };
                    }),
                Tables\Filters\Filter::make('first_aid_certificate')
                    ->form([
                        Forms\Components\Select::make('first_aid_certificate')
                            ->options(['yes' => __('general.exist'), 'no' => __('general.not_exist')])
                            ->label(__('general.first_aid_certificate')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $status = $data['first_aid_certificate'];
                        return match ($status)
                        {
                            'yes' => $query->whereHas('firstAidCertificate'),
                            'no' => $query->whereDoesntHave('firstAidCertificate'),
                            default => $query,
                        };
                    }),
                Tables\Filters\Filter::make('note')->form([
                    Forms\Components\TextInput::make('note')
                        ->label(__('general.note')),
                ])->query(function (Builder $query, array $data): Builder {
                    return $data['note'] ? $query->where('note', 'like', '%' . $data['note'] . '%') : $query;
                }),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_after')
                            ->label(__('general.created_date')),
//                        Forms\Components\DatePicker::make('created_until')->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_after'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            );
//                            ->when(
//                                $data['created_until'],
//                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
//                            );
                    }),
                Tables\Filters\Filter::make('last_login_at')
                    ->form([
                        Forms\Components\DatePicker::make('last_login_after')
                            ->label(__('general.user_last_login_date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $lastLoginAt = $data['last_login_after'];
                        return $lastLoginAt ? $query->whereDate('last_login_at', '>=', $lastLoginAt) : $query;
                    }),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->paginated([10, 25, 50])
            ->defaultSort('full_name', 'asc')
            ->filtersFormColumns(4)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('download_svg')
                        ->visible(fn(User $record) => $record->username)
                        ->label('Download QR (Svg)')
                        ->icon('heroicon-m-qr-code')
                        ->action(function (User $record) {
                            return ProfileQRDownloadHelper::svg($record);
                        }),
                    Tables\Actions\Action::make('download_png')
                        ->visible(fn(User $record) => $record->username)
                        ->label('Download QR (Png)')
                        ->url(fn(User $record) => ProfileQRDownloadHelper::png($record),true)
                        ->icon('heroicon-m-qr-code')
                        ->action(function (User $record) {
                            return ProfileQRDownloadHelper::png($record);
                        }),
                    Tables\Actions\Action::make('approve')
                        ->icon('heroicon-m-check')
                        ->visible(fn (User $user) => $user->hasRole('applicant'))
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (User $user) => ApplicationApproved::dispatch($user)),
                    Tables\Actions\Action::make('reject')
                        ->icon('heroicon-o-x-mark')
                        ->visible(fn (User $user) => $user->hasRole('applicant'))
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (User $user) => ApplicationRejected::dispatch($user)),
                    Tables\Actions\Action::make('make_volunteer')
                        ->icon('heroicon-m-check')
                        ->visible(fn (User $user) => $user->hasRole('field trainee'))
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (User $user) => UserCompletedFieldTraining::dispatch($user)),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger'),
                    Tables\Actions\Action::make('transfer_data')
                        ->icon('heroicon-m-arrow-right')
                        ->color('success')
                        ->form(function (User $record){
                            return [
                                Forms\Components\Select::make('selected_user')
                                    ->options(User::query()
                                        ->where('id','!=',$record->id)
                                        ->orderBy('name')
                                        ->orderBy('surname')
                                        ->get(['id','full_name'])
                                        ->map(function($volunteer){
                                            $volunteer->full_name .= ' ('.$volunteer->id.')';
                                            return $volunteer;
                                        })->pluck('full_name','id')
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->exists('users','id')
                                    ->label('Select User'),
                            ];
                        })
                        ->action(function (array $data, User $user){
                            $transferredUser = User::find($data['selected_user']);

                            foreach ($user->events as $event) {
                                // Check if the event is not already associated with the second user
                                if (!$transferredUser->events->contains($event->id)) {
                                    // Attach the event to the second user
                                    $transferredUser->events()->attach($event->id);
                                }

                                // Detach the event from the first user
                                $user->events()->detach($event->id);
                            }

                            Notification::make()
                                ->success()
                                ->title('Event records are transferred!')
                                ->send();
                        })
                        ->visible(fn(User $user) => auth()->user()->id == 1),
                    Impersonate::make()
                        ->label('Impersonate')
                        ->color('gray')
                        ->redirectTo(URL::to('/candidate')),
                    Tables\Actions\ForceDeleteAction::make()
                        ->requiresConfirmation()
                        ->visible(fn(User $record) => $record->trashed() && auth()->user()->id == 1 && auth()->user()->is_admin)
                ]),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                ExportBulkAction::make(),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('move_to_rejected')
                        ->label('Move To Rejected')
                        ->color('danger')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(function ($record) {
                                RoleHelper::moveTo($record,'rejected');
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('make_expired')
                        ->label('Move To Expired')
                        ->color('danger')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(function ($record) {
                                $record->assignRole('expired');
                            });

                            Notification::make()
                                ->success()
                                ->title('Role Assigned')
                                ->body('The "expired" role has been assigned to the users.')
                                ->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('move_to_candidate')
                        ->label('Move To Candidate')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(function ($record) {
                                RoleHelper::moveTo($record,'candidate');
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('move_to_official')
                        ->label('Move To Official')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(function ($record) {
                                RoleHelper::moveTo($record,'official');
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('mark_as_active')
                        ->label('Mark as Active')
                        ->color('info')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['is_active' => true]);
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('mark_as_inactive')
                        ->label('Mark as Inactive')
                        ->color('warning')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['is_active' => false]);
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('attach_category')
                        ->label('Attach Category')
                        ->color('info')
                        ->form(function (){
                            return [
                                Forms\Components\Select::make('selected_category')
                                    ->options(
                                        UserCategory::all()->pluck('name','id')
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->exists('user_categories','id')
                                    ->label('Select Category'),
                            ];
                        })
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $category = UserCategory::find($data['selected_category']);
                            $records->each(function ($record) use ($category) {
                                if (!$record->categories->contains($category->id)) {
                                    $record->categories()->attach($category->id);
                                }
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('detach_category')
                        ->label('Detach Category')
                        ->color('info')
                        ->form(function (){
                            return [
                                Forms\Components\Select::make('selected_category')
                                    ->options(
                                        UserCategory::all()->pluck('name','id')
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->exists('user_categories','id')
                                    ->label('Select Category'),
                            ];
                        })
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $category = UserCategory::find($data['selected_category']);
                            $records->each(function ($record) use ($category) {
                                if ($record->categories->contains($category->id)) {
                                    $record->categories()->detach($category->id);
                                }
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->visible(auth()->user()->id == 1),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AddressesRelationManager::class,
            SocialAccountsRelationManager::class,
            CertificatesRelationManager::class,
            RegistrationQuestionAnswersRelationManager::class,
            DriverLicencesRelationManager::class,
            FirstAidCertificateRelationManager::class,
            RadioCertificateRelationManager::class,
            HealthProfileRelationManager::class,
            VehiclesRelationManager::class,
            ForestFireFightingCertificateRelationManager::class,
            EquipmentsRelationManager::class,
            DrivingEquipmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Coordinator\Resources\UserResource\Pages\ListUsers::route('/'),
            'create' => \App\Filament\Coordinator\Resources\UserResource\Pages\CreateUser::route('/create'),
            'edit' => \App\Filament\Coordinator\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
            'rejecteds' => Rejecteds::route('/rejecteds'),
            'applicants' => Applicants::route('/applicants'),
            'expired' => Expired::route('/expired'),
            'officials' => Volunteers::route('/officials'),
            'candidates' => Candidates::route('/candidates'),
            'active' => Active::route('/active'),
            'inactive' => Inactive::route('/inactive'),
            'archived' => Archived::route('/archived'),
            'duplicates' => Duplicates::route('/duplicates'),
            'trashed' => Trashed::route('/trashed'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = strtolower(str_replace(' ', '_', self::getModelLabel())) . '_count';

        return Cache::rememberForever($cacheKey, function () {
            return self::getModel()::count();
        });
    }
}
