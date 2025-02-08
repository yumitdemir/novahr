<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveRequestResource\Pages;
use App\Filament\Resources\LeaveRequestResource\RelationManagers;
use App\Models\LeaveRequest;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaveRequestResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = LeaveRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

public static function getEloquentQuery(): Builder
{
    if (auth()->user()->can('view_any_all_leave::request')) {
        return parent::getEloquentQuery();
    }
    return parent::getEloquentQuery()->where('employee_id', auth()->user()->employee_id);
}

public static function form(Form $form): Form
{
    $schema = [
        Forms\Components\DatePicker::make('start_date')
            ->required(),
        Forms\Components\DatePicker::make('end_date')
            ->required(),
        Forms\Components\TextInput::make('description')
            ->required()
            ->maxLength(255),
        Forms\Components\Select::make('leave_type')
            ->options([
                'sick' => 'Sick Leave',
                'vacation' => 'Vacation',
                'personal' => 'Personal Leave',
            ])
            ->required(),
    ];

    if (auth()->user()->can('create_all_leave::request')) {
        $schema[] = Forms\Components\Select::make('status')
            ->options([
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ])
            ->required();
        $schema[] = Select::make('employee_id')
            ->relationship('employee', 'name')
            ->required();
    }

    return $form->schema($schema);
}

public static function table(Table $table): Table
{
    $filters = [
        Tables\Filters\SelectFilter::make('status')
            ->options([
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ])
            ->label('Status'),
        Tables\Filters\SelectFilter::make('leave_type')
            ->options([
                'sick' => 'Sick Leave',
                'vacation' => 'Vacation',
                'personal' => 'Personal Leave',
            ])
            ->label('Leave Type'),
    ];

    if (auth()->user()->can('view_all_leave::request')) {
        $filters[] = Tables\Filters\SelectFilter::make('employee_id')
            ->relationship('employee', 'name')
            ->searchable()
            ->label('Employee');
    }

    return $table
        ->columns([
            Tables\Columns\TextColumn::make('start_date')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('end_date')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('status')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('description')
                ->searchable(),
            Tables\Columns\TextColumn::make('leave_type')
                ->searchable(),
            Tables\Columns\TextColumn::make('employee.name')
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
        ->filters($filters)
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}


    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',

            'view_any_all',
            'view_all',
            'update_all',
            'delete_all',
            'create_all',
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
