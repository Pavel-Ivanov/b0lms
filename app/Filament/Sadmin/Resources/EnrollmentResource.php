<?php

namespace App\Filament\Sadmin\Resources;

use App\Filament\Sadmin\Resources\EnrollmentResource\Pages;
use App\Filament\Sadmin\Resources\EnrollmentResource\RelationManagers;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use DB;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                        Tabs\Tab::make('Основные данные')
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
                            ]),
                        Tabs\Tab::make('План обучения')
                            ->schema([
/*                                Forms\Components\Repeater::make('steps')
                                    ->hiddenLabel()
                                    ->relationship('steps')
                                    ->schema([
                                        Forms\Components\Hidden::make('enrollment_id'),
                                        Forms\Components\Hidden::make('course_id'),
                                        Forms\Components\Hidden::make('user_id'),
                                        Forms\Components\Hidden::make('stepable_id'),
                                        Forms\Components\Hidden::make('stepable_type'),
                                        Forms\Components\Hidden::make('position'),
                                        Forms\Components\Hidden::make('is_completed'),
                                    ])
                                    ->afterStateHydrated(function (Forms\Components\Repeater $component, $state) {
                                        if (empty($state)) {
                                            $enrollment = Enrollment::find(request()->route('record'));
                                            if ($enrollment) {
                                                $steps = $enrollment->steps()->orderBy('position')->get();
                                                $component->state($steps->toArray());
                                            }
                                        }
                                    })
                                        ->itemLabel(function (array $state): ?string {
                                        if (empty($state['stepable_type'])) {
                                            return '';
                                        }
                                        return match ($state['stepable_type']) {
                                            Lesson::class => 'Урок - ' . Lesson::findOrFail($state['stepable_id'])->name,
                                            Quiz::class => 'Тест',
                                            default => '',
                                        };

                                    })
                                    ->columns()
                                    ->collapsible()
                                    ->collapsed()
                                    ->addable(false)
//                                    ->addActionLabel('Добавить тест')
                                    ->defaultItems(0),*/
                                Forms\Components\Actions::make([
                                    Action::make('set_steps')
                                        ->label('Создать план обучения')
                                        ->icon('heroicon-o-clipboard-document-list')
                                        ->color('success')
                                        ->requiresConfirmation()
                                        ->action(function (Enrollment $record) {
                                            static::setSteps($record);
                                        })
                                        ->hidden(function (Enrollment $record) {
                                            return $record->hasSteps();
                                        }),
                                ]),
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
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Курс')
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

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
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
//        dump($stepsData);
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
//        dump($steps);

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
