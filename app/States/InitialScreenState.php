<?php

namespace App\States;

use App\Livewire\TestManager;
use Illuminate\Contracts\View\View;

class InitialScreenState extends TestManagerState
{
    public function getName(): string
    {
        return 'initial_screen';
    }

    public function render(): View
    {
        return view('livewire.test-manager.initial-screen', [
            'quiz' => $this->manager->quiz,
            'userTestAttempt' => $this->manager->userTestAttempt,
            'totalQuestions' => $this->manager->getTotalQuestions(),
            'currentAttempt' => $this->manager->currentAttemptNumber,
        ]);
    }

    public function shouldTransition(): ?string
    {
        // If the enrollment step is already completed, go to final state
        if ($this->manager->enrollmentStep->is_completed) {
            return 'final';
        }

        // If all attempts are used, go to final state
        if ($this->manager->currentAttemptNumber >= $this->manager->quiz->max_attempts) {
            return 'final';
        }

        return null;
    }
}
