<?php

namespace App\Filament\Resources\JobOpeningnsResource\Pages;

use App\Filament\Resources\JobApplicationResource\Pages\ViewJopApplication;
use App\Filament\Resources\JobOpeningResource;
use App\Models\JobApplication;
use App\Models\JobOpening;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Applications extends Page implements HasForms, HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = JobOpeningResource::class;

    protected static string $view = 'filament.resources.job-openings-resource.pages.applications';

    public function table(Table $table): Table
    {
        return $table
            ->query(JobApplication::query()->where('job_opening_id', $this->record->id))
            ->columns([
                TextColumn::make('status')->sortable(),
                TextColumn::make('application_date')->date()->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('surname')->searchable(),
                TextColumn::make('phone')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('compatibility_rating')->searchable()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('view')
                    ->label('View Application')
                    ->url(fn (JobApplication $record): string => ViewJopApplication::getUrl(['record' => $record->id]))
                    ->icon(FilamentIcon::resolve('heroicon-o-eye') ?? 'heroicon-o-eye'),
            ])
            ->bulkActions([
                //
            ]);
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
}
