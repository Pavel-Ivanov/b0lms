<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\EnrollmentResource\Pages;
use App\Filament\Teacher\Resources\EnrollmentResource\RelationManagers;
use App\Models\Enrollment;
use App\Models\EnrollmentStep;
use App\Models\Lesson;
use App\Models\Quiz;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use IbrahimBougaoua\FilaProgress\Tables\Columns\ProgressBar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
//    protected static ?int $navigationSort = 5;
    protected static ?string $modelLabel = 'Назначение курса';
    protected static ?string $pluralModelLabel = 'Назначения курсов';
//    protected static ?string $navigationGroup = 'Академия';
    protected static ?string $navigationLabel = 'Назначения';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основная информация')
                    ->label('')
                    ->schema([
                        Forms\Components\Select::make('course_id')
                            ->label('Курс')
                            ->relationship('course', 'name')
                            ->preload()
                            ->required()
                            ->disabled(fn (?Enrollment $record) => $record !== null)
                            ->helperText(fn (?Enrollment $record) => $record !== null ? 'Это поле недоступно для редактирования' : null),
                        Forms\Components\Select::make('user_id')
                            ->label('Студент')
                            ->relationship('user', 'name', fn ($query) => $query->whereHas('roles', function ($q) {
                                $q->where('name', 'Студент');
                            }))
                            ->preload()
                            ->required()
                            ->disabled(fn (?Enrollment $record) => $record !== null)
                            ->helperText(fn (?Enrollment $record) => $record !== null ? 'Это поле недоступно для редактирования' : null),
                        Forms\Components\DateTimePicker::make('enrollment_date')
                            ->label('Дата начала обучения')
                            ->date()
                            ->required()
                            ->default(now())
                            ->disabled(fn (?Enrollment $record) => $record !== null)
                            ->helperText(fn (?Enrollment $record) => $record !== null ? 'Это поле недоступно для редактирования' : null),
                        Forms\Components\DatePicker::make('completion_deadline')
                            ->label('Дата окончания обучения')
                            ->date()
                            ->default(now()->addMonth())
                            ->rules([
                                fn (Forms\Get $get): string => 'after_or_equal:' . $get('enrollment_date'),
                            ])
                            ->validationAttribute('Дата окончания обучения')
                            ->validationMessages([
                                'after_or_equal' => 'Дата окончания обучения не может быть меньше даты начала обучения',
                            ]),
                    ])
                    ->columns(2),
            ])
;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Курс')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Студент')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('enrollment_date')
                    ->label('Дата назначения')
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('completion_deadline')
                    ->label('Дата окончания')
                    ->date('d.m.Y')
                    ->sortable(),
                ProgressBar::make('bar')
                    ->label('Выполнено')
                    ->getStateUsing(function (Enrollment $record) {
                        $progressData = $record->progressData();
                        $total = $progressData['max'];
                        $progress = $progressData['value'];
                        return [
                            'total' => $total,
                            'progress' => $progress,
                        ];
                    }),

            ])
            ->filters([
                SelectFilter::make('course')
                    ->label('Курс')
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('user')
                    ->label('Студент')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('user.companyDepartment')
                    ->label('Подразделение')
                    ->relationship('user.companyDepartment', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Состояние')
                    ->options([
                        'not_started' => 'Не начатые',
                        'incomplete' => 'Не завершенные',
                        'overdue' => 'Просроченные',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;
                        if (!$value) {
                            return $query;
                        }
                        return match ($value) {
                            'not_started' => $query
                                ->where('is_steps_created', true)
                                ->whereHas('steps')
                                ->whereDoesntHave('completedSteps'),
                            'incomplete' => $query
                                ->whereDate('completion_deadline', '>=', now())
                                ->whereHas('steps', fn ($q) => $q->where('is_completed', false)),
                            'overdue' => $query
                                ->whereDate('completion_deadline', '<', now())
                                ->whereHas('steps', fn ($q) => $q->where('is_completed', false)),
                            default => $query,
                        };
                    }),
                Filter::make('is_not_steps_created')
                    ->label('Нет плана обучения')
                    ->query(fn (Builder $query): Builder => $query->where('is_steps_created', false)),
            ])
            ->recordUrl(function ($record) {
                return Pages\ViewEnrollment::getUrl([$record->id]);
            })
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hiddenLabel(),
            ])
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
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'view' => Pages\ViewEnrollment::route('/{record}'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
