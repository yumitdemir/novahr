<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Filament\Resources\EmployeeResource\Widgets\SalaryTrend;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $schema = [
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('surname')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('employee_position')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(100),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(100),
                Forms\Components\DatePicker::make('hire_date')
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('salary')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->required(),
                Forms\Components\Select::make('department_id')
                    ->relationship(name: 'department', titleAttribute: 'name')
                    ->searchable('name')
                    ->preload()
                    ->label('Department'),


                Fieldset::make('address')
                    ->relationship('address')
                    ->schema([
                        Forms\Components\TextInput::make('fullAddress')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('city')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('street')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('zip')
                            ->required()
                            ->maxLength(100),
                    ])
            ];
if (auth()->user()->can('create_employee')) {
    $schema[] = Forms\Components\Select::make('user_id')
        ->relationship('user', 'name')
        ->required()
        ->createOptionForm([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(100),
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(100),
            Forms\Components\TextInput::make('password')
                ->password()
                ->required()
                ->maxLength(100),
            Select::make('roles')
                ->label('Roles')
                ->multiple()
                ->relationship('roles', 'name')
                ->preload()
                ->searchable()
        ]);
}

        return $form->schema($schema);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('surname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hire_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->searchable()
                    ->label('Department'),

                Tables\Columns\TextColumn::make('salary')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address.fullAddress')
                    ->searchable()
                    ->label('Street'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->label('Status'),
                Tables\Filters\SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Department'),
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

    public static function getWidgets(): array
    {
        return [
            SalaryTrend::class
        ];
    }
    public static function getRelations(): array
    {
        return [
            RelationManagers\EmployeeChangeLogsRelationManager::class
        ];
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Grid::make()
                ->schema([
                    TextEntry::make('name')
                        ->label('Name'),
                    TextEntry::make('surname')
                        ->label('Surname'),
                    TextEntry::make('email')
                        ->label('Email'),
                    TextEntry::make('phone')
                        ->label('Phone'),
                    TextEntry::make('hire_date')
                        ->label('Hire Date')
                        ->date(),
                    TextEntry::make('salary')
                        ->label('Salary')
                        ->numeric(),
                    TextEntry::make('department.name')
                        ->label('Department'),
                    TextEntry::make('status')
                        ->label('Status'),
                ])
                ->columns([
                    'default' => 4,
                    'sm' => 2,
                    'md' => 4,
                ]),

        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
            'view' => Pages\ViewEmployee::route('/{record}'),
        ];
    }
}
