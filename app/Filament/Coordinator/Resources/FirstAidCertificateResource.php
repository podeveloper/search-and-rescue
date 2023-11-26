<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Coordinator\Resources\FirstAidCertificateResource\Pages;
use App\Filament\Coordinator\Resources\FirstAidCertificateResource\RelationManagers;
use App\Models\FirstAidCertificate;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FirstAidCertificateResource extends Resource
{
    protected static ?string $model = FirstAidCertificate::class;

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
                        return $file->storeAs("first-aid-certificates/{$get('user_id')}", ($user->national_id_number ? $user->national_id_number.'_' : '').'first_aid_certificate'.'.pdf');
                    }),
                Forms\Components\TextInput::make('licence_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('training_institution')
                    ->required()
                    ->maxLength(255),
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
                Tables\Columns\TextColumn::make('licence_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('training_institution')
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
                    ->url(function (FirstAidCertificate $record){
                      return $record->pdf ? route('files.show', ['path' => $record->pdf]) : '';
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (FirstAidCertificate $record): string => $record->pdf != null),
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
            'index' => Pages\ManageFirstAidCertificates::route('/'),
        ];
    }
}
