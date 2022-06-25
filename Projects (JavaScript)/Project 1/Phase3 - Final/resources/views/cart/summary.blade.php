@extends('layout.page')

@section('content')

<main class="content container my-4">
    @include('cart.components.navigation')
    <div class="my-4 flex-fill d-flex">
        <div class="col-10 mx-auto">
            <div>
                <h3 class="mb-4">Your Order is Confirmed!</h3>
                <p>Your order <strong>#{{ $order->id }}</strong> has been received and is now being processed.</p>
            </div>
            <div class="mt-5">
                <h4>Order #{{ $order->id }}</h4>
                <span class="text-muted"> {{ $order->created_at }}</span>
                <div class="mb-5 text-left">
                    <div class="text-left text-muted font-25">Items ordered</div>
                    <hr class="my-2 mx-0" />
                    @foreach ($order->cart_products as $cart_item)
                    <div class="row py-2 mx-0" style="font-size: 0.95rem">
                        <div class="col-2 p-0 m-auto"><img class="w-100"
                                src="{{ config('app.image_path').$cart_item->product->images[0] }}" alt="" />
                        </div>
                        <div class=" col-6 m-auto text-left">
                            <h6>{{ $cart_item->product->name }}</h6>
                            <p class="cart-item-desc">{{ $cart_item->product->description }}</p>
                            <a href=""><span class="font-italic">See more details</span></a>
                        </div>
                        <div class="col-2 m-auto text-center font-10"><span>€ {{ $cart_item->product->price }}</span>
                        </div>
                        <div class="col-2 m-auto text-center font-10"><span>x {{ $cart_item->quantity }}</span></div>
                    </div>
                    <hr class="mx-4 my-2" />
                    @endforeach
                    <hr class="my-2 mx-0" />
                    <div class="px-3">
                        <div class="d-flex justify-content-between">
                            <span class="h5">Subtotal</span>
                            <span
                                class="h5">€{{ $order->total - $order->shipping_price - $order->payment_price }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="h5">Shipping & Payment</span>
                            <span class="h5">€{{ $order->shipping_price + $order->payment_price }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="h5">Discount</span>
                            <span class="h5">- €0,00</span>
                        </div>
                        <hr class="my-2 mx-4" />
                        <div class="d-flex justify-content-between">
                            <span class="h5">Total</span>
                            <span class="h5">€{{ $order->total }}</span>
                        </div>
                    </div>
                </div>
                @if ($order->paid)
                <div class="mb-5">
                    <div class="text-left text-muted font-25">Payment information</div>
                    <hr class="my-2 mx-0" />
                    <div class="px-3 d-flex justify-content-between">
                        <span class="h5">Visa (••••••••••4586)</span>
                        <span class="h5">€98,75</span>
                    </div>
                </div>
                @endif
                <div class="mb-5">
                    <div class="row">
                        <div class="col text-left text-muted font-25">Contact</div>
                        <div class="col text-right text-muted font-25">Address</div>
                    </div>
                    <hr class="my-2 mx-0" />
                    <div class="row px-3 text-left font-10">
                        <div class="col">
                            <div>
                                {{ $order->firstName }} {{ $order->lastName }}<br />
                                {{ $order->email }}<br />
                                {{ $order->phone_number }}
                            </div>
                        </div>
                        <div class="col">
                            <div class="float-right">
                                {{ $order->firstName }} {{ $order->lastName }}<br />
                                {{ $order->street }} {{ $order->apt }}<br />
                                {{ $order->zip }} {{ $order->city }}<br />
                                {{ $order->country }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection