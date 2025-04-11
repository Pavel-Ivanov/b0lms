<div>
    @if ($quiz)
        <div class="">
            <h1 class="text-xl font-semibold text-gray-900">
                {{ $quiz->name }}
            </h1>
        </div>

        <div class="mt-2 mx-auto max-w-7xl border-b pb-4">
            <livewire:quiz-form :quiz="$quiz" :enrollment="$enrollment" />
        </div>

        <div class="mt-2 mx-auto max-w-7xl">
            <p>Статус: {{ $quiz->is_completed ? 'Завершено' : 'Не завершено' }}</p>
        </div>
    @endif
</div>
