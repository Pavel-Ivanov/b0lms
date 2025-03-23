<?php

namespace App\Filament\Sadmin\Resources;

use App\Filament\Sadmin\Resources\LessonResource\Pages;
use App\Filament\Sadmin\Resources\LessonResource\RelationManagers;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuestionOption;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Урок';
    protected static ?string $pluralModelLabel = 'Уроки';
    protected static ?string $navigationGroup = 'Академия';
    protected static ?string $navigationLabel = 'Уроки';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Основная информация')
                            ->schema([
                                Forms\Components\Select::make('course_id')
                                    ->label('Курс')
                                    ->relationship('course', 'name')
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->label('Название урока')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('announcement')
                                    ->label('Анонс')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('position')
                                    ->label('Позиция урока')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Опубликовать')
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Содержание урока')
                            ->schema([
                                Forms\Components\RichEditor::make('lesson_content')
                                    ->label('Содержание урока')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('lesson_images')
                                    ->fileAttachmentsVisibility('public')
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Тесты')
                            ->schema([
                                Forms\Components\Repeater::make('quizzes')
                                    ->hiddenLabel()
                                    ->relationship('quizzes')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Название теста')
                                            ->required()
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Описание теста')
                                            ->columnSpanFull(),
                                        Forms\Components\Checkbox::make('is_published')
                                            ->label('Опубликован'),

                                        /*                                        Forms\Components\Textarea::make('question_text')
                                                                                    ->label('Текст вопроса')
                                                                                    ->required()
                                                                                    ->columnSpanFull(),
                                                                                Forms\Components\Repeater::make('questionOptions')
                                                                                    ->required()
                                                                                    ->relationship()
                                                                                    ->columnSpanFull()
                                                                                    ->schema([
                                                                                        Forms\Components\TextInput::make('option')
                                                                                            ->label('Ответ')
                                                                                            ->required()
                                                                                            ->hiddenLabel(),
                                                                                        Forms\Components\Checkbox::make('correct')
                                                                                            ->label('Правильный ответ'),
                                                                                    ])
                                                                                    ->columns()
                                                                                    ->addActionLabel('Добавить ответ')
                                                                                    ->reorderable(true)
                                                                                    ->reorderableWithButtons()
                                                                                    ->cloneable(),
                                                                                Forms\Components\Textarea::make('answer_explanation')
                                                                                    ->label('Объяснение правильного ответа')
                                                                                    ->columnSpanFull(),
                                                                                Forms\Components\TextInput::make('more_info_link')
                                                                                    ->label('Ссылка на дополнительную информацию')
                                                                                    ->columnSpanFull(),*/
                                    ])
                                        ->itemLabel(function (array $state): ?string {
                                            if (empty($state['name'])) {
                                                return '';
                                            }
                                            return $state['name'];
                                        })
                                        ->columns()
                                    ->collapsible()
                                    ->collapsed()
    //                                ->addable(false)
                                    ->addActionLabel('Добавить тест')
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
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Курс')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Позиция')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quizzes_count')
                    ->label('Кол-во тестов')
                    ->counts('quizzes'),
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
                SelectFilter::make('course')
                    ->label('Курс')
                    ->relationship('course', 'name')
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
            'index' => Pages\ListLessons::route('/'),
            'create' => Pages\CreateLesson::route('/create'),
            'edit' => Pages\EditLesson::route('/{record}/edit'),
        ];
    }
}
