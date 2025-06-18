<div class="space-y-6">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ $quiz->name }}
            </h3>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                Попытка {{ $currentAttempt }} из {{ $quiz->max_attempts }}
            </span>
        </div>

        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <form wire:submit="submit" class="space-y-4">
                {{ $this->form }}
            </form>
        </div>
    </div>
</div>
