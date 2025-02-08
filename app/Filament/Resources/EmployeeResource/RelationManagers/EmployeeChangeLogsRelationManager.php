<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeChangeLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'EmployeeChangeLogs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

public function table(Table $table): Table
{
    return $table
        ->recordTitleAttribute('employee.name')
        ->columns([
            Tables\Columns\TextColumn::make('employee.name')
                ->label('Employee Name')
                ->searchable(),
            Tables\Columns\TextColumn::make('change_type')
                ->label('Change Type')
                ->searchable(),
            Tables\Columns\TextColumn::make('old_value')
                ->label('Old Value')
                ->searchable(),
            Tables\Columns\TextColumn::make('new_value')
                ->label('New Value')
                ->searchable(),
            Tables\Columns\TextColumn::make('changed_at')
                ->label('Changed At')
                ->dateTime()
                ->sortable(),
        ])
        ->filters([
            Tables\Filters\Filter::make('changed_at')
                ->form([
                    Forms\Components\DatePicker::make('changed_from')
                        ->label('Changed From'),
                    Forms\Components\DatePicker::make('changed_until')
                        ->label('Changed Until'),
                ])
                ->query(function (Builder $query, array $data) {
                    return $query
                        ->when($data['changed_from'], fn ($query, $date) => $query->whereDate('changed_at', '>=', $date))
                        ->when($data['changed_until'], fn ($query, $date) => $query->whereDate('changed_at', '<=', $date));
                }),
            Tables\Filters\Filter::make('change_type')
                ->form([
                    Forms\Components\TextInput::make('change_type')
                        ->label('Change Type')
                        ->placeholder('Search Change Type'),
                ])
                ->query(function (Builder $query, array $data) {
                    return $query->when($data['change_type'], fn ($query, $changeType) => $query->where('change_type', 'like', "%{$changeType}%"));
                }),
        ])
        ->headerActions([
            // Add your header actions here
        ])
        ->actions([
            // Add your row actions here
        ])
        ->bulkActions([
            // Add your bulk actions here
        ]);
}
}
