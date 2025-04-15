<?php

namespace App\Filament\Sadmin\Resources;

use App\Filament\Sadmin\Resources\EnrollmentStepResource\Pages;
use App\Filament\Sadmin\Resources\EnrollmentStepResource\RelationManagers;
use App\Models\EnrollmentStep;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EnrollmentStepResource extends Resource
{
    protected static ?string $model = EnrollmentStep::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Этап обучения';
    protected static ?string $pluralModelLabel = 'Этапы обучения';
    protected static ?string $navigationGroup = 'Справочники';
    protected static ?string $navigationLabel = 'Этапы обучения';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('enrollment_id')
                    ->relationship('enrollment', 'id')
                    ->required(),
                Forms\Components\TextInput::make('course_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('stepable_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('stepable_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('position')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_completed')
                    ->required(),
                Forms\Components\DateTimePicker::make('started_at'),
                Forms\Components\DateTimePicker::make('completed_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
/*                Tables\Columns\TextColumn::make('enrollment.id')
                    ->numeric()
                    ->sortable(),*/
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Курс')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stepable_type')
                    ->label('Тип этапа')
                    ->searchable()
                ->formatStateUsing( fn(string $state) => $state === 'App\Models\Lesson' ? 'Урок' : 'Тест' ),
                Tables\Columns\TextColumn::make('stepable_id')
                    ->label('ID этапа')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Позиция')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_completed')
                    ->label('Завершен')
                    ->boolean(),
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Начат')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Завершен')
                    ->dateTime()
                    ->sortable(),
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
            ->recordUrl(function ($record) {
                return Pages\ViewEnrollmentStep::getUrl([$record->id]);
            })

            ->actions([
                Tables\Actions\EditAction::make()
                    ->hiddenLabel(),
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
            'index' => Pages\ListEnrollmentSteps::route('/'),
            'create' => Pages\CreateEnrollmentStep::route('/create'),
            'view' => Pages\ViewEnrollmentStep::route('/{record}'),
            'edit' => Pages\EditEnrollmentStep::route('/{record}/edit'),
        ];
    }
}
