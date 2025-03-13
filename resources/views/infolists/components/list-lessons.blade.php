<div {{ $attributes }}>
    <ul role="list" class="divide-y divide-gray-200">
        @foreach($getLessons() as $index => $lesson)
            <li @class([
                    'py-4',
                    'hover:bg-gray-100 hover:text-primary-600' => !$isActive($lesson)
                ])>
                <a href="{{ $getUrl($lesson) }}">
                <div class="flex items-center gap-x-3">
                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                        {{ $index + 1 }}
                    </span>
                    <p @class([
                            'flex-auto',
                            'truncate',
                            'text-sm/6',
                            'font-semibold',
                            'text-danger-600' => $isActive($lesson),
                            'text-gray-900' =>!$isActive($lesson)
                    ])>
                        {{ $lesson->name }}
                    </p>
{{--                    <time datetime="2023-01-23T11:00" class="flex-none text-xs text-gray-500">1h</time>--}}
                </div>
                <p class="mt-3 truncate text-sm text-gray-500">{{ $lesson->announcement }}</p>
                </a>
            </li>
        @endforeach
    </ul>
</div>


