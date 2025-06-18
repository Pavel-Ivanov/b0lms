<?php

namespace App\States;

use App\Livewire\TestManager;
use App\Models\Test;
use Illuminate\Contracts\View\View;

class TestFormState extends TestManagerState
{
    public function getName(): string
    {
        return 'test_form';
    }

    public function render(): View
    {
        return view('livewire.test-manager.test-form', [
            'quiz' => $this->manager->quiz,
            'userTestAttempt' => $this->manager->userTestAttempt,
            'totalQuestions' => $this->manager->getTotalQuestions(),
            'currentAttempt' => $this->manager->currentAttempt,
        ]);
    }

    public function enter(): void
    {
        // Determine the current attempt number
        $currentAttempt = 1; // Default to 1 for the first attempt

        // If a test record exists and it's completed, increment the attempt counter
        if ($this->manager->userTestAttempt && $this->manager->userTestAttempt->status === 'completed') {
            $currentAttempt = $this->manager->userTestAttempt->current_attempt + 1;
        }

        // Store the current attempt number in the manager
        $this->manager->currentAttempt = $currentAttempt;

        // Reset answers and question index
        $this->manager->userAnswers = [];
        $this->manager->currentQuestionIndex = 0;
    }
}
