@extends('layout.page')

@section('content')
<main class="content container">
    <!-- Carousel for trailer images -->
    <div id="carousel-trailer" class="carousel slide my-4" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carousel-trailer" data-slide-to="0" class="active"></li>
            <li data-target="#carousel-trailer" data-slide-to="1"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <picture>
                    <source media="(min-width:1200px)" srcset="/img/carousel_1_1200w.png" />
                    <source media="(min-width:768px)" srcset="/img/carousel_1_800w.png" />
                    <img class="img-fluid" src="/img/carousel_1_350w.png" alt="Halloween Promo" />
                </picture>
            </div>
            <div class="carousel-item">
                <picture>
                    <source media="(min-width:1200px)" srcset="/img/carousel_2_1200w.png" />
                    <source media="(min-width:768px)" srcset="/img/carousel_2_800w.png" />
                    <img class="img-fluid" src="/img/carousel_2_350w.png" alt="Premium Chocolate Promo" />
                </picture>
            </div>
        </div>
        <a class="carousel-control-prev my-auto mx-1" href="#carousel-trailer" role="button" data-slide="prev">
            <i class="fa fa-angle-left text-dark fa-2x"></i>
        </a>
        <a class="carousel-control-next my-auto mx-1" href="#carousel-trailer" role="button" data-slide="next">
            <i class="fa fa-angle-right text-dark fa-2x"></i>
        </a>
    </div>

    <!-- New Arrivals & Ads Section -->
    <div class="row my-3">
        <!-- New Arrivals & Register Promo Ad Container -->
        <div class="col-lg-9 col-12">
            <!-- New Arrivals -->
            <div id="new-arrivals" class="my-3 p-4 rounded-5">
                <h4 class="mx-auto mb-4 text-white">New Arrivals</h4>
                <div class="row row-gap text-left">
                    @each('products.productCard', $products_new, 'product')
                </div>
            </div>
            <!-- Register Promo Ad -->
            <div class="mt-4">
                <a href="/register">
                    <img class="d-block w-100" src="/img/register.png" alt="" />
                </a>
            </div>
        </div>
        <!-- Free Shipping Promo Ad -->
        <div class="col-lg-3">
            <a class="mt-auto ml-auto" href="/products">
                <img class="h-100 w-100" src="/img/banner.png" alt="" />
            </a>
        </div>
    </div>

    <!-- Personal recommendation -->
    <div class="d-block my-4 p-4 border">
        <h4 class="dark-pink bg-white">Recommended for you</h4>
        <div id="recommendation" class="col-sm-12 col-10 px-0 mx-auto owl-carousel text-left">
            @foreach ($products_recommend as $product)
            @php
            $product->carousel = true
            @endphp
            <div class="col item">
                @include('products.productCard', $product)
            </div>
            @endforeach
        </div>
    </div>
</main>
@endsection