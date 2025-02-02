<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobApplicationResource\Pages;
use App\Filament\Resources\JobApplicationResource\RelationManagers;
use App\Models\JobApplication;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('application_date'),
                Forms\Components\TextInput::make('name')
                    ->maxLength(100),
                Forms\Components\TextInput::make('surname')
                    ->maxLength(100),
                Forms\Components\FileUpload::make('cv')
                    ->downloadable()
                    ->directory('cv')
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']),
                Forms\Components\Select::make('job_opening_id')
                    ->relationship('jobOpening', 'title')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('application_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('surname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jobOpening.title')
                    ->numeric()
                    ->sortable(),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Personal Information')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('surname'),
                        TextEntry::make('email'),
                        TextEntry::make('phone'),
                        TextEntry::make('linkedin'),
                        TextEntry::make('location'),
                    ])
                    ->columns(2),
                Section::make('Professional Information')
                    ->schema([
                        TextEntry::make('current_job_title'),
                        TextEntry::make('current_employer'),
                        TextEntry::make('years_of_experience'),
                        TextEntry::make('university'),
                        TextEntry::make('certifications'),
                        TextEntry::make('technical_skills'),
                        TextEntry::make('soft_skills'),
                        TextEntry::make('languages_spoken'),
                    ])
                    ->columns(2),
                Section::make('CV')
                    ->schema([
                        TextEntry::make('cv')
                            ->label(false)
                            ->url(fn(JobApplication $record) => Storage::url($record->cv))
                            ->openUrlInNewTab(),
                    ])
                    ->columns(2),
                Section::make('Job Opening Information')
                    ->schema([
                        TextEntry::make('jobOpening.title')
                            ->label('Title'),
                        TextEntry::make('jobOpening.description')
                        ->label('Description'),
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
            'index' => Pages\ListJobApplications::route('/'),
            'create' => Pages\CreateJobApplication::route('/create'),
            'edit' => Pages\EditJobApplication::route('/{record}/edit'),
            'view' => Pages\ViewJopApplication::route('/{record}'),
        ];
    }
}
