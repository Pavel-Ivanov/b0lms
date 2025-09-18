<?php

namespace App\States;

use App\Livewire\TestManager;
use Illuminate\Contracts\View\View;

class ResultState extends TestManagerState
{
    public function getName(): string
    {
        return 'result';
    }

    public function render(): View
    {
        return view('livewire.test-manager.result', [
            'quiz' => $this->manager->quiz,
            'enrollmentStep' => $this->manager->enrollmentStep,
            'userTestAttempt' => $this->manager->userTestAttempt,
            'questions' => $this->manager->questions,
            'userAnswers' => $this->manager->userAnswers,
            'totalQuestions' => $this->manager->getTotalQuestions(),
            'currentAttemptNumber' => $this->manager->currentAttemptNumber,
            'effectiveMaxAttempts' => $this->manager->enrollmentStep->max_attempts ?? $this->manager->quiz->max_attempts,
            'effectivePassingPercentage' => $this->manager->enrollmentStep->passing_percentage ?? $this->manager->quiz->passing_percentage,
        ]);
    }

    public function enter(): void
    {
//        dump('entering result state');
        // Calculate score and finish the attempt if the test attempt exists and the status is not completed
        if ($this->manager->userTestAttempt && $this->manager->userTestAttempt->status !== 'completed') {
            $this->manager->calculateScoreAndFinishAttempt();
        }
    }
}
