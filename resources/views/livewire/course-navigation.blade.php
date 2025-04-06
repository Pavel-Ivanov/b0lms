<div>
    <ul role="list" class="divide-y divide-gray-200">
        @foreach($steps as $step)
            @php
                $stepModel = $step->stepableModel();
                $stepType = $step->stepableType();
            @endphp
            @if($stepType === 'Lesson')
                @include('filament.intern.pages.lesson-item', [
                    'lesson' => $stepModel,
                ])
            @elseif($stepType === 'Quiz')
                @include('filament.intern.pages.quiz-item', [
                    'quiz' => $stepModel,
                ])
            @endif
        @endforeach
    </ul>
</div>
