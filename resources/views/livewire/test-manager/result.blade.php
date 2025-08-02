<div class="space-y-6">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Результаты теста</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                {{ $userTestAttempt->passed ? 'Поздравляем! Вы успешно прошли тест.' : 'К сожалению, вы не прошли тест.' }}
            </p>
        </div>

        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Правильные ответы</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $userTestAttempt->result }} из {{ $totalQuestions }}
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Процент правильных</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $totalQuestions > 0 ? round(($userTestAttempt->result / $totalQuestions) * 100, 0) : 0 }}%
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Проходной балл</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $quiz->passing_percentage }}%</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Затраченное время</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $userTestAttempt->time_spent }} сек.
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Попытка</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $userTestAttempt->current_attempt }} из {{ $quiz->max_attempts }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Подробные результаты</h3>
        </div>

        <div class="border-t border-gray-200">
            <div class="divide-y divide-gray-200">
                @foreach($questions as $index => $question)
                    <div class="px-4 py-5 sm:px-6 {{ $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' }}">
                        <h4 class="text-base font-medium text-gray-900">
                            Вопрос {{ $index + 1 }}: {{ $question->question_text }}
                        </h4>

                        <div class="mt-2 space-y-2">
                            @foreach($question->questionOptions as $option)
                                @if(isset($userAnswers[$question->id]) && $userAnswers[$question->id] == $option->id)
                                    <div class="flex items-start space-x-2">
                                        <div class="flex-shrink-0 mt-0.5">
                                            @if($option->correct)
                                                <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <svg class="h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="text-sm font-medium {{ $option->correct ? 'text-green-700' : 'text-red-700' }}">
                                            {{ $option->option }}
                                            @if($option->rationale)
                                                <p class="mt-1 text-xs text-gray-500">{{ $option->rationale }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="flex justify-end space-x-4">
        @if(!$userTestAttempt->passed && $currentAttemptNumber < $quiz->max_attempts)
            <x-filament::button
                wire:click="startTest"
                color="primary"
            >
                Пройти тест заново
            </x-filament::button>
        @endif
            @if($userTestAttempt->passed)
                <x-filament::button
                    wire:click="nextStep"
                    color="success"
                >
                    Перейти к следующему шагу
                </x-filament::button>
            @endif

            {{--
                    <x-filament::button
                        wire:click="showFinalState"
                        color="success"
                    >
                        Показать общие результаты
                    </x-filament::button>
            --}}
    </div>
</div>
