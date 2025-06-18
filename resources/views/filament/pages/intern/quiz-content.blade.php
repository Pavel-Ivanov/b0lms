{{--
    Variables:
    - $quiz: The Quiz object containing quiz details
    - $enrollment: The Enrollment object for the current user
    - $activeStep: The current active step in the quiz
--}}
{{--@dump($quiz, $enrollment, $activeStep)--}}
<div>
    @if ($quiz)
        <div class="">
            <h1 class="text-xl font-semibold text-gray-900">
                {{ $quiz->name }}
            </h1>
        </div>

        <div class="mt-2 mx-auto max-w-7xl border-b pb-4">
            <livewire:test-manager :quiz="$quiz" :enrollment="$enrollment" :enrollmentStep="$activeStep"/>
        </div>
    @endif
</div>
