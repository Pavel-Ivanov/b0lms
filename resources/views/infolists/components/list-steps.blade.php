<div {{ $attributes }}>
    <ul role="list" class="divide-y divide-gray-200">
        @foreach($getSteps() as $step)
            @php
                $stepModel = $step->stepableModel();
            @endphp

            @if($getStepType($step) === 'Lesson')
                @include('infolists.components.lesson-item', [
                    'lesson' => $stepModel,
                ])
            @elseif($getStepType($step) === 'Quiz')
                @include('infolists.components.quiz-item', [
                    'quiz' => $stepModel,
                ])
            @endif
        @endforeach
    </ul>
</div>
