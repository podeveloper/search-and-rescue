<?php

namespace App\Filament\Candidate\Pages\Auth;

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\EducationLevel;
use App\Models\Gender;
use App\Models\Language;
use App\Models\Nationality;
use App\Models\Occupation;
use App\Models\ReferralSource;
use App\Models\RegistrationQuestion;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class Apply extends BaseRegister
{
    protected function getForms(): array
    {
        $fieldsArray = [
            FileUpload::make('profile_photo')
                ->columnSpanFull()
                ->image()
                ->openable()
                ->previewable()
                ->maxSize(10000)
                ->label(__('general.profile_photo'))
                ->saveUploadedFileUsing(function ($file, $get) {
                    $fullName = strtolower(str_replace(' ','_',$get('name').' '.$get('surname')));
                    return $file->storeAs('profile-photos',
                        $fullName.'_'.now()->toDateString().'_profile_photo'.'.png',
                        'public');
                }),
            FileUpload::make('resume')
                ->columnSpanFull()
                ->acceptedFileTypes(['application/pdf'])
                ->openable()
                ->previewable()
                ->maxSize(10000)
                ->label(__('general.resume'))
                ->saveUploadedFileUsing(function ($file, $get) {
                    $fullName = strtolower(str_replace(' ','_',$get('name').' '.$get('surname')));
                    return $file->storeAs("resumes", $fullName.'_'.now()->toDateString().'_resume.pdf');
                }),
            TextInput::make('name')
                ->required()
                ->string()
                ->label(__('general.name')),
            TextInput::make('surname')
                ->required()
                ->string()
                ->label(__('general.surname')),
            Select::make('gender_id')
                ->preload()
                ->options(Gender::all(['id','name'])->pluck('name','id'))
                ->required()
                ->exists('genders','id')
                ->label(__('general.gender_singular')),
            DatePicker::make('date_of_birth')
                ->maxDate(now()->subYears(10))
                ->required()
                ->label(__('general.date_of_birth')),
            Select::make('nationality_id')
                ->searchable()
                ->preload()
                ->options(Nationality::all(['id','name'])->pluck('name','id'))
                ->required()
                ->exists('nationalities','id')
                ->label(__('general.nationality_singular')),
            Select::make('country_id')
                ->required()
                ->options(Country::all(['id','name'])->pluck('name','id'))
                ->searchable()
                ->preload()
                ->live()
                ->required()
                ->exists('countries','id')
                ->label(__('general.country_singular')),
            Select::make('city_id')
                ->options(fn(Get $get): Collection => City::query()
                    ->where('country_id',$get('country_id'))
                    ->pluck('name','id'))
                ->searchable()
                ->preload()
                ->nullable()
                ->exists('cities','id')
                ->label(__('general.city_singular')),
            Select::make('district_id')
                ->options(fn(Get $get): Collection => District::query()
                    ->where('city_id',$get('city_id'))
                    ->pluck('name','id'))
                ->searchable()
                ->preload()
                ->nullable()
                ->exists('districts','id')
                ->label(__('general.district_singular')),
            TextInput::make('email')
                ->required()
                ->email()
                ->unique(table: User::class, column: 'email')
                ->maxLength(255)
                ->label(__('general.email')),
            TextInput::make('phone')
                ->required()
                ->tel()
                ->maxLength(255)
                ->label(__('general.phone')),
            Select::make('occupation_id')
                ->searchable()
                ->preload()
                ->options(Occupation::all(['id','name'])->pluck('name','id'))
                ->required()
                ->exists('occupations','id')
                ->label(__('general.occupation_singular')),
            Select::make('education_level_id')
                ->searchable()
                ->preload()
                ->options(EducationLevel::all(['id','name'])->pluck('name','id'))
                ->required()
                ->exists('education_levels','id')
                ->label(__('general.education_level_singular')),
            Select::make('speaking_languages')
                ->multiple()
                ->searchable()
                ->preload()
                ->options(Language::all(['id','name'])->pluck('name','id'))
                ->label(__('general.language_plural')),
            Select::make('referral_source_id')
                ->options(ReferralSource::all(['id','name'])->pluck('name','id'))
                ->required()
                ->exists('referral_sources','id')
                ->label(__('general.referral_source_question')),
            TextInput::make('reference_name')
                ->nullable()
                ->string()
                ->label(__('general.user_reference')),
            TextInput::make('instagram')
                ->nullable()
                ->string()
                ->placeholder('username'),
            TextInput::make('facebook')
                ->nullable()
                ->string()
                ->placeholder('username'),
            TextInput::make('twitter')
                ->nullable()
                ->string()
                ->placeholder('username'),
            Checkbox::make('kvkk_approval')
                ->label(fn()=>new HtmlString('<a href="https://makudder.org/kvkk/" target="_blank"><span style="color:red">KVKK Metnini</span></a> Okudum Onaylıyorum.'))
                ->required(),
            Checkbox::make('explicit_consent_approval')
                ->label(fn()=>new HtmlString('<a href="https://makudder.org/makud-acik-riza-beyani/" target="_blank"><span style="color:red">Açık Rıza Beyanını</span></a> Okudum Onaylıyorum.'))
                ->required(),
        ];

        $questions = RegistrationQuestion::where('is_published',true)->orderBy('sort_order','asc')->get();

        $questionsArray = [];
        foreach ($questions as $question)
        {
            $questionsArray[] = Textarea::make('question_' .$question->id)
                ->required()
                ->maxLength(300)
                ->label($question->text);
        }

        $applicationForm = array_merge($fieldsArray,$questionsArray);

        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema(
                        $applicationForm
                    )
                    ->statePath('data'),
            ),
        ];
    }
}
