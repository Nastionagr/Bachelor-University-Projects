@php
$sort_items = [['price-low', 'Price ascending'],['price-high', 'Price descending'],['newest', 'Newest'],['oldest'
, 'Oldest'],['a-z' , 'A-Z'],['z-a' , 'Z-A']];
@endphp
<form id="order-form" action="" method="GET">
    <fieldset class="border p-2">
        <legend class="h6 text-muted w-auto px-2 m-0"><small>Sort items</small></legend>
        <select class="btn text-left" onchange="$('#order-form').submit();">
            @foreach ($sort_items as list($item,$name))
            <option value="{{ $item }}"
                {{ is_null(request()->sort) ? ($item == 'a-z' ? 'selected' : '') : (request()->sort == $item ? 'selected':'') }}>
                {{ $name }}</option>
            @endforeach
        </select>
    </fieldset>
</form>