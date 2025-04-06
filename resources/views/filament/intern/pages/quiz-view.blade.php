<x-filament-panels::page>

<div class="mx-auto w-full max-w-7xl grow flex flex-col-reverse lg:flex-row xl:px-2">
    <!-- Left sidebar & main wrapper -->
    <div class="flex-1 xl:flex">
        <div class="px-4 py-6 sm:px-6 lg:pl-8 xl:w-64 xl:shrink-0 xl:pl-6">
            @if(!$completed)
                <form wire:submit="submit">
                    {{ $this->form }}
                </form>
            @else
                <x-filament::section>
                    <x-slot name="heading">
                        Результаты теста
                    </x-slot>

                    <p class="text-center">{{ $message }}</p>
                </x-filament::section>
            @endif
        </div>

        <div class="px-4 py-6 sm:px-6 lg:pl-8 xl:flex-1 xl:pl-6">

        </div>
    </div>

    <div class="shrink-0 px-4 py-6 sm:px-6 lg:w-96 lg:pr-8 xl:pr-6 lg:order-first">
        @livewire('course-navigation', ['enrollment' => $enrollment])
    </div>
</div>

</x-filament-panels::page>
