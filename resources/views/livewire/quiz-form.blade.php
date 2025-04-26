<div>
    @if (!$activeStep->is_completed)
        <form wire:submit="submit" class="space-y-4">
            {{ $this->form }}
        </form>
    @else
        <div class="bg-white rounded-lg shadow-md p-6 mx-auto max-w-md">
            <h2 class="text-xl font-semibold text-gray-700 text-center mb-4">Ваш результат</h2>

            <div class="relative w-32 h-16 mx-auto">
                <svg class="absolute top-0 left-0 w-full h-full" viewBox="0 0 100 20" preserveAspectRatio="xMidYMid meet">
                    <line x1="5" y1="10" x2="95" y1="10" stroke="#e5e7eb" stroke-width="10" stroke-linecap="round" />
                    <line x1="5" y1="10" x2="calc(5 + (90 * 0.8))" y1="10" stroke="linear-gradient(to right, #10b981 0%, #f43f5e 100%)" stroke-width="10" stroke-linecap="round" />
                </svg>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-4xl font-bold text-blue-600">8</div>
                <div class="absolute bottom-1 left-1/2 -translate-x-1/2 text-sm text-gray-500 text-center">из 10<br>80%</div>
            </div>

            <div class="mt-6 grid grid-cols-3 gap-4">
                <div class="bg-white rounded-md border border-green-300 p-3 text-center">
                    <div class="text-sm font-medium text-green-500 mb-1">Верно</div>
                    <div class="text-2xl font-bold text-gray-800">8</div>
                </div>
                <div class="bg-white rounded-md border border-red-300 p-3 text-center">
                    <div class="text-sm font-medium text-red-500 mb-1">Неверно</div>
                    <div class="text-2xl font-bold text-gray-800">2</div>
                </div>
            </div>

            <button class="block w-full rounded-md border border-gray-300 bg-white text-gray-700 py-2 px-4 font-medium shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 mt-4">Посмотреть результаты</button>
            <button class="block w-full rounded-md bg-blue-600 text-white py-3 px-4 font-medium shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 mt-2">Продолжить обучение</button>

            <p class="block text-sm text-blue-500 underline text-center mt-4 cursor-pointer hover:text-blue-700">Пройти тест заново</p>
        </div>
    @endif
</div>
