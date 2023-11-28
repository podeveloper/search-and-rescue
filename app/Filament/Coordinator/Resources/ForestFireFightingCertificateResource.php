<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Coordinator\Resources\ForestFireFightingCertificateResource\Pages;
use App\Filament\Coordinator\Resources\ForestFireFightingCertificateResource\RelationManagers;
use App\Models\City;
use App\Models\District;
use App\Models\ForestFireFightingCertificate;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class ForestFireFightingCertificateResource extends Resource
{
    protected static ?string $model = ForestFireFightingCertificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('pdf')
                    ->columnSpanFull()
                    ->acceptedFileTypes(['application/pdf'])
                    ->saveUploadedFileUsing(function ($file, $get) {
                        $user = User::find($get('user_id'));
                        return $file->storeAs("forest-fire-fighting-certificates/{$get('user_id')}", ($user->national_id_number ? $user->national_id_number.'_' : '').'forest_fire_fighting_certificate'.'.pdf');
                    }),
                Forms\Components\TextInput::make('registration_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('work_area_city_id')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->exists('cities','id'),
                Forms\Components\TextInput::make('directorate')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('duty')
                    ->required()
                    ->options(ForestFireFightingCertificate::duties()),
                Forms\Components\DatePicker::make('date_of_issue')
                    ->required(),
                Forms\Components\DatePicker::make('expiration_date')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('registration_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('directorate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('duty')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_issue')
                    ->date('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->date('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('pdf')
                    ->icon('heroicon-o-document-text')
                    ->url(function (ForestFireFightingCertificate $record){
                        return $record->pdf ? route('files.show', ['path' => $record->pdf]) : '';
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (ForestFireFightingCertificate $record): string => $record->pdf != null),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageForestFireFightingCertificates::route('/'),
        ];
    }
}
