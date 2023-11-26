<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Coordinator\Resources\RadioCertificateResource\Pages;
use App\Filament\Coordinator\Resources\RadioCertificateResource\RelationManagers;
use App\Models\RadioCertificate;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RadioCertificateResource extends Resource
{
    protected static ?string $model = RadioCertificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('pdf')
                    ->columnSpanFull()
                    ->acceptedFileTypes(['application/pdf'])
                    ->saveUploadedFileUsing(function ($file, $get) {
                        $user = User::find($get('user_id'));
                        return $file->storeAs("radio-certificates/{$get('user_id')}", ($user->national_id_number ? $user->national_id_number.'_' : '').'radio_certificate'.'.pdf');
                    }),
                Forms\Components\TextInput::make('call_sign')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('radio_net_sign')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('licence_number')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\Select::make('licence_class')
                    ->required()
                    ->options(RadioCertificate::classifications()),
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
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('call_sign')
                    ->searchable(),
                Tables\Columns\TextColumn::make('radio_net_sign')
                    ->searchable(),
                Tables\Columns\TextColumn::make('licence_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('licence_class')
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
                    ->url(function (RadioCertificate $record){
                        return $record->pdf ? route('files.show', ['path' => $record->pdf]) : '';
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (RadioCertificate $record): string => $record->pdf != null),                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ManageRadioCertificates::route('/'),
        ];
    }
}
