<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\CourseResource\Pages;
use App\Filament\Student\Resources\CourseResource\Pages\ViewCourse;
use App\Filament\Student\Resources\CourseResource\RelationManagers;
use App\Filament\Student\Resources\LessonResource\Pages\ViewLesson;
use App\Filament\Student\Resources\QuizResource\Pages\ViewQuiz;
use App\Models\Course;
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

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Курс';
    protected static ?string $pluralModelLabel = 'Мои курсы';
//    protected static ?string $navigationGroup = 'Академия';
    protected static ?string $navigationLabel = 'Мои курсы';


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
                        SpatieMediaLibraryImageColumn::make('Course Image')
                            ->collection('course_images')
                            ->extraImgAttributes(['class' => 'w-full rounded'])
                            ->height('auto'),
                        TextColumn::make('name')
                            ->weight(FontWeight::SemiBold)
                            ->size(TextColumnSize::Large),
                        TextColumn::make('announcement')
                            ->html(),
//                        ProgressColumn::make('Progress'),
                    ]),
            ])
            ->contentGrid(['md' => 2, 'xl' => 3])
            ->paginationPageOptions([9, 18, 27])
            ->defaultSort('name', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->published()
                ->whereIn('id', auth()->user()->courses->pluck('id')))
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
            'index' => Pages\ListCourses::route('/'),
            'view'  => Pages\ViewCourse::route('/{record}'),
            'lessons.view' => ViewLesson::route('/{parent}/lessons/{record}'),
            'quizzes.view' => ViewQuiz::route('/{parent}/quizzes/{record}'),
//            'create' => Pages\CreateCourse::route('/create'),
//            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
