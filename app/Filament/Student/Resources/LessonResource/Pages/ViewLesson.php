<?php

namespace App\Filament\Student\Resources\LessonResource\Pages;

use App\Filament\Student\Resources\CourseResource;
use App\Filament\Student\Resources\LessonResource;
use App\Filament\Traits\HasParentResource;
use App\Infolists\Components\CompleteButton;
use App\Infolists\Components\CourseProgress;
use App\Infolists\Components\LessonPaginator;
use App\Infolists\Components\ListLessons;
use App\Infolists\Components\ListQuestions;
use App\Infolists\Components\ListSteps;
use App\Models\Enrollment;
use App\Models\Lesson;
use Awcodes\Matinee\Matinee;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Support\Enums\ActionSize;
use Hugomyb\FilamentMediaAction\Infolists\Components\Actions\MediaAction;
use Illuminate\Contracts\View\View;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewLesson extends ViewRecord
{
    use HasParentResource;

    protected static string $parentResource = CourseResource::class;

    protected static string $resource = LessonResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->name;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->getRecord())
            ->schema([
                Grid::make()
                    ->columns(1)
                    ->schema([
//                        CompleteButton::make(),
                        TextEntry::make('lesson_content')
                            ->hiddenLabel()
                            ->html()
                            ->size(TextEntrySize::Medium)
                        ,
                        TextEntry::make('media')
                            ->hiddenLabel()
                            ->formatStateUsing(function (Lesson $record)
                                {
                                    $data = $this->getRecord()->media;
                                    return view('infolists.components.lesson-video', ['data' => $data]);
                                })
                    ])
                    ->columnSpan(2),
                Grid::make()
                    ->columns(1)
                    ->schema([
/*                        CourseProgress::make()
                            ->course($this->getRecord()->course),*/
/*                        ListLessons::make('Lessons')
                            ->course($this->getRecord()->course)
                            ->activeLesson($this->getRecord()),*/
                        ListSteps::make('Шаги')
                            ->enrollment(Enrollment::where('course_id', $this->getRecord()->course->id)->where('user_id', auth()->id())->first()),

                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public function toggleCompleted(): void
    {
        $lesson = $this->getRecord();

        $lesson->isCompleted() ? $lesson->markAsUncompleted() : $lesson->markAsCompleted();
    }

    public function markAsCompletedAndGoToNext()
    {
        $lesson = $this->getRecord();
//        $lesson->markAsCompleted();

        return redirect()->to($this->getParentResource()::getUrl('lessons.view', [
            $lesson->course,
            $lesson->getNext(),
        ]));
    }

    public function submit(): void
    {
        $result = 0;

        /*        $test = Test::create([
                    'user_id'    => auth()->id(),
                    'quiz_id'    => $this->record->id,
                    'result'     => 0,
                    'ip_address' => request()->ip(),
                    'time_spent' => now()->timestamp - $this->startTimeSeconds,
                ]);

                foreach ($this->questionsAnswers as $key => $option) {
                    info($option);
                    $status = 0;

                    if (! empty($option) && QuestionOption::find($option)->correct) {
                        $status = 1;
                        $result++;
                    }

                    TestAnswer::create([
                        'user_id'     => auth()->id(),
                        'test_id'     => $test->id,
                        'question_id' => $this->questions[$key]->id,
                        'option_id'   => $option ?? null,
                        'correct'     => $status,
                    ]);
                }

                $test->update([
                    'result' => $result,
                ]);

                $this->redirectIntended(ResultResource::getUrl('view', ['record' => $test]));*/
    }
}
