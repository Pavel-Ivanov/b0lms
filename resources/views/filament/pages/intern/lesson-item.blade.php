<li @class([
    'py-4 flex gap-x-4',
    'hover:bg-gray-100 hover:text-primary-600' => !$item['active']
])>
    <a href="{{ $item['url'] }}">
        <div class="flex items-center gap-x-3">
            <x-filament::icon
                icon="heroicon-o-document-text"
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
                    {{ $item['stepModel']->announcement }}
                </p>
            </div>
{{--            @dump($item['step']->is_completed)--}}
            <x-filament::icon
                icon="heroicon-o-check-circle"
                @class([
                   'w-7 h-7',
                    'text-danger-600' => $item['completed'],
                    'text-gray-400' =>!$item['completed'],
                ])
{{--                style="min-width:28px;"--}}
            />

        </div>
    </a>
</li>
