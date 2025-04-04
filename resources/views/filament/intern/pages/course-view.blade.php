<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="col-span-2">
            <h2 class="text-2xl font-bold mb-4">{{ $record->name }}</h2>
            <p class="mb-4">{{ $record->announcement }}</p>

{{--
            @if ($lessonsAndQuizzes->isNotEmpty())
                @php
                    $firstItem = $lessonsAndQuizzes->first();
                @endphp
                @if ($firstItem['type'] === 'lesson')
                    <livewire:filament.intern.pages.lesson-view :lesson="$firstItem['model']" />
                @elseif ($firstItem['type'] === 'quiz')
                    <livewire:filament.intern.pages.quiz-view :quiz="$firstItem['model']" />
                @endif
            @else
                <p>В этом курсе пока нет уроков и тестов.</p>
            @endif
--}}
        </div>
        <div class="space-y-2">
            <h3 class="font-semibold">Содержание курса</h3>
{{--
            <ul class="space-y-1">
                @foreach ($lessonsAndQuizzes as $item)
                    <li>
                        <a href="{{ $item['url'] }}" class="block py-2 px-3 rounded hover:bg-gray-100">
                            {{ $item['title'] }} ({{ $item['type'] === 'lesson' ? 'Урок' : 'Тест' }})
                        </a>
                    </li>
                @endforeach
            </ul>
--}}
        </div>
    </div>
</x-filament-panels::page>
