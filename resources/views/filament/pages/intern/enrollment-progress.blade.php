<div>
    @php
        $progress = $this->getProgress();

        function getProgressColor($percent) {
            if ($percent <= 25) return '#e74c3c';
            if ($percent <= 50) return '#f39c12';
            if ($percent < 100) return '#27ae60';
            return '#2980b9';
        }

        $progressColor = getProgressColor($progress['percent']);
    @endphp

    <div class="w-full h-6 bg-gray-200 rounded-md overflow-hidden relative shadow-inner">
        <div class="h-full rounded-md transition-all duration-300 ease-in-out"
             style="width: {{ $progress['percent'] }}%; background-color: {{ $progressColor }};">
            <div class="absolute inset-0 bg-white/20"></div>
        </div>

        <div class="absolute inset-0 flex items-center justify-center text-sm">
            <small @class([
                'font-semibold drop-shadow-sm',
                'text-gray-700' => $progress['percent'] != 100,
                'text-white' => $progress['percent'] == 100
            ])>
                {{ $progress['completed'] }} из {{ $progress['total'] }} выполнено / {{ $progress['percent'] }}%
            </small>
        </div>
    </div>
</div>
