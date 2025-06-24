<div class="space-y-6">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
{{--            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $quiz->name }}</h3>--}}
            <p class="mt-1 text-sm text-gray-500">{{ $quiz->description }}</p>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Количество вопросов</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $totalQuestions }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Проходной балл</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $quiz->passing_percentage }}%</dd>
                </div>
{{--
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Максимальное количество попыток</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $quiz->max_attempts }}</dd>
                </div>
--}}
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Количество попыток</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $currentAttempt }} из {{ $quiz->max_attempts }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="flex justify-center">
        <x-filament::button
            wire:click="startTest"
            color="primary"
            size="lg"
            class="px-8 py-3"
        >
            Начать тест
        </x-filament::button>
    </div>
</div>
