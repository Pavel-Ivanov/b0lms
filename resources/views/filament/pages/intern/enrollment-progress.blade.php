<div>
    @php
        $progress = $this->getProgress();

        if($progress['percent'] == 100){
            $progressColor = '#2980b9';
        } else if($progress['percent'] > 50){
            $progressColor = '#27ae60';
        } else if($progress['percent'] > 25){
            $progressColor = '#f39c12';
        } else {
            $progressColor = '#e74c3c';
        }

        $displayProgress = $progress['percent'] == 100 ? number_format($progress['percent'], 0) : number_format($progress['percent'], 2);
    @endphp

    <div class="progress-container">
        <div class="progress-bar" style="width: {{ $progress['percent'] }}%; background-color: {{ $progressColor }};"></div>
        <div class="progress-text">
                <small @class([
                'text-gray-700' => $progress['percent'] != 100,
                'text-white' => $progress['percent'] == 100
                ])>
                    {{ $progress['completed'] }} из {{ $progress['total'] }} выполнено / {{ $progress['percent'] }}%
                </small>
        </div>
    </div>

    <style>
        .progress-container {
            width: 100%;
            background-color: #e5e7eb;
            border-radius: 0.375rem;
            height: 1.5rem;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .progress-bar {
            height: 100%;
            border-radius: 0.375rem;
            transition: width 0.3s, background-color 0.3s;
            width: 0;
        }
        .progress-text {
            text-align: center;
            font-size: 0.875rem;
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }
        .progress-bar::after {
            content: '';
            display: block;
            height: 100%;
            border-radius: 0.375rem;
            background: rgba(255, 255, 255, 0.2);
            /*background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 25%, rgba(255, 255, 255, 0) 25%, rgba(255, 255, 255, 0) 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, rgba(255, 255, 255, 0) 75%, rgba(255, 255, 255, 0) 100%);*/
            background-size: 40px 40px;
            /*animation: progress-bar-stripes 1s linear infinite;*/
        }
        @keyframes progress-bar-stripes {
            from {
                background-position: 40px 0;
            }
            to {
                background-position: 0 0;
            }
        }
    </style>

</div>
