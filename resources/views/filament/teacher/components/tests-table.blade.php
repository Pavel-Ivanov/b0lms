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
            <tr>
                <th class="px-3 py-2 text-left font-medium text-gray-500">#</th>
                <th class="px-3 py-2 text-left font-medium text-gray-500">Результат</th>
                <th class="px-3 py-2 text-left font-medium text-gray-500">Пройден</th>
                <th class="px-3 py-2 text-left font-medium text-gray-500">Начат</th>
                <th class="px-3 py-2 text-left font-medium text-gray-500">Завершен</th>
                <th class="px-3 py-2 text-left font-medium text-gray-500">Время (сек)</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($tests as $t)
                <tr>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $t->attempt_number }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $t->result}} / {{$t->quiz->questions->count()}}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $t->passed ? 'Да' : 'Нет' }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ optional($t->started_at)->format('d.m.Y H:i') }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ optional($t->completed_at)->format('d.m.Y H:i') }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $t->time_spent }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-sm text-gray-500">Нет попыток</div>
@endif
