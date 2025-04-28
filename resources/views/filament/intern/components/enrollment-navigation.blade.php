<nav class="navigation">
    <ul role="list" class="divide-y divide-gray-200">
        @foreach($navigation as $item)
            @include($item['template'], $item)
{{--    ToDo: Dynamic component rendering instead of include for better performance.
            <x-dynamic-component
                :component="$item['template']"
                :attributes="$item"
            />
--}}
        @endforeach
    </ul>
</nav>
