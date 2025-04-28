<div>
    @if (!$activeStep->is_completed)
        <form wire:submit="submit" class="space-y-4">
            {{ $this->form }}
        </form>
    @else

        <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Правильные ответы</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $quiz->correct_answers ?? 0 }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Процент правильных</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                    {{ $quiz->questions_count > 0 ? round(($quiz->correct_answers / $quiz->questions_count) * 100, 2) : 0 }}%
                </dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Затраченное время</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $quiz->time_spent ?? '0:00' }}</dd>
            </div>
        </dl>

        <div class="mt-6 flex justify-center gap-4">
            <x-filament::button type="button"
                wire:click="viewDetails"
                color="success">
                Подробные результаты
            </x-filament::button>
            <x-filament::button type="button"
                wire:click="resetResults">
                Пройти заново
            </x-filament::button>
        </div>
    @endif
</div>
