<?php

namespace App\Filament\Sadmin\Resources;

use App\Filament\Sadmin\Resources\CourseLevelResource\Pages;
use App\Filament\Sadmin\Resources\CourseLevelResource\RelationManagers;
use App\Models\CourseLevel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseLevelResource extends Resource
{
    protected static ?string $model = CourseLevel::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
//    protected static ?int $navigationSort = 5;
    protected static ?string $modelLabel = 'Уровень сложности курса';
    protected static ?string $pluralModelLabel = 'Уровни сложности курсов';
    protected static ?string $navigationGroup = 'Справочники';
    protected static ?string $navigationLabel = 'Уровни сложности курсов';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_published')
                    ->label('Опубликовать')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Опубликовано')
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hiddenLabel(),
                Tables\Actions\DeleteAction::make()
                    ->hiddenLabel(),
            ])
            ->bulkActions([
/*                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),*/
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCourseLevels::route('/'),
        ];
    }
}
