
<div class="flex items-center gap-x-3">
    <x-filament::icon
        :icon="$icon"
        class="w-5 h-5 {{ $color }}"
    />

    <div class="flex flex-col">
{{--        <span>{{ $type }} {{ isset($courseName) ? $courseName : $text }}</span>--}}
        <span>{{ isset($courseName) ? $courseName : $text }}</span>
        @if(isset($parameters))
            <span class="text-gray-500 text-sm">{{ $parameters }}</span>
        @endif
    </div>
</div>
