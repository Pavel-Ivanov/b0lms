<div>
    @if (!$completed)
        <form wire:submit="submit" class="space-y-4">
            {{ $this->form }}
        </form>
    @else
        <div class="p-4 rounded-md" :class="{'bg-success-500/10': state === 'success', 'bg-danger-500/10': state === 'failure'}">
            <p class="font-semibold" :class="{'text-success-500': state === 'success', 'text-danger-500': state === 'failure'}">{{ $message }}</p>
        </div>
    @endif
</div>
