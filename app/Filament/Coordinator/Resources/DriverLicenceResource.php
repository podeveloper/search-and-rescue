<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Coordinator\Resources\DriverLicenceResource\Pages;
use App\Filament\Coordinator\Resources\DriverLicenceResource\RelationManagers;
use App\Models\DriverLicence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class DriverLicenceResource extends Resource
{
    protected static ?string $model = DriverLicence::class;

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
                        return $file->storeAs("public/driver-licences/{$get('user_id')}", $file->getClientOriginalName());
                    }),
                Forms\Components\Select::make('class')
                    ->options(DriverLicence::classifications())
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\DatePicker::make('date_of_issue')
                    ->required(),
                Forms\Components\DatePicker::make('expiration_date')
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
                Tables\Columns\TextColumn::make('class')
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
                    ->url(fn (DriverLicence $record): string => Storage::url($record->pdf) ?? '')
                    ->openUrlInNewTab()
                    ->visible(fn (DriverLicence $record): string => $record->pdf != null),
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
            'index' => Pages\ManageDriverLicences::route('/'),
        ];
    }
}
