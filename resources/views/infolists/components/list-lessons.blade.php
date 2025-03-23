<div {{ $attributes }}>
    <ul role="list" class="divide-y divide-gray-200">

        @foreach($getLessons() as $index => $lesson)
{{--
            <li @class([
                    'py-4', 'flex',  'gap-x-4',
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
    --}}
{{--                    <time datetime="2023-01-23T11:00" class="flex-none text-xs text-gray-500">1h</time>--}}{{--

                    </div>
                    <p class="mt-3 truncate text-sm text-gray-500">{{ $lesson->announcement }}</p>
                </a>

            </li>
--}}

            {{--            Урок--}}
            <li @class([
                    'py-4', 'flex',  'gap-x-4',
                    'hover:bg-gray-100 hover:text-primary-600' => !$isActive($lesson)
                ])>
                <a href="{{ $getUrl($lesson) }}">
                    <div class="flex items-center gap-x-3">
                        <x-filament::icon icon="heroicon-o-document-text" class="w-7 h-7 text-gray-600" style="min-width:28px;">
        {{--
                            @class([
                                'w-7 h-7', 'text-gray-600',
                            ])>
        --}}
                        </x-filament::icon>
                        <div @class([
                            'flex-auto'
                            ])>
                            <div class="flex items-baseline justify-between gap-x-4">
                                <p
        {{--                            class="text-sm/6 font-semibold text-gray-900">--}}
                                    @class([
                                            'flex-auto',
                                            'truncate',
                                            'text-sm/6',
                                            'font-semibold',
                                            'text-danger-600' => $isActive($lesson),
                                            'text-gray-900' =>!$isActive($lesson)
                                    ])>

                                    {{ $lesson->name }}
                                </p>
        {{--
                                <p @class([
                                    'flex-none', 'text-xs', 'text-gray-600',
                                ])>
                                    4/5
                                </p>
        --}}
                            </div>
                            <p @class([
                                    'mt-1', 'line-clamp-2', 'text-sm', 'text-gray-500',
                                ])>
                                {{ $lesson->announcement }}
                            </p>
                        </div>
                        <x-filament::icon icon="heroicon-o-check-circle" style="min-width:28px;"
                              @class([
                                  'w-7', 'h-7', 'text-green-600',
                              ])>
                        </x-filament::icon>
                    </div>
                </a>
            </li>

            {{--            Тест--}}
            <li @class([
                    'py-4 flex gap-x-4',
                    'hover:bg-gray-100 hover:text-primary-600' => !$isActive($lesson)
                ])>
                <a href="{{ $getUrl($lesson) }}">
                    <div class="flex items-center gap-x-3">
                        <x-filament::icon icon="heroicon-o-question-mark-circle" class="w-7 h-7 text-gray-600">
                        </x-filament::icon>
                        <div @class([
                            'flex-auto'
                            ])>
                            <div class="flex items-baseline justify-between gap-x-4">
                                <p class="text-sm/6 font-semibold text-gray-900">
                                    Тест
                                </p>
                                <p class="flex-none text-xs text-gray-600">
                                    4/5
                                </p>
                            </div>
                            <p class="mt-1 line-clamp-2 text-sm text-gray-500">
                                5 вопросов
                            </p>
{{--
                            <p class="text-xs text-gray-600">
                                4/5
                            </p>
--}}
                        </div>
                        <x-filament::icon icon="heroicon-o-check-circle"
                                          @class([
                                              'w-7', 'h-7', 'text-green-600',
                                          ])>
                        </x-filament::icon>
                    </div>
                </a>
            </li>

{{--
        <li>
            <div class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-2 py-2 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2
            hover:border-gray-400" hover:bg-gray-100>
                <x-filament::icon icon="heroicon-o-document-text" class="w-7 h-7 text-gray-600"></x-filament::icon>
                <div class="flex-auto">
                    <div class="flex items-baseline justify-between gap-x-4">
                        <p class="text-sm/6 font-semibold text-gray-900">{{ $lesson->name }}</p>
                        <p class="flex-none text-xs text-gray-600">
                            <time datetime="2023-03-04T15:54Z">4/5</time>
                        </p>
                    </div>
                    <p class="mt-1 line-clamp-2 text-sm text-gray-500">
                        {{ $lesson->announcement }}
                    </p>
                </div>
            </div>

        </li>
--}}

{{--            @endif--}}
        @endforeach
    </ul>
</div>


