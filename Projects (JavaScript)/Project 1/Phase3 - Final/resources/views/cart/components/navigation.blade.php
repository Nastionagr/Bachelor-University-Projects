<nav class="cart-header row mx-auto">
    <ul class="d-flex justify-content-center align-items-end px-0 h-100">
        @foreach ($steps as $index=>$step)
        <li class="{{isset($step['active']) ? 'mb-2 border-dark' : ''}} h-100 px-2 pb-1 mx-3 border-bottom">
            @if (isset($step['link']))
            <a href="{{ URL::to('/cart/'.$step['link']) }}" class="{{isset($step['active']) ? '' : 'text-muted'}}">
                <span class="d-none d-sm-block">
                    {{ $step['name'] }}</span>
                <span class="d-sm-none d-block">{{ $index+1 }}</span>
            </a>
            @else
            <span class="d-none d-sm-block {{isset($step['active']) ? '' : 'text-muted'}}">
                {{ $step['name'] }}</span>
            <span class="d-sm-none d-block">{{ $index+1 }}</span>
            @endif
        </li>
        @endforeach
    </ul>
</nav>