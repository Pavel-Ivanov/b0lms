<?php

namespace App\Filament\Sadmin\Resources;

use App\Filament\Sadmin\Resources\EnrollmentResource\Pages;
use App\Filament\Sadmin\Resources\EnrollmentResource\RelationManagers;
use App\Models\Enrollment;
use App\Models\EnrollmentStep;
use App\Models\Lesson;
use App\Models\Quiz;
use DB;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use function Pest\Laravel\delete;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?int $navigationSort = 5;
    protected static ?string $modelLabel = 'Назначение курса';
    protected static ?string $pluralModelLabel = 'Назначения курсов';
    protected static ?string $navigationGroup = 'Академия';
    protected static ?string $navigationLabel = 'Назначения';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Основная информация')
                            ->schema([
                                Section::make('Основная информация')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\Select::make('course_id')
                                            ->label('Курс')
                                            ->relationship('course', 'name')
                                            ->preload()
                                            ->required()
                                            ->searchable(['name'])
                                            ->disabled(fn (?Enrollment $record) => $record !== null)
                                            ->helperText(fn (?Enrollment $record) => $record !== null ? 'Это поле недоступно для редактирования' : null),
                                        Forms\Components\Select::make('user_id')
                                            ->label('Студент')
                                            ->relationship('user', 'name', fn ($query) => $query->whereHas('roles', function ($q) {
                                                $q->where('name', 'Студент');
                                            }))
                                            ->preload()
                                            ->required()
                                            ->searchable(['name'])
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
                            ]),
                        Tabs\Tab::make('План обучения')
                            ->schema([
                                Forms\Components\Repeater::make('steps')
                                    ->hiddenLabel()
                                    ->relationship('steps')
                                    ->schema([
/*                                        Forms\Components\Placeholder::make('step_title')
                                            ->hiddenLabel()
//                                            ->label('Шаг')
                                            ->content(function (?array $state): string {
                                                if (empty($state) || empty($state['stepable_type']) || empty($state['stepable_id'])) {
                                                    return '';
                                                }
                                                return match ($state['stepable_type']) {
                                                    \App\Models\Lesson::class => optional(\App\Models\Lesson::find($state['stepable_id']))?->name,
                                                    \App\Models\Quiz::class => 'Тест — ' . optional(\App\Models\Quiz::find($state['stepable_id']))?->name,
                                                    default => ''
                                                } ?? '';
                                            })
                                            ->columnSpanFull(),*/
                                        Forms\Components\TextInput::make('max_attempts')
                                            ->label('Макс. попыток')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(100)
                                            ->visible(fn (Forms\Get $get) => $get('stepable_type') === \App\Models\Quiz::class)
                                            ->helperText('Количество попыток для данного теста.'),
                                        Forms\Components\TextInput::make('passing_percentage')
                                            ->label('Проходной балл, %')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->visible(fn (Forms\Get $get) => $get('stepable_type') === \App\Models\Quiz::class)
                                            ->helperText('Минимальный процент правильных ответов для зачёта.'),
                                    ])
                                        ->itemLabel(function (?array $state): ?string {
                                        if (empty($state) || empty($state['stepable_type'])) {
                                            return '';
                                        }
                                        return match ($state['stepable_type']) {
                                            Lesson::class => optional(Lesson::find($state['stepable_id']))?->name,
                                            Quiz::class => optional(Quiz::find($state['stepable_id']))?->name,
                                            default => '',
                                        };

                                    })
                                    ->deletable(false)
                                    ->columns()
                                    ->addable(false)
                            ])
                        ->visible(function (?Enrollment $record): bool {
                            return $record !== null && $record->hasSteps();
                        }),
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
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Курс')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Студент')
                    ->sortable(),
                Tables\Columns\TextColumn::make('enrollment_date')
                    ->label('Дата назначения')
                    ->dateTime('d.m.Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('completion_deadline')
                    ->label('Дата окончания')
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_steps_created')
                    ->label('План обучения'),
                Tables\Columns\IconColumn::make('completed_status')
                    ->label('Завершено')
                    ->boolean()
                    ->getStateUsing(fn(Enrollment $record) => $record->isCompleted()),
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
                Filter::make('incomplete')
                    ->label('Не завершенные')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereHas('steps', fn ($q) => $q->where('is_completed', false))
                    ),
                Filter::make('overdue')
                    ->label('Просроченные')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereDate('completion_deadline', '>', now())
                        ->whereHas('steps', fn ($q) => $q->where('is_completed', false))
                    ),
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
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
            'view' => Pages\ViewEnrollment::route('/{record}'),
        ];
    }

}
