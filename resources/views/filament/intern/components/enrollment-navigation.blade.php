<div>
    <ul role="list" class="divide-y divide-gray-200">
        @foreach($navigation as $item)
            @include($item['template'], $item)
        @endforeach
    </ul>
</div>
