<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\EnrollmentResource\Pages;
use App\Filament\Student\Resources\EnrollmentResource\RelationManagers;
use App\Models\Enrollment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Назначение';
    protected static ?string $pluralModelLabel = 'Мои назначения';
//    protected static ?string $navigationGroup = 'Академия';
    protected static ?string $navigationLabel = 'Мои назначения';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Grid::make()
                    ->columns(1)
                    ->schema([
/*                        SpatieMediaLibraryImageColumn::make('Course Image')
                            ->collection('course_images')
                            ->extraImgAttributes(['class' => 'w-full rounded'])
                            ->height('auto'),*/
                        TextColumn::make('course.name')
                            ->weight(FontWeight::SemiBold)
                            ->size(TextColumnSize::Large),
                        TextColumn::make('course.announcement')
                            ->html(),
//                        ProgressColumn::make('Progress'),
                    ]),
            ])
            ->contentGrid(['md' => 2, 'xl' => 3])
            ->paginationPageOptions([9, 18, 27])
//            ->defaultSort('name', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->whereIn('id', auth()->user()->enrollments()->pluck('id')))
            ->recordUrl(fn (Model $model) => CourseResource::getUrl('view', [$model]))
            ;
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
//            'create' => Pages\CreateEnrollment::route('/create'),
//            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
