@extends('layout.page')

@section('content')

<main class="content container-fluid my-4">
    @include('cart.components.navigation')
    <form method="POST" action="/cart/info">
        @csrf
        <div class="row row-gap px-2 px-lg-5 px-sm-0 my-4 flex-fill text-left">
            <div class="col-lg-4 col-sm-10 col-md-8 px-2 mx-auto">
                <div class="p-4 m-0 border rounded-5 shadow">
                    <div id="contact-info-form" class="mb-5">
                        <h5>CONTACT INFORMATION</h5>
                        <hr class="m-1 pb-2" />
                        @guest
                        <div class="form-group">
                            <label for="register-email">E-mail</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="register-email" name="email" value="{{old('email')}}" />
                            @error('email')
                            <span class="invalid-feedback text-left" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group mt-1">
                            <div class="d-flex justify-content-center">
                                <span>Have an account?</span>
                                <a href="/login"><span class="ml-1 font-weight-bold">Login</span></a>
                            </div>
                        </div>
                    </div>
                    <div>
                        <hr class="m-1 pb-2" />
                        @endguest
                        <div class="form-group row">
                            <div class="col-6 pr-1">
                                <label for="shipping-first-name">First Name</label>
                                <input type="text" name="firstName"
                                    class="form-control @error('firstName') is-invalid @enderror"
                                    id="shipping-first-name" value="{{old('firstName')}}" />
                                @error('firstName')
                                <span class="invalid-feedback text-left" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-6 pl-1">
                                <label for="shipping-first-name">Last Name</label>
                                <input type="text" name="lastName"
                                    class="form-control @error('lastName') is-invalid @enderror" id="shipping-last-name"
                                    value="{{old('lastName')}}" />
                                @error('lastName')
                                <span class="invalid-feedback text-left" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="shipping-company">Company</label>
                            <input type="text" class="form-control" id="shipping-company" value="{{old('company')}}" />
                        </div>
                        <div class="form-group">
                            <label for="shipping-street">Street address</label>
                            <input type="text" class="form-control @error('street') is-invalid @enderror"
                                id="shipping-street" name="street" value="{{old('street')}}" />
                            @error('street')
                            <span class="invalid-feedback text-left" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="shipping-apartment">Apartment, suite, etc.</label>
                            <input type="text" class="form-control @error('apt') is-invalid @enderror"
                                id="shipping-apartment" name="apt" value="{{old('apt')}}" />
                            @error('apt')
                            <span class="invalid-feedback text-left" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group row">
                            <div class="col-6 pr-1">
                                <label for="shipping-city">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                    id="shipping-city" name="city" value="{{old('city')}}" />
                                @error('city')
                                <span class="invalid-feedback text-left" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-6 pl-1">
                                <label for="shipping-zip">ZIP / Postal code</label>
                                <input type="text" pattern="[0-9]*"
                                    class="form-control @error('zip') is-invalid @enderror" id="shipping-zip" name="zip"
                                    value="{{old('zip')}}" />
                                @error('zip')
                                <span class="invalid-feedback text-left" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="shipping-country">Country</label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror"
                                id="shipping-country" name="country" value="{{old('country')}}" />
                            @error('country')
                            <span class="invalid-feedback text-left" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="shipping-phone">Phone number</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                id="shipping-phone" name="phone" value="{{old('phone')}}" />
                            @error('phone')
                            <span class="invalid-feedback text-left" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-10 col-md-8 px-2 m-auto">
                <div id="shipping-methods-form" class="mb-5 p-4 border rounded-5 shadow">
                    <h5>SHIPPING METHODS</h5>
                    <hr class="m-1 pb-2" />
                    @foreach ($shipping as $index=>$method)
                    <label class="shipping-method form-check mt-1 pl-2 border rounded align-items-center d-flex">
                        <input class="mx-2" type="radio" name="shipping" price="{{ $method['price'] }}"
                            value="{{ $method['name'] }}" {{ $index == 0 ? 'checked' : '' }} onChange="" />
                        <h5 class="font-weight-bold my-auto mr-3">+ {{$method['price']}}€</h5>
                        <div>
                            <h5 class="shipping-method-name m-0">{{ $method['name'] }}</h5>
                            @if (isset($method['duration']))
                            <span class="d-block text-muted">Est {{$method['duration']}} business days</span>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
                <div id="payment-methods-form" class="p-4 border rounded-5 shadow">
                    <h5>PAYMENT METHODS</h5>
                    <hr class="m-1 pb-2" />
                    <div class="row m-0">
                        <div class="col-4 p-1">
                            <input class="d-none" name="payment" price="1" value="Cash" type="radio" id="payment-cash"
                                checked />
                            <label
                                class="p-1 w-100 h-100 border rounded d-flex justify-content-center align-items-center text-center"
                                for="payment-cash">
                                <div class="payment-icon font-weight-bold font-25">
                                    <span class="d-block">CASH</span>
                                    <span class="d-block">+ 1€</span>
                                </div>
                            </label>
                        </div>
                        <div class="col-4 p-1">
                            <input class="d-none" name="payment" price="0" value="Google-Pay" type="radio"
                                id="payment-gpay" />
                            <label class="p-1 w-100 h-100 border rounded" for="payment-gpay">
                                <div class="payment-icon h-100 d-flex justify-content-center align-items-center">
                                    <i class="fab fa-google-pay fa-3x"></i>
                                </div>
                            </label>
                        </div>
                        <div class="col-4 p-1">
                            <input class="d-none" name="payment" price="0" value="PayPal" type="radio"
                                id="payment-paypal" />
                            <label class="p-1 w-100 h-100 border rounded" for="payment-paypal">
                                <div class="payment-icon h-100 d-flex justify-content-center align-items-center">
                                    <i class="fab fa-cc-paypal fa-3x"></i>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-10 col-md-8 px-2 mx-auto">
                <div id="order-sum-form" class="border rounded-5 p-4 shadow">
                    <h5>ORDER SUMMARY</h5>
                    <hr class="m-1 pb-2" />
                    <div class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="h6">SUBTOTAL:</span>
                            <span id="order-subtotal-price" value="{{ $cart['total'] }}"
                                class="h6">€{{ $cart['total'] }}</span>
                        </div>
                        <div class="text-muted pl-4">-- {{ count($cart['products']) }} items in the cart</div>
                    </div>
                    <div id="shipping-summary" class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="h6">SHIPPING:</span>
                            <span id="shipping-method-price" class="h6">€5.00</span>
                        </div>
                        <div id="shipping-method-name" class="text-muted pl-4">-- Courier</div>
                    </div>
                    <div id="payment-summary" class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="h6">PAYMENT:</span>
                            <span id="payment-method-price" class="h6">€1.00</span>
                        </div>
                        <div id="payment-method-name" class="text-muted pl-4">-- CASH</div>
                    </div>
                    <hr class="m-1 pb-2" />
                    <div class="d-flex justify-content-between pt-3">
                        <span class="h6">TOTAL:</span>
                        <span id="order-total-price" class="h6">€{{$cart['total'] + 5 + 1}}</span>
                    </div>
                    <div class="form-check mt-5">
                        <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox"
                            id="terms-conditions" name="terms" />
                        <label class="form-check-label" for="terms-conditions"><small> I have read and accept <a
                                    class="font-weight-bold" href="">Terms & Conditions</a> of the shop </small></label>
                        @error('terms')
                        <span class="invalid-feedback text-left" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('privacy') is-invalid @enderror" type="checkbox"
                            id="privacy-policy" name="privacy" />

                        <label class="form-check-label" for="privacy-policy"><small> I have read and accept <a
                                    class="font-weight-bold" href=""> Privacy Policy</a> of the shop </small></label>
                        @error('privacy')
                        <span class="invalid-feedback text-left" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group mt-5">
                        <label for="register-email">Gift card or discount code</label>
                        <input type="text" class="form-control" id="discount-code" placeholder="XX-XX-XX-XX" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row row-gap justify-content-around mx-2">
            <div class="has-icon-left">
                <i class="fas fa-lg fa-angle-left icon"></i>
                <a href="{{ URL::to('/cart/info') }}" class="btn light-pink-bg">Continue shopping</a>
            </div>
            <div class="has-icon-right {{ isset($cart['products']) ? '' : 'invisible'}}">
                <i class="fas fa-lg fa-angle-right icon"></i>
                <input type="submit" class="btn light-pink-bg" value="Proceed to checkout"></input>
            </div>
        </div>
    </form>
</main>
@endsection