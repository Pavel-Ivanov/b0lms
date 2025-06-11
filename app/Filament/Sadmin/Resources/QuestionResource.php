<?php

namespace App\Filament\Sadmin\Resources;

use App\Filament\Sadmin\Resources\QuestionResource\Pages;
use App\Filament\Sadmin\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?int $navigationSort = 4;
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
                        Forms\Components\Select::make('quiz_id')
                            ->label('Тест')
                            ->required()
                            ->columnSpanFull()
//                            ->visibleOn('edit')
                            ->relationship('quiz', 'name'),
                    ])
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question_text')
                    ->label('Текст вопроса')
                    ->limit(50)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('quiz.name')
                    ->label('Тест')
                    ->limit(50)
                    ->sortable(),
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
                SelectFilter::make('quiz_id')
                    ->label('Тест')
                    ->relationship('quiz', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->hiddenLabel(),
            ])
            ->bulkActions([
/*                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),*/
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
            Forms\Components\Repeater::make('questionOptions')
                ->label('Ответы')
                ->required()
                ->relationship()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\TextArea::make('option')
                        ->label('Ответ')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\TextArea::make('rationale')
                        ->label('Объяснение ответа')
                        ->nullable()
                        ->columnSpanFull(),
                    Forms\Components\Checkbox::make('correct')
                    ->label('Правильный ответ'),
                ])
                ->columns()
                ->addActionLabel('Добавить ответ')
                ->reorderable(true)
                ->reorderableWithButtons()
                ->cloneable()
                ->deleteAction(function (\Filament\Forms\Components\Actions\Action $action) {
                    return $action
                        ->action(function (array $arguments, \Filament\Forms\Components\Repeater $component) {
                            $item = $arguments['item'];
                            $record = null;

                            // Get the record from the database if it exists
                            if (isset($component->getItemState($item)['id'])) {
                                $recordId = $component->getItemState($item)['id'];
                                $record = \App\Models\QuestionOption::find($recordId);
                            }

                            // Check if the record has related TestAnswer records
                            if ($record && $record->testAnswers()->count() > 0) {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Невозможно удалить ответ')
                                    ->body('Этот ответ используется в ответах тестов и не может быть удален.')
                                    ->send();

                                return;
                            }

                            // If no related records, proceed with deletion
                            $state = $component->getState();
                            unset($state[$item]);
                            $component->state($state);
                        });
                }),
            Forms\Components\Textarea::make('hint')
                ->label('Подсказка')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('more_info_link')
                ->label('Ссылка на дополнительную информацию')
                ->columnSpanFull(),
        ];
    }

}
