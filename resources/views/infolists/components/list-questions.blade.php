<div {{ $attributes }}>
    <h2 class="text-2xl font-bold mb-4">Вопросы урока</h2>
    <ul role="list" class="divide-y divide-gray-200">
        @foreach($getQuestions() as $index => $question)
            <li class="py-4 px-6 flex items-start gap-4">
                {{ $question->question_text }}

                @foreach($question->questionOptions as $option)
                    <div wire:key="option.{{ $option->id }}">
                        <label for="option.{{ $option->id }}">
                            <x-filament::input.radio
                                id="option.{{ $option->id }}"
                                value="{{ $option->id }}"
{{--                                name="questionsAnswers.{{ $currentQuestionIndex }}"--}}
{{--                                wire:model="questionsAnswers.{{ $currentQuestionIndex }}"--}}
                            />

                            <span>
                            {{ $option->option }}
                        </span>
                        </label>
                    </div>
                @endforeach

            </li>
        @endforeach
    </ul>


{{--    <form wire:submit.prevent="saveAnswers">--}}
        @foreach($getQuestions() as $question)
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">{{ $question->question_text }}</h3>

                @foreach($question->questionOptions as $option)
                    <div class="flex items-center mb-2">
                        <input
                            type="radio"
                            id="question_{{ $question->id }}_option_{{ $option->id }}"
                            name="userAnswers.{{ $question->id }}"
                            value="{{ $option->id }}"
                            wire:model="userAnswers.{{ $question->id }}"
{{--                            class="mr-2"--}}
                        >
                        <label for="question_{{ $question->id }}_option_{{ $option->id }}">
                            {{ $option->option }}
                        </label>
                    </div>
                @endforeach
            </div>
        @endforeach

    <div class="mt-6">
            <x-filament::button type="submit" wire:click="submit">
                Ответить
            </x-filament::button>

    </div>

{{--    </form>--}}
</div>
