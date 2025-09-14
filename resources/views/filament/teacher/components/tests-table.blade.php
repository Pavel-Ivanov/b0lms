@php
    // Retrieve attempts from ViewEntry state provided by Filament Infolists
    // ViewEntry exposes a $getState() helper in the view.
    if (!isset($tests)) {
        try {
            $tests = function_exists('get_defined_vars') ? ($getState()) : null;
        } catch (Throwable $e) {
            $tests = null;
        }
    }
@endphp
@if(($tests ?? null) && count($tests))
    <div class="fi-in-table overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
            <tr class="font-medium text-gray-500">
                <th class="px-3 py-2 text-left">#</th>
                <th class="px-3 py-2 text-left">Результат</th>
                <th class="px-3 py-2 text-left">Пройден</th>
                <th class="px-3 py-2 text-left">Начат</th>
                <th class="px-3 py-2 text-left">Завершен</th>
                <th class="px-3 py-2 text-left">Время (сек)</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($tests as $t)
                <tr class="text-gray-500 text-sm whitespace-nowrap">
                    <td class="px-3 py-2">{{ $t->attempt_number }}</td>
                    <td class="px-3 py-2">{{ $t->result}} / {{$t->quiz->questions->count()}}</td>
                    <td class="px-3 py-2">{{ $t->passed ? 'Да' : 'Нет' }}</td>
                    <td class="px-3 py-2">{{ optional($t->started_at)->format('d.m.Y H:i:s') }}</td>
                    <td class="px-3 py-2">{{ optional($t->completed_at)->format('d.m.Y H:i:s') }}</td>
                    <td class="px-3 py-2">{{ $t->time_spent }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-sm text-gray-500">Нет попыток</div>
@endif
