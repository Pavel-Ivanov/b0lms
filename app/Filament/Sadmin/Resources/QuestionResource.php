<?php

namespace App\Filament\Sadmin\Resources;

use App\Filament\Sadmin\Resources\QuestionResource\Pages;
use App\Filament\Sadmin\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?int $navigationSort = 8;
    protected static ?string $modelLabel = 'Вопрос';
    protected static ?string $pluralModelLabel = 'Вопросы';
    protected static ?string $navigationGroup = 'Академия';
    protected static ?string $navigationLabel = 'Вопросы';


    public static function form(Form $form): Form
    {
        return $form
            ->schema(array_merge(
                    static::questionForm(),
                    [
                        Forms\Components\Select::make('lesson_id')
                            ->label('Урок')
                            ->columnSpanFull()
//                            ->visibleOn('edit')
                            ->relationship('lesson', 'name')
                    ])
            );

/*        ->schema([
                Forms\Components\Select::make('course_id')
                    ->relationship('course', 'name')
                    ->required(),
                Forms\Components\Textarea::make('question_text')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('answer_explanation')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('more_info_link')
                    ->maxLength(255),
            ]);*/
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lesson.name')
                    ->label('Урок')
                    ->sortable(),
                Tables\Columns\TextColumn::make('question_text')
                    ->label('Текст вопроса')
                    ->searchable(),
                Tables\Columns\TextColumn::make('more_info_link')
                    ->label('Ссылка на доп. инф.')
                    ->searchable()
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
                //
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }

    public static function questionForm(): array
    {
        return [
            Forms\Components\Textarea::make('question_text')
                ->label('Текст вопроса')
                ->required()
                ->columnSpanFull(),
/*            Forms\Components\Repeater::make('questionOptions')
                ->required()
                ->relationship()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\TextInput::make('option')
                        ->required()
                        ->hiddenLabel(),
                    Forms\Components\Checkbox::make('correct'),
                ])->columns(),*/
            Forms\Components\Textarea::make('answer_explanation')
                ->label('Объяснение правильного ответа')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('more_info_link')
                ->label('Ссылка на дополнительную информацию')
                ->columnSpanFull(),
        ];
    }

}
