<div>
    @if (!$activeStep->is_completed)
        <form wire:submit="submit" class="space-y-4">
            {{ $this->form }}
        </form>
    @else
{{--@dump($latestTest)--}}
        <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Правильные ответы</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $latestTest->result ?? 0 }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Процент правильных</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                    {{ count($questions) > 0 ? round(($latestTest->result / count($questions)) * 100, 0) : 0 }}%
                </dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Затраченное время</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $latestTest->time_spent ?? '0:00' }} сек.</dd>
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

{{--
        <x-filament::modal id="quiz-details" width="md">
            <x-slot name="heading">
                Детали теста
            </x-slot>

            {{ $this->form }}

            <x-slot name="footer">
                <x-filament::button wire:click="$dispatch('close-modal', { id: 'quiz-details' })">
                    Закрыть
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
--}}
</div>
