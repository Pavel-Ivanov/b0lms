<?php

namespace App\States;

use App\Livewire\TestManager;
use Illuminate\Contracts\View\View;

class FinalState extends TestManagerState
{
    public function getName(): string
    {
        return 'final';
    }

    public function render(): View
    {
        return view('livewire.test-manager.final', [
            'quiz' => $this->manager->quiz,
            'userTestAttempt' => $this->manager->userTestAttempt,
            'enrollmentStep' => $this->manager->enrollmentStep,
            'totalQuestions' => $this->manager->getTotalQuestions(),
        ]);
    }
}
