{{--
    Variables:
    - $quiz: The Quiz object containing quiz details
    - $enrollment: The Enrollment object for the current user
    - $activeStep: The current active step in the quiz
--}}
<div>
    @if ($lesson)
        <div class="flex justify-end">
            <x-filament::button type="button"
                wire:click="markLessonAsCompleted"
                :disabled="$enrollment?->steps->firstWhere('id', $activeStepId)?->is_completed">
                Отметить как завершенный
            </x-filament::button>
        </div>

        <div class="border-b border-gray-200 pb-4">
            <h2 class="text-xl font-semibold text-gray-900">
                {{ $lesson->name }}
            </h2>
            <p class="mt-2 max-w-4xl text-sm text-gray-500">
                {{ $lesson->announcement }}
            </p>
        </div>

        <div class="mt-2 mx-auto max-w-7xl border-b pb-4 prose">
            {!! $lesson->lesson_content !!}
        </div>

        @if($lesson->media->isNotEmpty())
            <div class="mt-2 mx-auto max-w-7xl border-b pb-4">
                @include('filament.pages.intern.lesson-video', ['data' => $lesson->media])
            </div>
        @endif

        <div class="flex mt-2 justify-end">
            <x-filament::button type="button"
                                wire:click="markLessonAsCompleted"
                                :disabled="$enrollment?->steps->firstWhere('id', $activeStepId)?->is_completed">
                Отметить как завершенный
            </x-filament::button>
        </div>

{{--
        <div class="mt-2 mx-auto max-w-7xl">
            <p>Статус: {{ $activeStep->is_completed ? 'Завершено' : 'Не завершено' }}</p>
        </div>
--}}
    @endif
</div>
