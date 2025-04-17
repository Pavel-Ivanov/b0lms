<div>
    @if ($lesson)
        <div class="border-b border-gray-200 pb-4">
            <h1 class="text-xl font-semibold text-gray-900">
                {{ $lesson->name }}
            </h1>
            <p class="mt-2 max-w-4xl text-base text-gray-500">
                {{ $lesson->announcement }}
            </p>
        </div>

        <div class="mt-2 mx-auto max-w-7xl border-b pb-4">
            {!! $lesson->lesson_content !!}
        </div>

        <div class="mt-2 mx-auto max-w-7xl border-b pb-4">
            @include('filament.pages.intern.lesson-video', ['data' => $lesson->media])
        </div>

        <div class="mt-2 mx-auto max-w-7xl">
            <p>Статус: {{ $lesson->is_completed ? 'Завершено' : 'Не завершено' }}</p>
        </div>
    @endif
</div>
