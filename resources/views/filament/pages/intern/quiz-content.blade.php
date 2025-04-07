<div>
    @if ($quiz)
        <h2>{{ $quiz->name }}</h2>
        <p>{{ $quiz->description }}</p>

        @if ($enrollment)
            @php
                $enrollmentStep = $enrollment->steps()
                    ->where('stepable_id', $quiz->id)
                    ->where('stepable_type', \App\Models\Quiz::class)
                    ->first();
            @endphp
            @if ($enrollmentStep)
                <p>Статус шага: {{ $enrollmentStep->is_completed ? 'Завершено' : 'Не завершено' }}</p>
                <p>Позиция шага: {{ $enrollmentStep->position }}</p>
                {{-- Здесь вы можете добавить дополнительную информацию о прохождении теста (например, результаты) --}}

            @endif
        @endif
    @else
        <p>Тест не найден.</p>
    @endif
</div>
