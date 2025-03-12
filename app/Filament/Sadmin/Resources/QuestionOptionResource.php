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
                    ->relationship('question', 'id')
                    ->required(),
                Forms\Components\TextInput::make('option')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('correct')
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
            'index' => Pages\ListQuestionOptions::route('/'),
            'create' => Pages\CreateQuestionOption::route('/create'),
            'edit' => Pages\EditQuestionOption::route('/{record}/edit'),
        ];
    }
}
