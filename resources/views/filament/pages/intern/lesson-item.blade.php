<li @class([
    'flex gap-x-4 py-4',
    'hover:bg-gray-100 hover:text-primary-600' => !$item['active'] && $item['accessible'],
    'opacity-50 cursor-not-allowed' => !$item['accessible']
])>
    <div class="flex-none">
        <x-heroicon-o-book-open class="h-6 w-6 text-gray-600" />
    </div>
    <div class="flex-auto">
        @if($item['accessible'])
            <a href="{{ $item['url'] }}">
                @else
                    <div>
                        @endif
                        <div class="flex items-baseline justify-between gap-x-4">
                            <p @class([
                                'text-sm font-semibold',
                                'text-danger-600' => $item['active'],
                                'text-gray-900' => !$item['active'] && $item['accessible'],
                                'text-gray-400' => !$item['accessible']
                            ])>
                                {{ $item['stepModel']->name }}
                            </p>
                        </div>
                    @if($item['accessible'])
                        </a>
                    @else
                        </div>
                    @endif
    </div>
    <div class="flex-none ml-auto">
        @if(!$item['accessible'])
            <x-heroicon-o-lock-closed class="w-6 h-6 text-gray-400" />
        @else
            <x-heroicon-o-check-circle
                @class(['w-6 h-6',
                     'text-green-600' => $item['completed'],
                     'text-gray-400' =>!$item['completed'],
                ])
            />
        @endif
    </div>
</li>
