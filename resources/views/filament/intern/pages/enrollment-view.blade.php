<x-filament-panels::page>

    <div class="mx-auto w-full max-w-7xl grow flex flex-col-reverse lg:flex-row xl:px-2">
        <div class="flex-1 flex flex-col">
            <div class="px-4 py-6 sm:px-6 lg:pl-8 xl:pl-6">
                @if ($activeLesson)
                    @include('filament.pages.intern.lesson-content',
                            ['lesson' => $activeLesson,
                             'enrollment' => $this->getEnrollment(),
                             'activeStep' => $this->getActiveStep()
                             ])
                @elseif ($activeQuiz)
                    @include('filament.pages.intern.quiz-content',
                            ['quiz' => $activeQuiz,
                            'enrollment' => $this->getEnrollment(),
                            'activeStep' => $this->getActiveStep()
                            ])
                @else
                    <div>
                        <h2>Информация о назначении</h2>
                        <p>Курс: {{ $this->enrollment->course->name }}</p>
                        <p>Пользователь: {{ $this->enrollment->user->name }}</p>
                    </div>
                @endif
            </div>
            <div class="px-4 py-6 sm:px-6 lg:pl-8 xl:pl-6">
            </div>
        </div>
        <div class="shrink-0 px-4 py-6 sm:px-6 lg:max-w-xs lg:pr-8 xl:pr-6 break-words">
            @include('filament.pages.intern.enrollment-progress')
            <x-filament.intern.enrollment-navigation :enrollment="$enrollment" :active-step-id="$activeStep?->id" />
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('enrollment-step-completed', () => {
                Livewire.dispatch('refresh-enrollment-view');
            });

            Livewire.on('enrollment-navigation-update', () => {
                Livewire.dispatch('refresh-enrollment-view');
            });
        });
    </script>

</x-filament-panels::page>
