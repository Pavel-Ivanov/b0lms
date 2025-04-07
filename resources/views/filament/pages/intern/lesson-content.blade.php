<div>
    @if ($lesson)
        <h1>{{ $lesson->name }}</h1>
        <p>{!! $lesson->lesson_content !!}</p>

        @if ($enrollment)
            @php
                $enrollmentStep = $enrollment->steps()
                    ->where('stepable_id', $lesson->id)
                    ->where('stepable_type', \App\Models\Lesson::class)
                    ->first();
            @endphp
            @if ($enrollmentStep)
                <p>Статус шага: {{ $enrollmentStep->is_completed ? 'Завершено' : 'Не завершено' }}</p>
                <p>Позиция шага: {{ $enrollmentStep->position }}</p>
{{-- Здесь вы можете добавить дополнительную информацию о прохождении урока --}}

            @endif
        @endif
    @else
        <p>Урок не найден.</p>
    @endif
</div>
