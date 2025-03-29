<div {{ $attributes }}>
    <ul role="list" class="divide-y divide-gray-200">
        @foreach($getLessons() as $lesson)
            @include('infolists.components.lesson-item', ['lesson' => $lesson])

            @if($lesson->quizzes->isNotEmpty())
                @foreach($lesson->quizzes as $quiz)
                    @include('infolists.components.quiz-item', ['quiz' => $quiz, 'lesson' => $lesson])
                @endforeach
            @endif
        @endforeach
    </ul>
</div>
