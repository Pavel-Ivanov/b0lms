<x-filament-panels::page>
    <div class="flex h-screen overflow-hidden">
        <div class="w-64 border-r border-gray-200 bg-gray-100">
            <nav class="py-6">
                <ul role="list" class="space-y-1">
                    @foreach ($this->getNavigation() as $item)
                        <li class="filament-navigation-item">
                            <a
                                href="{{ $item['url'] }}"
                                @class([
                                    'filament-navigation-item-link flex items-center gap-2 py-2 px-3 rounded-md font-medium transition-colors duration-200 hover:bg-gray-50',
                                    'bg-primary-50 text-primary-600' => $item['active'],
                                    'text-gray-600' => !$item['active'],
                                ])
                            >
                                <x-filament::icon icon="{{ $item['icon'] }}" class="h-5 w-5" />
                                <span>{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>

        <div class="flex-1 overflow-y-auto p-6">
            @if ($activeLesson)
                @include('filament.pages.intern.lesson-content', ['lesson' => $activeLesson, 'enrollment' => $this->getEnrollment()])
            @elseif ($activeQuiz)
                @include('filament.pages.intern.quiz-content', ['quiz' => $activeQuiz, 'enrollment' => $this->getEnrollment()])
            @else
                <div>
                    <h2>Информация о назначении</h2>
                    <p>Курс: {{ $this->enrollment->course->name }}</p>
                    <p>Пользователь: {{ $this->enrollment->user->name }}</p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
