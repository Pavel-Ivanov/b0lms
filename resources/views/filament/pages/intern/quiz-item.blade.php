<li @class([
    'flex gap-x-4 py-4',
    'hover:bg-gray-100 hover:text-primary-600' => !$item['active']
])>
    <div class="flex-none">
        <x-heroicon-o-question-mark-circle class="h-6 w-6 text-gray-600" />
    </div>
    <div class="flex-auto">
        <a href="{{ $item['url'] }}">
            <div class="flex items-baseline justify-between gap-x-4">
                <p @class([
                    'text-sm font-semibold',
                    'text-danger-600' => $item['active'],
                    'text-gray-900' => !$item['active']
                ])>
                    {{ $item['stepModel']->name }}
                </p>
            </div>
            <p class="mt-1 line-clamp-2 text-xs text-gray-500">
                0 из {{ $item['stepModel']->questions->count() }} вопросов
            </p>
        </a>
    </div>
    <div class="flex-none ml-auto">
        <x-heroicon-o-check-circle
            @class(['w-6 h-6',
                 'text-danger-600' => $item['completed'],
                 'text-gray-400' =>!$item['completed'],
            ])
        />
    </div>


{{--
    <a href="{{ $item['url'] }}">
        <div class="flex items-center gap-x-3">
            <x-filament::icon
                icon="heroicon-o-question-mark-circle"
                class="w-7 h-7 text-gray-600"
            />
            <div class="flex-auto">
                <div class="flex items-baseline justify-between gap-x-4">
                    <p @class([
                        'flex-auto truncate text-sm/6 font-semibold',
                        'text-danger-600' => $item['active'],
                        'text-gray-900' => !$item['active']
                    ])>
                    {{ $item['stepModel']->name }}
                    </p>
                </div>
                <p class="mt-1 line-clamp-2 text-sm text-gray-500">
                    0 из {{ $item['stepModel']->questions->count() }} вопросов
                </p>
            </div>
            --}}
{{-- Uncomment if needed
            <x-filament::icon
                icon="heroicon-o-check-circle"
                class="w-7 h-7"
            />
            --}}{{--

        </div>
    </a>
--}}
</li>
