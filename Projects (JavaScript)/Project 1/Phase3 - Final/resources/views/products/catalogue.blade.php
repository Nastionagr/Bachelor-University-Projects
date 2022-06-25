@extends('layout.page')

@section('content')
<main class="content container my-4">
    <div class="row flex-fill text-left">
        <!-- Filter sidebar -->
        <section id="filter" class="col-lg-3 col-md-4 mt-4 mb-md-0 mb-3">
            <h4 class="mb-4 pb-2 border-bottom">Filter</h4>
            <form id="filter-form" action="">
                @foreach ($filter['items'] as $filter_name)
                <div class="mb-3 card">
                    <header class="card-header sidebar-filter-header d-flex justify-content-between">
                        <h5 class="m-0">{{ $filter_name['fullname'] }}</h5>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </header>
                    <ul class="card-body sidebar-filter-item-container mb-1">
                        @foreach ($filter_name['data'] as $index=>$brand)
                        <li class="form-check my-2">
                            <input class="form-check-input" type="checkbox" name="{{$filter_name['name']}}"
                                value="{{$brand}}" id="{{$filter_name['name']}}-{{$index}}"
                                onChange="$('#filter-form').submit();"
                                {{ in_array($brand,explode(',',request()->get($filter_name['name'])) ?? []) ? 'checked' : ''}} />
                            <label class="form-check-label"
                                for="{{$filter_name['name']}}-{{$index}}">{{ $brand }}</label>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
                <div class="mb-3 card">
                    <header class="card-header sidebar-filter-header d-flex justify-content-between">
                        <h5 class="m-0">Price</h5>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </header>
                    <div class="card-body px-3 mb-1">
                        <div class="d-flex justify-content-center mb-4">
                            <div class="price-min col p-0 d-flex position-relative">
                                <input id="input-price-min" name="price-min"
                                    initial-value="{{ $filter['filter_price_min'] }}"
                                    value="{{ is_null(request()->get('price-min')) ? $filter['filter_price_min'] : request()->get('price-min') }}"
                                    class="w-100 px-2" type="text" />
                                <span>€</span>
                            </div>
                            <span class="mx-3">-</span>
                            <div class="price-max col p-0 d-flex position-relative">
                                <input id="input-price-max" name="price-max"
                                    initial-value="{{ $filter['filter_price_max'] }}"
                                    value="{{ is_null(request()->get('price-max')) ? $filter['filter_price_max'] : request()->get('price-max') }}"
                                    class="w-100 px-2" type="text" />
                                <span>€</span>
                            </div>
                        </div>
                        <div class="sidebar-filter-item mx-3 mb-4 py-2">
                            <div id="sidebar-price-slider"></div>
                        </div>
                        <button type="submit" class="btn d-block mx-auto light-pink-bg">Set price</button>
                    </div>
                </div>
            </form>
        </section>
        <!-- Catalogue of products -->
        <section class="col-lg-9 col-md-8">
            <h4 class="d-md-none mb-4 pb-2 border-bottom">Products</h4>
            @if (isset($category))
            <nav class="catalogue-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    @if (isset($category->parent))
                    <li class="breadcrumb-item"><a
                            href="/category/{{ $category->parent->url_name }}">{{ $category->parent->name }}</a></li>
                    @endif
                    <li class="breadcrumb-item active">{{ $category->name }}</li>
                </ol>
            </nav>
            <div class="catalogue-header">
                <h4>{{ $category->name }}</h4>
                <p>{{ $category->description }}</p>
            </div>
            @endif
            <div class="catalogue-info row row-gap mb-4">
                <div class="col-sm-6 h5 my-auto">{{ $products->total() }} Product(s)</div>
                <div class="col-sm-6 d-flex justify-content-start justify-content-sm-end">
                    @include('products.sort')
                </div>
            </div>
            <div class="catalogue-products container pt-2">
                <div class="row row-gap mb-4">
                    @each('products.productCard', $products, 'product')
                </div>
            </div>
            <div class="catalogue-pagination mt-5">
                <nav class="d-flex justify-content-center">
                    {!! $products->appends(request()->input())->links() !!}
                </nav>
            </div>
        </section>
    </div>
</main>
@endsection