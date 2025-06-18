<?php

namespace App\States;

use App\Livewire\TestManager;

class StateFactory
{
    /**
     * Create a state object based on the state name
     */
    public static function createState(string $stateName, TestManager $manager): TestManagerState
    {
        return match ($stateName) {
            'initial_screen' => new InitialScreenState($manager),
            'test_form' => new TestFormState($manager),
            'result' => new ResultState($manager),
            'final' => new FinalState($manager),
            default => throw new \InvalidArgumentException("Unknown state: {$stateName}"),
        };
    }
}
