<x-filament-panels::page>

    <div class="mx-auto w-full max-w-7xl grow flex flex-col-reverse lg:flex-row xl:px-2">
        <!-- Left sidebar & main wrapper -->
        <div class="flex-1 xl:flex">
            <div class="px-4 py-6 sm:px-6 lg:pl-8 xl:w-64 xl:shrink-0 xl:pl-6">
                <h2 class="text-2xl font-bold mb-4">{{ $record->name }}</h2>
                <p class="mb-4">{{ $record->announcement }}</p>
            </div>

            <div class="px-4 py-6 sm:px-6 lg:pl-8 xl:flex-1 xl:pl-6">

            </div>
        </div>
        <!-- Right sidebar -->
        <div class="shrink-0 px-4 py-6 sm:px-6 lg:max-w-xs lg:pr-8 xl:pr-6 lg:order-first break-words">
            @livewire('course-navigation', ['enrollment' => $enrollment])
        </div>
    </div>

</x-filament-panels::page>
