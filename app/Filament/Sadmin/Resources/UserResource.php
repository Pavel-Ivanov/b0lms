<?php

namespace App\Filament\Sadmin\Resources;

use App\Filament\Sadmin\Resources\UserResource\Pages;
use App\Filament\Sadmin\Resources\UserResource\RelationManagers;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Пользователь';
    protected static ?string $pluralModelLabel = 'Пользователи';
    protected static ?string $navigationGroup = 'Администрирование';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Информация')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('ФИО')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\CheckboxList::make('roles')
                                    ->label('Роли')
                                    ->relationship('roles', 'name')
                                    ->columns(5)
                                    ->columnSpan('full'),
                                Forms\Components\Select::make('company_department_id')
                                    ->label('Подразделение')
                                    ->relationship('companyDepartment', 'name')
                                    ->required(),
                                Forms\Components\Select::make('company_position_id')
                                    ->label('Должность')
                                    ->relationship('companyPosition', 'name')
                                    ->required(),
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) =>
                                    filled($state) ? Hash::make($state) : null)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->maxLength(255)
                                    ->label('Пароль'),
                            ]),
                        Tabs\Tab::make('Курсы')
                            ->schema([
                                Repeater::make('enrollments')
                                    ->label('Курсы')
                                    ->relationship('enrollments')
                                    ->schema([
                                        Forms\Components\Select::make('course_id')
                                            ->label('Курс')
                                            ->relationship('course', 'name')
                                            ->required(),
                                        Forms\Components\Select::make('user_id')
                                            ->label('Студент')
                                            ->relationship('user', 'name')
                                            ->required(),
                                        Forms\Components\DateTimePicker::make('enrollment_date')
                                            ->label('Дата начала обучения')
                                            ->required(),
                                        Forms\Components\DatePicker::make('completion_deadline')
                                            ->label('Дата окончания обучения')
                                            ->date(),
                                    ])
                                    ->itemLabel(function (array $state): ?string {
                                        if (empty($state['course_id'])) {
                                            return '';
                                        }
                                        return Course::where('id', $state['id'])->first()->name ?? '';
                                    })
                                    ->columns()
                                    ->collapsible()
                                    ->collapsed()
                                    ->defaultItems(0),
                            ]),
                    ])
                    ->persistTab()
                    ->columnSpan('full')
                    ->activeTab(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Роли'),
                Tables\Columns\TextColumn::make('companyDepartment.name')
                    ->label('Подразделение')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('companyPosition.name')
                    ->label('Должность')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('roles')
                    ->label('Роли')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                SelectFilter::make('companyDepartment')
                    ->label('Подразделение')
                    ->relationship('companyDepartment', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('companyPosition')
                    ->label('Должность')
                    ->relationship('companyPosition', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('is_published')
                    ->label('Опубликован')
                    ->toggle()
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->hiddenLabel(),
            ])
            ->bulkActions([
            ])
            ->defaultSort('name')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession();
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
