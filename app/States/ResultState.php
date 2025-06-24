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
            'userTestAttempt' => $this->manager->userTestAttempt,
            'questions' => $this->manager->questions,
            'userAnswers' => $this->manager->userAnswers,
            'totalQuestions' => $this->manager->getTotalQuestions(),
            'currentAttemptNumber' => $this->manager->currentAttemptNumber,
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
