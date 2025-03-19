<div {{ $attributes }}>
    <hr>
    <h2 class="text-2xl font-bold mb-4">Вопросы урока</h2>
        @foreach($getQuestions() as $questionIndex => $question)
            <div class="mb-2">
                <x-filament::input.wrapper>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2">{{ $question->question_text }}</h3>

                        @foreach($question->questionOptions as $option)
                            <div class="flex items-center mb-2">
                                <div wire:key="option.{{ $option->id }}">
                                    <label for="option.{{ $option->id }}">

                                        <x-filament::input.radio
                                            id="option.{{ $option->id }}"
                                            value="{{ $option->id }}"
                                            name="questionsAnswers.{{ $questionIndex }}"
                                            wire:model="questionsAnswers.{{ $questionIndex }}"
                                        />
                                        <span>
                                            {{ $option->option }}
                                        </span>

                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-filament::input.wrapper>
            </div>
        @endforeach

    <div class="mt-6">
            <x-filament::button type="submit" wire:click="submit">
                Завершить урок
            </x-filament::button>
    </div>
</div>
