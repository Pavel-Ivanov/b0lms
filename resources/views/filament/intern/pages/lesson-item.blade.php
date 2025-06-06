<li @class([
    'py-4 flex gap-x-4',
    'hover:bg-gray-100 hover:text-primary-600' => !$isActive
])>
    <a href="{{ $this->getUrl($stepModel) }}" wire:click="setActiveStepId({{ $step->id }})">
        <div class="flex items-center gap-x-3">
            <x-filament::icon
                icon="heroicon-o-document-text"
                class="w-7 h-7 text-gray-600"
            />
            <div class="flex-auto">
                <div class="flex items-baseline justify-between gap-x-4">
                    <p @class([
                        'flex-auto truncate text-sm/6 font-semibold',
                        'text-danger-600' => $isActive,
                        'text-gray-900' => !$isActive
                    ])>
                        {{ $stepModel->name }}
                    </p>
                </div>
                <p class="mt-1 line-clamp-2 text-sm text-gray-500">
                    {{ $stepModel->announcement }}
                </p>
            </div>
            {{-- Uncomment if needed
            <x-filament::icon
                icon="heroicon-o-check-circle"
                class="w-7 h-7 text-green-600"
                style="min-width:28px;"
            />
            --}}
        </div>
    </a>
</li>
