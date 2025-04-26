<x-filament-panels::page>

    <div class="mx-auto w-full max-w-7xl grow flex flex-col-reverse lg:flex-row xl:px-2">
        <!-- Left sidebar & main wrapper -->
        <div class="flex-1 xl:flex">
            <div class="px-4 py-6 sm:px-6 lg:pl-8 xl:w-64 xl:shrink-0 xl:pl-6">
                @if ($activeLesson)
                    @include('filament.pages.intern.lesson-content', ['lesson' => $activeLesson, 'enrollment' => $this->getEnrollment(), 'activeStep' => $this->getActiveStep()])
                @elseif ($activeQuiz)
                    @include('filament.pages.intern.quiz-content', ['quiz' => $activeQuiz, 'enrollment' => $this->getEnrollment(), 'activeStep' => $this->getActiveStep()])
                @else
                    <div>
                        <h2>Информация о назначении</h2>
                        <p>Курс: {{ $this->enrollment->course->name }}</p>
                        <p>Пользователь: {{ $this->enrollment->user->name }}</p>
                    </div>
                @endif
            </div>

            <div class="px-4 py-6 sm:px-6 lg:pl-8 xl:flex-1 xl:pl-6">

            </div>
        </div>
        <!-- Right sidebar -->
        <div class="shrink-0 px-4 py-6 sm:px-6 lg:max-w-xs lg:pr-8 xl:pr-6 lg:order-first break-words">
            @include('filament.pages.intern.enrollment-progress')
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($this->getNavigation() as $item)
                    @include($item['template'])
                @endforeach
            </ul>
        </div>
    </div>

</x-filament-panels::page>
