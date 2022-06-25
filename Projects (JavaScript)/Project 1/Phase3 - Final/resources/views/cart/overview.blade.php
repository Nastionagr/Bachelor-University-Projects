@extends('layout.page')

@section('content')

<main class="content container my-4">
    @include('cart.components.navigation')
    <!-- Cart products -->
    <div class="container my-4 flex-fill text-left px-0">
        @if ( (auth()->check() && auth()->user()->cart_products()->whereNull('order_id')->count() > 0) ||
        ( !auth()->check() && isset($cart['products']) && isset($cart['total'])) )
        <div class="row col-12 mx-2 text-center d-none d-md-flex">
            <div class="col-md-7     col-8">ITEM</div>
            <div class="col-2 d-none d-md-block">PRICE</div>
            <div class="col-md-2 col-3">QUANTITY</div>
            <div class="col-1"></div>
        </div>
        <hr class="my-2" />
        @foreach ($cart['products'] as $product)
        <div class="row mx-2 py-2 pb-3 pb-sm-0 border-bottom">
            <div class="col-md-2 col-sm-3 col-12 cart-item-img px-0 pb-3 pb-sm-0"><img class="card-img"
                    src="{{ config('app.image_path').$product['images'][0] }}" alt="" /></div>
            <div class="col-md-5 col-sm-6 col-12 m-auto">
                <h6>{{ $product['brand'] }} {{ $product['name'] }}</h6>
                <p class="cart-item-desc">{{ $product['description'] }}</p>
                <a class="d-none d-sm-block" href="{{URL::to('/products/'.$product['id'])}}"><span
                        class="font-italic">See
                        more details</span></a>
            </div>
            <div class="col-2 m-auto text-center d-none d-md-block"><span>€ {{$product['price']}}</span></div>
            <form method="POST" action="/cart/update" class="col-sm-2 col-6 m-auto d-flex justify-content-center">
                @method('patch')
                @csrf
                <input type="hidden" name="id" value="{{$product['id']}}" />
                <input name="quantity" value="{{$product['quantity']}}" min="0" class="form-control quantity-input"
                    type="number" onChange="$(this).parent().submit();" />
            </form>
            <form method="POST" action="/cart/remove/{{$product['id']}}" class="col-sm-1 col-6 m-auto text-center">
                @method('delete')
                @csrf
                <i class="fas fa-2x fa-times-circle" onClick="$(this).parent().submit()"></i>
            </form>
        </div>
        @endforeach
        <div class="text-right my-4 mx-4 h5">
            <span>Subtotal:</span>
            <span id="cart-price">€ {{ $cart['total'] }}</span>
        </div>
        @else
        <div class="my-auto">
            <h5 class="my-auto text-center">No products in cart.</h5>
        </div>
        @endif
    </div>
    <div class="row row-gap justify-content-around mx-2">
        <div class="has-icon-left">
            <i class="fas fa-lg fa-angle-left icon"></i>
            <a href="{{ URL::to('/products') }}" class="btn light-pink-bg">Continue shopping</a>
        </div>
        @if ( (auth()->check() && auth()->user()->cart_products()->whereNull('order_id')->count() > 0) ||
        ( !auth()->check() && isset($cart['products']) && isset($cart['total'])) )
        <div class="has-icon-right {{ isset($cart['products']) ? '' : 'invisible'}}">
            <i class="fas fa-lg fa-angle-right icon"></i>
            <a href="{{ URL::to('/cart/info') }}" class="btn light-pink-bg">Proceed to checkout</a>
        </div>
        @endif
    </div>
</main>
@endsection