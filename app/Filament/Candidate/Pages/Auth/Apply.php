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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Collection;

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
                ->label(__('general.profile_photo')),
            FileUpload::make('resume')
                ->columnSpanFull()
                ->openable()
                ->previewable()
                ->maxSize(10000)
                ->label(__('general.resume')),
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
                ->nullable()
                ->exists('genders','id')
                ->label(__('gender.singular')),
            DatePicker::make('date_of_birth')
                ->default(now())
                    ->maxDate(now())
                ->nullable()
                ->label(__('general.date_of_birth')),
            Select::make('nationality_id')
                ->searchable()
                ->preload()
                ->options(Nationality::all(['id','name'])->pluck('name','id'))
                ->nullable()
                ->exists('nationalities','id')
                ->label(__('nationality.singular')),
            Select::make('country_id')
                ->options(Country::all(['id','name'])->pluck('name','id'))
                ->searchable()
                ->preload()
                ->live()
                ->required()
                ->exists('countries','id')
                ->label(__('country.singular')),
            Select::make('city_id')
                ->options(fn(Get $get): Collection => City::query()
                    ->where('country_id',$get('country_id'))
                    ->pluck('name','id'))
                ->searchable()
                ->preload()
                ->nullable()
                ->exists('cities','id')
                ->label(__('city.singular')),
            Select::make('district_id')
                ->options(fn(Get $get): Collection => District::query()
                    ->where('city_id',$get('city_id'))
                    ->pluck('name','id'))
                ->searchable()
                ->preload()
                ->nullable()
                ->exists('districts','id')
                ->label(__('district.singular')),
            TextInput::make('email')
                ->required()
                ->email()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->label(__('general.email')),
            TextInput::make('phone')
                ->nullable()
                ->tel()
                ->maxLength(255)
                ->label(__('general.phone')),
            Select::make('occupation_id')
                ->searchable()
                ->preload()
                ->options(Occupation::all(['id','name'])->pluck('name','id'))
                ->nullable()
                ->exists('occupations','id')
                ->label(__('occupation.singular')),
            Select::make('education_level_id')
                ->searchable()
                ->preload()
                ->options(EducationLevel::all(['id','name'])->pluck('name','id'))
                ->nullable()
                ->exists('education_levels','id')
                ->label(__('education_level.singular')),
            Select::make('speaking_languages')
                ->multiple()
                ->searchable()
                ->preload()
                ->options(Language::all(['id','name'])->pluck('name','id'))
                ->label(__('language.plural')),
            Select::make('referral_source_id')
                ->options(ReferralSource::all(['id','name'])->pluck('name','id'))
                ->nullable()
                ->exists('referral_sources','id')
                ->label(__('referral_source.question')),
            TextInput::make('reference_name')
                ->nullable()
                ->string()
                ->label(__('user.reference')),
            TextInput::make('instagram')
                ->nullable()
                ->string()
                ->placeholder('@username'),
            TextInput::make('facebook')
                ->nullable()
                ->string()
                ->placeholder('@username'),
            TextInput::make('twitter')
                ->nullable()
                ->string()
                ->placeholder('@username'),
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
