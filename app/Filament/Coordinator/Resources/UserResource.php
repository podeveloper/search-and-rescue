<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Coordinator\Resources\UserResource\RelationManagers\AddressesRelationManager;
use App\Filament\Coordinator\Resources\UserResource\RelationManagers\DriverLicencesRelationManager;
use App\Filament\Coordinator\Resources\UserResource\RelationManagers\FirstAidCertificateRelationManager;
use App\Filament\Coordinator\Resources\UserResource\RelationManagers\HealthProfileRelationManager;
use App\Filament\Coordinator\Resources\UserResource\RelationManagers\RadioCertificateRelationManager;
use App\Filament\Coordinator\Resources\UserResource\RelationManagers\RegistrationQuestionAnswersRelationManager;
use App\Filament\Coordinator\Resources\UserResource\RelationManagers\SocialAccountsRelationManager;
use App\Filament\Reference\Resources\UserResource\RelationManagers\CertificatesRelationManager;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Candidate\Resources;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Todo;
use App\Models\User;
use App\Traits\NavigationLocalizationTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    use NavigationLocalizationTrait;
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Human Resources';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'surname', 'full_name', 'email', 'phone','nationality.name','educationLevel.name','occupation.name','occupation_text','organisation.name','organisation_text','languages.name','tags.name','categories.name','certificates.title','addresses.country.name','addresses.city.name','addresses.district.name'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('profile_photo')
                    ->columnSpan(2)
                    ->image()
                    ->maxSize(10000)
                    ->label(__('general.profile_photo')),
                Forms\Components\Section::make('Personal Info')
                    ->schema([
                        Forms\Components\TextInput::make('national_id_number')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('passport_number')
                            ->nullable()
                            ->string(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->string()
                            ->label(__('general.name')),
                        Forms\Components\TextInput::make('surname')
                            ->required()
                            ->string()
                            ->label(__('general.surname')),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->default(now())
                    ->maxDate(now())
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
                            ->exists('genders','id')
                            ->label(__('gender.singular')),
                        Forms\Components\Select::make('nationality_id')
                            ->relationship('nationality', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->exists('nationalities','id')
                            ->label(__('nationality.singular')),
                    ]),
                Forms\Components\Section::make('Contact Info')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->nullable()
                            ->tel()
                            ->maxLength(255)
                            ->label(__('general.phone')),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label(__('general.email')),
                    ]),
                Forms\Components\Section::make('Educational Info')
                    ->schema([
                        Forms\Components\Select::make('education_level_id')
                            ->relationship('educationLevel', 'name')
                            ->nullable()
                            ->exists('education_levels','id')
                            ->label(__('education_level.singular')),
                        Forms\Components\Select::make('languages')
                            ->relationship('languages', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label(__('language.plural')),
                    ]),
                Forms\Components\Section::make('Other Info')
                    ->schema([
                        Forms\Components\Select::make('occupation_id')
                            ->relationship('occupation', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->exists('occupations','id')
                            ->label(__('occupation.singular')),
                        Forms\Components\TextInput::make('occupation_text')
                            ->nullable()
                            ->string()
                            ->visible(auth()->user()?->is_admin || auth()->user()?->hasRole(['coordinator'])),
                        Forms\Components\Select::make('organisation_id')
                            ->relationship('organisation', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->exists('organisations','id')
                            ->label(__('organisation.singular')),
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
                            ->label(__('event.plural')),
                        Forms\Components\Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label(__('tag.plural')),
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
                            ->label(__('user_category.plural')),
                        Forms\Components\Select::make('certificates')
                            ->relationship('certificates', 'title')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label(__('certificate.plural')),
                        Forms\Components\TextInput::make('reference_name')
                            ->nullable()
                            ->string(),
                        Forms\Components\Select::make('reference_id')
                            ->label('Reference Person')
                            ->relationship('reference', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->exists('users','id')
                            ->label(__('user.reference')),
                        Forms\Components\Select::make('referral_source_id')
                            ->relationship('referralSource', 'name')
                            ->nullable()
                            ->exists('referral_sources','id')
                            ->label(__('referral_source.singular')),
                        Forms\Components\Textarea::make('note')
                            ->nullable()
                            ->maxLength(300)
                            ->label(__('general.note'))
                            ->visible(auth()->user()?->is_admin || auth()->user()?->hasRole(['coordinator'])),
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label(__('role.plural')),
                        Forms\Components\Toggle::make('is_admin')
                            ->onIcon('heroicon-m-bolt')
                            ->offIcon('heroicon-m-user')
                            ->inline(false)
                            ->label(__('user.is_admin'))
                            ->visible(auth()->user()->is_admin),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\ImageColumn::make('profile_photo')
                        ->defaultImageUrl(asset('img/avatar.png'))
                        ->width(50)
                        ->height(50)
                        ->circular()
                        ->toggleable()
                        ->label(__('general.profile_photo')),
                    Tables\Columns\TextColumn::make('national_id_number')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable()
                        ->toggleable(),
                    Tables\Columns\TextColumn::make('passport_number')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable()
                        ->toggleable(),
                    Tables\Columns\TextColumn::make('name')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable()
                        ->toggleable()
                        ->label(__('general.name')),
                    Tables\Columns\TextColumn::make('surname')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable()
                        ->toggleable()
                        ->label(__('general.surname')),
                    Tables\Columns\Layout\Stack::make([
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
                    ])
                ]),
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('gender.name')
                                ->icon('heroicon-m-finger-print')
                                ->searchable()
                                ->sortable()
                                ->toggleable()
                                ->label(__('gender.singular')),
                            Tables\Columns\TextColumn::make('nationality.name')
                                ->icon('heroicon-m-flag')
                                ->searchable()
                                ->sortable()
                                ->toggleable()
                                ->label(__('nationality.singular')),
                        ]),
                        Tables\Columns\Layout\Stack::make([
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
                        ]),
                    ])
                        ->extraAttributes(['style' => 'margin: 20px; !important;']),

                    Tables\Columns\Layout\Split::make([
                       Tables\Columns\Layout\Stack::make([
                           Tables\Columns\TextColumn::make('educationLevel.name')
                               ->icon('heroicon-m-academic-cap')
                               ->searchable()
                               ->sortable()
                               ->toggleable()
                               ->label(__('education_level.singular')),
                           Tables\Columns\TextColumn::make('referralSource.name')
                               ->icon('heroicon-m-megaphone')
                               ->searchable()
                               ->sortable()
                               ->toggleable()
                               ->label(__('referral_source.singular')),
                           Tables\Columns\TextColumn::make('occupation.name')
                               ->icon('heroicon-m-briefcase')
                               ->searchable()
                               ->sortable()
                               ->toggleable()
                               ->label(__('occupation.singular')),
                           Tables\Columns\TextColumn::make('organisation.name')
                               ->icon('heroicon-m-building-office-2')
                               ->searchable()
                               ->sortable()
                               ->toggleable()
                               ->label(__('organisation.singular')),
                       ]),
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('addresses.country_id')
                                ->icon('heroicon-m-map')
                                ->label('Country')
                                ->getStateUsing( function (User $record){
                                    return $record->addresses->first()?->country?->name;
                                })
                                ->toggleable()
                                ->label(__('country.singular')),
                            Tables\Columns\TextColumn::make('addresses.city_id')
                                ->icon('heroicon-m-map-pin')
                                ->label('City')
                                ->getStateUsing( function (User $record){
                                    return $record->addresses->first()?->city?->name;
                                })
                                ->toggleable()
                                ->label(__('city.singular')),
                            Tables\Columns\TextColumn::make('addresses.district')
                                ->icon('heroicon-m-map-pin')
                                ->label('District')
                                ->getStateUsing( function (User $record){
                                    return $record->addresses->first()?->district?->name;
                                })
                                ->toggleable()
                                ->label(__('district.singular')),
                       ]),
                    ])
                        ->extraAttributes(['style' => 'margin: 20px; !important;']),

                    Tables\Columns\Layout\Split::make([
                       Tables\Columns\Layout\Stack::make([
                           Tables\Columns\TextColumn::make('languages.name')
                               ->icon('heroicon-m-language')
                               ->badge()
                               ->toggleable()
                               ->searchable()
                               ->label(__('language.plural')),
                       ]),
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('roles.name')
                                ->icon('heroicon-m-check')
                                ->badge()
                                ->toggleable()
                                ->label(__('role.plural')),
                       ]),
                    ])
                        ->extraAttributes(['style' => 'margin: 20px; !important;']),


                    Tables\Columns\Layout\Split::make([
                       Tables\Columns\Layout\Stack::make([
                           Tables\Columns\TextColumn::make('events.title')
                               ->icon('heroicon-m-swatch')
                               ->badge()
                               ->toggleable()
                               ->label(__('event.plural')),
                       ]),
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('tags.name')
                                ->icon('heroicon-m-hashtag')
                                ->badge()
                                ->toggleable()
                                ->label(__('tag.plural')),
                            Tables\Columns\TextColumn::make('categories.name')
                                ->icon('heroicon-m-rectangle-stack')
                                ->badge()
                                ->toggleable()
                                ->label(__('user_category.plural')),
                       ]),
                    ])
                        ->extraAttributes(['style' => 'margin: 20px; !important;']),

                    Tables\Columns\Layout\Split::make([
                       Tables\Columns\Layout\Stack::make([
                           Tables\Columns\TextColumn::make('certificates.title')
                               ->icon('heroicon-m-sparkles')
                               ->badge()
                               ->toggleable()
                               ->label(__('certificate.plural')),
                       ]),
                        Tables\Columns\Layout\Stack::make([
                            //
                       ]),
                    ])
                        ->extraAttributes(['style' => 'margin: 20px; !important;']),

                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\Layout\Split::make([
                                Tables\Columns\TextColumn::make('last_login_at')
                                    ->date('Y-m-d')
                                    ->sortable()
                                    ->toggleable()
                                    ->label(__('user.last_login_date')),
                            ]),
                            Tables\Columns\Layout\Split::make([
                                Tables\Columns\TextColumn::make('created_at')
                                    ->date('Y-m-d')
                                    ->sortable()
                                    ->toggleable()
                                    ->label(__('general.created_date')),
                            ]),
                            Tables\Columns\Layout\Split::make([
                                Tables\Columns\TextColumn::make('updated_at')
                                    ->date('Y-m-d')
                                    ->sortable()
                                    ->toggleable()
                                    ->label(__('general.updated_date')),
                            ]),
                       ]),
                    ])
                        ->extraAttributes(['style' => 'margin: 20px; !important;']),

                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('note')
                            ->icon('heroicon-o-user')
                            ->searchable()
                            ->toggleable()
                            ->label(__('general.note')),
                    ])

                ])->collapsible()
            ])
            ->filters([
                Tables\Filters\Filter::make('national_id_number')
                    ->form([
                        Forms\Components\TextInput::make('national_id_number'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $national_id_number = $data['national_id_number'];
                        return $national_id_number ? $query->where('national_id_number', 'like', '%' . $national_id_number . '%') : $query;
                    }),
                Tables\Filters\Filter::make('passport_number')
                    ->form([
                        Forms\Components\TextInput::make('passport_number'),
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
                Tables\Filters\SelectFilter::make('gender')
                    ->relationship('gender', 'name')
                    ->multiple()
                    ->preload()
                    ->label(__('gender.singular')),
                Tables\Filters\SelectFilter::make('nationality')
                    ->relationship('nationality', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('nationality.singular')),
                Tables\Filters\Filter::make('country_id')
                    ->form([
                        Forms\Components\Select::make('country_id')
                            ->label('Country')
                            ->options(Country::all()->pluck('name','id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->label(__('country.singular')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $country_id = $data['country_id'];
                        return $country_id ? $query->whereHas('addresses',function ($q) use($country_id) {
                           $q->where('country_id','=',$country_id);
                        }) : $query;
                    }),
                Tables\Filters\SelectFilter::make('city_id')
                    ->relationship('addresses.city', 'name')
                    ->searchable()
                    ->label(__('city.singular')),
                Tables\Filters\SelectFilter::make('district')
                    ->relationship('addresses.district', 'name')
                    ->searchable()
                    ->label(__('district.singular')),
                Tables\Filters\SelectFilter::make('educationLevel')
                    ->relationship('educationLevel', 'name')
                    ->multiple()
                    ->preload()
                    ->label(__('education_level.singular')),
                Tables\Filters\SelectFilter::make('referralSource')
                    ->relationship('referralSource', 'name')
                    ->multiple()
                    ->preload()
                    ->label(__('referral_source.singular')),
                Tables\Filters\SelectFilter::make('occupation')
                    ->relationship('occupation', 'name')
                    ->multiple()
                    ->preload()
                    ->label(__('occupation.singular')),
                Tables\Filters\SelectFilter::make('organisation')
                    ->relationship('organisation', 'name')
                    ->multiple()
                    ->preload()
                    ->label(__('organisation.singular')),
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
                    ->label(__('language.plural')),
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('role.plural')),
                Tables\Filters\SelectFilter::make('events')
                    ->relationship('events', 'title')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('event.plural')),
                Tables\Filters\SelectFilter::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('tag.plural')),
                Tables\Filters\SelectFilter::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('user_category.plural')),
                Tables\Filters\Filter::make('min_age')
                    ->form([
                        Forms\Components\TextInput::make('min_age')->numeric()
                            ->label(__('user.min_age')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $age = $data['min_age'];
                        return $age ? $query->where('date_of_birth', '<=', now()->subYears($age)) : $query;
                    }),
                Tables\Filters\Filter::make('max_age')
                    ->form([
                        Forms\Components\TextInput::make('max_age')->numeric()
                            ->label(__('user.max_age')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $age = $data['max_age'];
                        return $age ? $query->where('date_of_birth', '>=', now()->subYears($age)) : $query;
                    }),
                Tables\Filters\Filter::make('note')->form([
                    Forms\Components\TextInput::make('note')
                        ->label(__('general.note')),
                ])->query(function (Builder $query, array $data): Builder {
                    return $data['note'] ? $query->where('note', 'like', '%'.$data['note'].'%') : $query;
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
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            );
//                            ->when(
//                                $data['created_until'],
//                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
//                            );
                    }),
                Tables\Filters\Filter::make('last_login_at')
                    ->form([
                        Forms\Components\DatePicker::make('last_login_after')
                            ->label(__('user.last_login_date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $lastLoginAt = $data['last_login_after'];
                        return $lastLoginAt ? $query->whereDate('last_login_at', '>=', $lastLoginAt) : $query;
                    }),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->paginated([
                10, 25, 50, 100])
            ->defaultSort('full_name','asc')
            ->filtersFormColumns(4)
            ->actions([

                Tables\Actions\EditAction::make(),
                Impersonate::make()->redirectTo(URL::to('/admin'))
            ])
            ->bulkActions([

                ExportBulkAction::make(),
                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make(),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Coordinator\Resources\UserResource\Pages\ListUsers::route('/'),
            'create' => \App\Filament\Coordinator\Resources\UserResource\Pages\CreateUser::route('/create'),
            'edit' => \App\Filament\Coordinator\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = strtolower(str_replace(' ', '_', self::getModelLabel())) . '_count';

        return Cache::rememberForever($cacheKey, function () {
            return self::getModel()::count();
        });
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
