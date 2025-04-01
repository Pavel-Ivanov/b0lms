<li @class([
    'py-4 flex gap-x-4',
    'hover:bg-gray-100 hover:text-primary-600' => !$isActive($quiz)
])>
    <a href="#">
        <div class="flex items-center gap-x-3">
            <x-filament::icon
                icon="heroicon-o-question-mark-circle"
                class="w-7 h-7 text-gray-600"
            />
            <div class="flex-auto">
                <div class="flex items-baseline justify-between gap-x-4">
                    <p class="text-sm/6 font-semibold text-gray-900">
                        {{ $quiz->name }}
                    </p>
                </div>
                <p class="mt-1 line-clamp-2 text-sm text-gray-500">
                    0 из {{ $quiz->questions->count() }} вопросов
                </p>
            </div>
            {{-- Uncomment if needed
            <x-filament::icon
                icon="heroicon-o-check-circle"
                class="w-7 h-7"
            />
            --}}
        </div>
    </a>
</li>
