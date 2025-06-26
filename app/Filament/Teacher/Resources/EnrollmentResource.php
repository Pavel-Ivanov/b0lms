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
                    ->sortable(),
/*                Tables\Columns\TextColumn::make('enrollment_date')
                    ->dateTime()
                    ->sortable(),*/
                Tables\Columns\TextColumn::make('completion_deadline')
                    ->label('Дата окончания')
                    ->date('d.m.Y')
                    ->sortable(),
                ProgressBar::make('bar')
                    ->label('Выполнено')
                    ->getStateUsing(function (Enrollment $record) {
                        $progress = $record->progress();
                        $total = $progress['max'];
                        $progress = $progress['value'];
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
                Filter::make('is_not_steps_created')
                    ->label('Нет плана обучения')
                    ->query(fn (Builder $query): Builder => $query->where('is_steps_created', false))
            ])
            ->recordUrl(function ($record) {
                return Pages\ViewEnrollment::getUrl([$record->id]);
            })
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hiddenLabel(),
            ]);
    }

/*    public static function infoList(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Tabs::make('Tabs')
                    ->tabs([
                        \Filament\Infolists\Components\Tabs\Tab::make('Основная информация')
                            ->schema([
                                TextEntry::make('course.name')
                                    ->label('Курс')
                                    ->inlineLabel(),
                                TextEntry::make('user.name')
                                    ->label('Студент')
                                    ->inlineLabel(),
                                TextEntry::make('enrollment_date')
                                    ->label('Дата назначения')
                                    ->date('d.m.Y')
                                    ->inlineLabel(),
                                TextEntry::make('completion_deadline')
                                    ->label('Дата окончания обучения')
                                    ->date('d.m.Y')
                                    ->inlineLabel(),
                            ]),
                        \Filament\Infolists\Components\Tabs\Tab::make('План обучения')
                            ->schema([
                                RepeatableEntry::make('steps')
                                    ->hiddenLabel()
                                    ->schema([
                                        \Filament\Infolists\Components\Section::make('step')
                                            ->heading(function (EnrollmentStep $record) {
                                                return $record->stepableModel()->name;
                                            })
                                            ->schema([
                                                TextEntry::make('is_completed')
                                                    ->label('Статус:')
                                                    ->inlineLabel()
                                                    ->formatStateUsing(function ($state) {
                                                        return $state === true ? 'Завершен' : 'Не завершен';
                                                    }),
                                                TextEntry::make('started_at')
                                                    ->label('Начат:')
                                                    ->date('d.m.Y H:i')
                                                    ->inlineLabel(),
                                                TextEntry::make('completed_at')
                                                    ->label('Завершен:')
                                                    ->date('d.m.Y H:i')
                                                    ->inlineLabel(),
                                                TextEntry::make('stepable_type')
                                                    ->label('Тип:')
                                                    ->inlineLabel()
                                                    ->formatStateUsing(function ($state) {
                                                        return match ($state) {
                                                            Lesson::class => 'Урок',
                                                            Quiz::class => 'Тест',
                                                            default => 'Неизвестно',
                                                        };
                                                    }),
                                                // Отображение информации о тестах, если шаг является тестом
                                                RepeatableEntry::make('tests')
                                                    ->label('Попытки прохождения теста:')
                                                    ->visible(fn (EnrollmentStep $record) => $record->stepable_type === Quiz::class)
                                                    ->schema([
                                                        TextEntry::make('attempt_number')
                                                            ->label('Номер попытки:')
                                                            ->inlineLabel(),
                                                        TextEntry::make('result')
                                                            ->label('Результат:')
                                                            ->inlineLabel(),
                                                        TextEntry::make('passed')
                                                            ->label('Пройден:')
                                                            ->inlineLabel()
                                                            ->formatStateUsing(function ($state) {
                                                                return $state === true ? 'Да' : 'Нет';
                                                            }),
                                                        TextEntry::make('started_at')
                                                            ->label('Начат:')
                                                            ->date('d.m.Y H:i')
                                                            ->inlineLabel(),
                                                        TextEntry::make('completed_at')
                                                            ->label('Завершен:')
                                                            ->date('d.m.Y H:i')
                                                            ->inlineLabel(),
                                                        TextEntry::make('time_spent')
                                                            ->label('Затраченное время (сек):')
                                                            ->inlineLabel(),
                                                    ])
                                                    ->contained(false),
                                            ]),
                                    ])
                                    ->contained(false),
                            ]),
                    ])
                    ->persistTab()
                    ->columnSpan('full')
                    ->activeTab(0),
            ]);
    }*/

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
