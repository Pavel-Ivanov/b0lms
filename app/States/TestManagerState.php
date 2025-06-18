<?php

namespace App\States;

use App\Livewire\TestManager;
use Illuminate\Contracts\View\View;

abstract class TestManagerState
{
    protected TestManager $manager;

    public function __construct(TestManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get the name of the state
     */
    abstract public function getName(): string;

    /**
     * Render the state view
     */
    abstract public function render(): View;

    /**
     * Handle any initialization when entering this state
     */
    public function enter(): void
    {
        // Default implementation does nothing
    }

    /**
     * Determine if the state should transition to another state
     */
    public function shouldTransition(): ?string
    {
        return null; // Default implementation stays in the current state
    }
}
