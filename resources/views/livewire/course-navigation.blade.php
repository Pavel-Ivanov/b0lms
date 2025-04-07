<div>
    <ul role="list" class="divide-y divide-gray-200">
        @foreach($steps as $step)
            @php
                $stepModel = $step->stepableModel();
                $stepType = $step->stepableType();
                $isActive = $this->isStepActive($step->id);
            @endphp
            @if($stepType === 'Lesson')
                @include('filament.intern.pages.lesson-item')
            @elseif($stepType === 'Quiz')
                @include('filament.intern.pages.quiz-item')
            @endif
        @endforeach
    </ul>
</div>
