<?php

namespace App\Filament\Sadmin\Resources;

use App\Filament\Sadmin\Resources\QuestionOptionResource\Pages;
use App\Filament\Sadmin\Resources\QuestionOptionResource\RelationManagers;
use App\Models\QuestionOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionOptionResource extends Resource
{
    protected static ?string $model = QuestionOption::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?int $navigationSort = 9;
    protected static ?string $modelLabel = 'Ответ';
    protected static ?string $pluralModelLabel = 'Ответы';
    protected static ?string $navigationGroup = 'Справочники';
    protected static ?string $navigationLabel = 'Ответы на вопросы';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('question_id')
                    ->label('Вопрос')
                    ->relationship('question', 'question_text')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Textarea::make('option')
                    ->label('Ответ')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('rationale')
                    ->label('Объяснение ответа')
                    ->nullable()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('correct')
                    ->label('Правильный')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question.question_text')
                    ->label('Вопрос')
                    ->sortable(),
                Tables\Columns\TextColumn::make('option')
                    ->label('Ответ')
                    ->searchable(),
                Tables\Columns\IconColumn::make('correct')
                    ->label('Правильный')
                    ->boolean(),
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
                SelectFilter::make('question')
                    ->label('Вопрос')
                    ->relationship('question', 'question_text')
                    ->searchable()
                    ->preload(),
                Filter::make('correct')
                    ->label('Правильный')
                    ->toggle()
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->hiddenLabel(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            $referencedOptions = [];

                            foreach ($records as $record) {
                                $testAnswersCount = \App\Models\TestAnswer::where('option_id', $record->id)->count();
                                if ($testAnswersCount > 0) {
                                    $referencedOptions[] = [
                                        'option' => $record->option,
                                        'count' => $testAnswersCount
                                    ];
                                } else {
                                    $record->delete();
                                }
                            }

                            if (!empty($referencedOptions)) {
                                $message = "Следующие опции не могут быть удалены, так как они используются в ответах тестов:\n";
                                foreach ($referencedOptions as $option) {
                                    $message .= "- \"{$option['option']}\" используется в {$option['count']} ответах\n";
                                }

                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Некоторые опции используются')
                                    ->body($message)
                                    ->send();
                            }
                        }),
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
            'index' => Pages\ListQuestionOptions::route('/'),
            'create' => Pages\CreateQuestionOption::route('/create'),
            'edit' => Pages\EditQuestionOption::route('/{record}/edit'),
        ];
    }
}
