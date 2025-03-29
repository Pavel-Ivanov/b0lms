<?php

namespace App\Filament\Sadmin\Resources;

use App\Filament\Sadmin\Resources\CourseResource\Pages;
use App\Filament\Sadmin\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Курс';
    protected static ?string $pluralModelLabel = 'Курсы';
    protected static ?string $navigationGroup = 'Академия';
    protected static ?string $navigationLabel = 'Курсы';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Главная')
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Название')
                                            ->required()
                                            ->maxLength(255),
                                        Textarea::make('announcement')
                                            ->label('Анонс'),
                                        RichEditor::make('description')
                                            ->label('Описание'),
                                    ]),
                                Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('duration')
                                            ->label('Длительность (минуты)')
                                            ->numeric(),
                                        Checkbox::make('is_published')
                                            ->label('Опубликован'),
                                    ])
                                ->columns(2),
                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Forms\Components\Select::make('course_type_id')
                                                    ->label('Тип')
                                                    ->relationship('courseType', 'name')
                                                    ->required(),
                                                Forms\Components\Select::make('course_level_id')
                                                    ->label('Уровень')
                                                    ->relationship('courseLevel', 'name')
                                                    ->required(),
                                                Forms\Components\Select::make('course_category_id')
                                                    ->label('Категория')
                                                    ->relationship('courseCategory', 'name')
                                                    ->required(),

                                        ]),
                                        Group::make()
                                            ->schema([
                                                SpatieMediaLibraryFileUpload::make('Course Image')
                                                    ->collection('course_images'),
                                        ]),
                                    ])
                                    ->columns(2),
                            ]),
                        Tabs\Tab::make('Уроки')
                            ->schema([
                                Repeater::make('lessons')
                                    ->label('Уроки')
                                    ->relationship('lessons')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Название')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('position')
                                            ->required()
                                            ->numeric()
                                            ->default(0),
                                        Forms\Components\Toggle::make('is_published')
                                            ->required(),
                                        Forms\Components\Hidden::make('course_id')
                                            ->default(fn (): ?int => $form->model['id'] ?? null)
                                    ])
                                    ->itemLabel(fn (array $state): ?string => $state['position'] . '. ' . $state['name'] ?? null)
                                    ->orderColumn('position')
                                    ->columns()
                                    ->collapsible()
                                    ->collapsed()
                                    ->addActionLabel('Добавить урок')
                                    ->defaultItems(0),
                            ]),
                        Tabs\Tab::make('Назначения')
                            ->schema([
                                Repeater::make('enrollments')
                                    ->label('Назначения курсов')
                                    ->relationship('enrollments')
                                    ->schema([
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
                                        Forms\Components\Hidden::make('course_id')
                                            ->default(fn (): ?int => $form->model['id'] ?? null)
                                    ])
                                    ->itemLabel(function (array $state): ?string {
                                        if (empty($state['user_id'])) {
                                            return '';
                                        }
                                        return Enrollment::where('id', $state['id'])->first()->enrollmentInfo();                                    })
                                    ->columns()
                                    ->collapsible()
                                    ->collapsed()
                                    ->addActionLabel('Добавить назначение')
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
                SpatieMediaLibraryImageColumn::make('Course Image')
                    ->label('Изображение')
                    ->collection('course_images'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('courseCategory.name')
                    ->label('Категория'),
                Tables\Columns\TextColumn::make('courseType.name')
                    ->label('Тип'),
                Tables\Columns\TextColumn::make('courseLevel.name')
                    ->label('Уровень'),
                Tables\Columns\TextColumn::make('lessons_count')
                    ->label('Кол-во уроков')
                    ->counts('lessons'),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label('Опубликован'),
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
                SelectFilter::make('courseCategory')
                    ->label('Категория')
                    ->relationship('courseCategory', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('courseType')
                    ->label('Тип')
                    ->relationship('courseType', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('courseLevel')
                    ->label('Уровень')
                    ->relationship('courseLevel', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->hiddenLabel(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
