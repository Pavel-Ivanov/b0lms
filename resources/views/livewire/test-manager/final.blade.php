<div class="space-y-6">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ $userTestAttempt->passed ? 'Тест успешно пройден' : 'Тест не пройден' }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                @if($userTestAttempt->passed)
                    Поздравляем! Вы успешно прошли тест и можете продолжить обучение.
                @else
                    К сожалению, вы исчерпали все попытки прохождения теста.
                @endif
            </p>
        </div>

        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Название теста</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $quiz->name }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Статус</dt>
                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                        @if($userTestAttempt->passed)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Пройден
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Не пройден
                            </span>
                        @endif
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Лучший результат</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $userTestAttempt->result }} из {{ $totalQuestions }}
                        ({{ $totalQuestions > 0 ? round(($userTestAttempt->result / $totalQuestions) * 100, 0) : 0 }}%)
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Проходной балл</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $quiz->passing_percentage }}%</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Использовано попыток</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $userTestAttempt->current_attempt }} из {{ $quiz->max_attempts }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    @if($userTestAttempt->passed)
        <div class="bg-green-50 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Шаг обучения завершен</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>Этот шаг обучения отмечен как завершенный. Вы можете продолжить обучение.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="flex justify-center">
        <x-filament::button
            tag="a"
            href="{{ url()->previous() }}"
            color="primary"
        >
            К списку тестов
        </x-filament::button>
    </div>
</div>
