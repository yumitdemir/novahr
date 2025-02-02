<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobOpeningnsResource\Pages\Applications;
use App\Filament\Resources\JobOpeningResource\Pages;
use App\Filament\Resources\JobOpeningResource\RelationManagers;
use App\Models\JobOpening;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JobOpeningResource extends Resource
{
    protected static ?string $model = JobOpening::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(100),

                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Open',
                        'inactive' => 'Closed',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()->columnSpan(2)->rows(10),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Applications')
                    ->url(fn (JobOpening $record): string => JobOpeningResource::getUrl('applications', ['record' => $record->id]))
                    ->icon(FilamentIcon::resolve('heroicon-o-eye') ?? 'heroicon-o-eye'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobOpenings::route('/'),
            'create' => Pages\CreateJobOpening::route('/create'),
            'edit' => Pages\EditJobOpening::route('/{record}/edit'),
            'applications' => Applications::route('/{record}/applications'),
            'view' => Pages\ViewJobOpening::route('/{record}'),
        ];
    }
}
