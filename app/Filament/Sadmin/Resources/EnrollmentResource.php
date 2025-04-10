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
                                            ->required(),
                                        Forms\Components\Select::make('user_id')
                                            ->label('Студент')
                                            ->relationship('user', 'name')
                                            ->preload()
                                            ->required(),
                                        Forms\Components\DateTimePicker::make('enrollment_date')
                                            ->label('Дата начала обучения')
                                            ->date()
                                            ->required()
                                            ->default(now()),
                                        Forms\Components\DatePicker::make('completion_deadline')
                                            ->label('Дата окончания обучения')
                                            ->date()
                                            ->default(now()->addMonth()),
                                    ])
                                    ->footerActions([
                                        Action::make('set_steps')
                                            ->label('Создать план обучения')
                                            ->icon('heroicon-o-clipboard-document-list')
                                            ->color('success')
                                            ->requiresConfirmation()
                                            ->action(function (?Enrollment $record) {
                                                if ($record) {
                                                    static::setSteps($record);
                                                }
                                            })
                                            ->hidden(function (?Enrollment $record) {
                                                return !$record || $record->hasSteps();
                                            }),
                                    ])
                                ->columns(2),
                            ]),
                        Tabs\Tab::make('План обучения')
                            ->schema([
                                Forms\Components\Repeater::make('steps')
                                    ->hiddenLabel()
                                    ->relationship('steps')
                                    ->schema([
                                        //
                                    ])
                                        ->itemLabel(function (array $state): ?string {
                                        if (empty($state['stepable_type'])) {
                                            return '';
                                        }
                                        return match ($state['stepable_type']) {
                                            Lesson::class => 'Урок - ' . Lesson::findOrFail($state['stepable_id'])->name,
                                            Quiz::class => 'Тест - ' . Quiz::findOrFail($state['stepable_id'])->name,
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
                Filter::make('is_not_steps_created')
                    ->label('Нет плана обучения')
                    ->query(fn (Builder $query): Builder => $query->where('is_steps_created', false))
            ])
            ->recordUrl(function ($record) {
/*                if ($record->trashed()) {
                    return null;
                }*/
                return Pages\ViewEnrollment::getUrl([$record->id]);
            })
            ->actions([
                Tables\Actions\EditAction::make()
                ->hiddenLabel(),
            ])
/*            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])*/
            ->persistFiltersInSession();
    }

    public static function infoList(Infolist $infolist): Infolist
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
                                    ->inlineLabel(),
                                TextEntry::make('completion_deadline')
                                    ->label('Дата окончания обучения')
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
//                                            ->description('')
                                            ->schema([
                                                TextEntry::make('is_completed')
                                                    ->label('Статус:')
                                                    ->inlineLabel()
//                                                    ->hiddenLabel()
                                                    ->formatStateUsing(function ($state) {
                                                        return $state === true ? 'Завершен' : 'Не завершен';
                                                    }),
                                                TextEntry::make('started_at')
                                                    ->label('Начат:')
                                                    ->inlineLabel(),
                                                TextEntry::make('completed_at')
                                                    ->label('Завершен:')
                                                    ->inlineLabel(),

                                            ]),
/*                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('stepable_type')
                                                    ->hiddenLabel()
                                                    ->formatStateUsing(function ($state, $record) {
//                                                        dump($state, $record);
                                                        return $record->stepableModel()->name;
                                                    }),
                                            ])
                                            ->columns(2),

                                        TextEntry::make('stepable_type')
                                            ->label('Тип шага'),
                                        TextEntry::make('stepable_id')
                                            ->label('ID шага'),
                                        TextEntry::make('stepable_name')
                                            ->label('Название шага'),*/
                                    ])
                                    ->contained(false),

                            ]),
                    ])
                    ->persistTab()
                    ->columnSpan('full')
                    ->activeTab(1),

/*                \Filament\Infolists\Components\Section::make('Основная информация')
                    ->schema([
                    ]),
            \Filament\Infolists\Components\Section::make('План обучения')
                ->schema([
                ])
            ->columns(),*/
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
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
            'view' => Pages\ViewEnrollment::route('/{record}'),
        ];
    }

    public static function setSteps($record): void
    {
        if ($record->is_steps_created) {
            return;
        }

        $course = $record->course;
        if (!$course) {
            throw new \Exception('Enrollment must be related to a course.');
        }

        $userId = $record->user_id;
        if (!$userId) {
            throw new \Exception('Enrollment must be associated with a user.');
        }

        $stepsData = $course->getSteps();
        $steps = [];
        foreach ($stepsData as $index => $step) {
            $steps[] = [
                'enrollment_id' => $record->id,
                'course_id' => $course->id,
                'user_id' => $userId,
                'stepable_id' => $step['stepable_id'],
                'stepable_type' => $step['stepable_type'],
                'position' => $index + 1,
                'is_completed' => false,
            ];
        }

        $insertedCount = DB::table('enrollment_steps')->insertOrIgnore($steps);

        if ($insertedCount === count($steps)) {
            $record->update(['is_steps_created' => true]);

            Notification::make()
                ->success()
                ->title('Шаги успешно созданы')
                ->send();
        }
        else {
            Notification::make()
                ->warning()
                ->title('Не все шаги были созданы')
                ->body("Создано {$insertedCount} из " . count($steps) . " шагов.")
                ->send();
        }
    }
}
