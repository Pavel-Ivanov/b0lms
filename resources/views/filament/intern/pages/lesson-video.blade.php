<div>
    <ul role="list" class="grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 sm:gap-x-6 lg:grid-cols-4 xl:gap-x-8">
        @foreach($data as $clip)
            <li class="relative">
                <div class="group overflow-hidden rounded-lg bg-gray-100 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 focus-within:ring-offset-gray-100">
                    <video width="320" height="240" controls>
                        <source src="{{ asset($clip->getUrl()) }}" type="video/mp4">
                        Ваш браузер не поддерживает видео.
                    </video>
                </div>
                <p class="pointer-events-none mt-2 block truncate text-sm font-medium text-gray-900">Название</p>
                <p class="pointer-events-none block text-sm font-medium text-gray-500">Описание</p>
            </li>
        @endforeach
    </ul>
</div>
