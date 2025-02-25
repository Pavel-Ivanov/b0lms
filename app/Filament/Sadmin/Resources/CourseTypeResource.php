<?php

namespace App\Filament\Sadmin\Resources;

use App\Filament\Sadmin\Resources\CourseTypeResource\Pages;
use App\Filament\Sadmin\Resources\CourseTypeResource\RelationManagers;
use App\Models\CourseType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseTypeResource extends Resource
{
    protected static ?string $model = CourseType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Тип курса';
    protected static ?string $pluralModelLabel = 'Типы курсов';
    protected static ?string $navigationGroup = 'Академия';

    protected static ?string $navigationLabel = 'Типы';


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
            'index' => Pages\ManageCourseTypes::route('/'),
        ];
    }
}
