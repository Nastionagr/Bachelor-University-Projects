<header class="header-nav container-fluid">
    <nav id="navbar-main" class="navbar navbar-expand-md justify-content-between row dark-pink-bg">
        <div class="col-md-3 col-6 p-0 d-flex justify-content-start">
            <a href="@admin() /admin @endadmin @notadmin() / @endnotadmin">
                <img class="h-100" src="{{ asset('img/logo150x60.png') }}" alt="" />
            </a>
        </div>
        <form @admin() action="/admin" @endadmin @notadmin() action="/products" @endnotadmin method="GET"
            class="col-md-6 p-0 input-group d-none d-md-flex justify-content-center">
            <input class="header-searchbar form-control" type="text" name="search" placeholder="Search products..."
                value="{{ request()->get('search') ? request()->get('search') : '' }}" />
            <div class="input-group-append">
                <button class="btn light-pink-bg">
                    <i class="fas fa-search dark-pink"></i>
                </button>
            </div>
        </form>
        <div class="col-md-3 col-6 p-0 d-flex justify-content-end align-items-center">
            @auth
            <div class="header-link mx-sm-2 mx-1 my-0">
                <a href="/logout">
                    <i class="fas fa-sign-out-alt fa-2x light-pink"></i>
                </a>
                <span
                    class="h6 m-0 light-pink d-none d-sm-block">{{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}</span>
            </div>
            @endauth
            @guest
            <div class="header-link mx-sm-2 mx-1 my-0">
                <a href="/login">
                    <i class="fas fa-user-circle fa-2x light-pink"></i>
                </a>
            </div>
            @endguest
            <div class="header-link mx-sm-2 mx-1 my-0">
                <a href="/survey">
                    <i class="fas fa-question-circle fa-2x light-pink"></i>
                </a>
            </div>
            @notadmin()
            <div class="header-link mx-sm-2 mx-1 my-0">
                <a href="{{ URL::to('/cart') }}">
                    <i class="fas fa-shopping-cart fa-2x light-pink"></i>
                    @auth
                    @if (auth()->user()->cart_products()->whereNull('order_id')->count() > 0)
                    <span
                        id="cart-items-count">{{ auth()->user()->cart_products()->whereNull('order_id')->count() }}</span>
                    @endif
                    @endauth
                    @guest
                    @if (session()->has('cart'))
                    <span id="cart-items-count">{{ count(session()->get('cart')['products']) }}</span>
                    @endif
                    @endguest
                </a>
            </div>
            <button class="navbar-toggler ml-sm-3 ml-1 my-0" type="button" data-toggle="collapse"
                data-target="#navbar-mobile">
                <i class="fas fa-bars fa-lg light-pink"></i>
            </button>
            @endadmin
        </div>
    </nav>

    @notadmin
    <!-- Desktop Nav -->
    <nav id="header-menu" class="navbar d-none d-md-flex row border-bottom light-pink-bg p-0">
        <ul>
            @foreach ($categories as $category)
            <li class="@if (count($category['children'])>0) dropdown @endif d-flex align-items-center">
                <a href="/category/{{strtolower($category->url_name)}}">{{ $category->name }}</a>
                @if (count($category['children'])>0)
                <ul class="dropdown-menu light-pink-bg">
                    @foreach ($category['children'] as $subcategory)
                    <li class="dropdown-item"><a
                            href="/category/{{strtolower($category->url_name)}}/{{strtolower($subcategory->url_name)}}">{{ $subcategory->name }}</a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </li>
            @endforeach
        </ul>
    </nav>

    <!-- Mobile Nav -->
    <nav id="navbar-mobile" class="collapse d-md-none text-left light-pink-bg row p-2">
        <ul class="navbar-nav w-100">
            @foreach ($categories as $category)
            <li class="nav-item">
                <a href="/category/{{strtolower($category->name)}}">{{ $category->name }}</a>
                @if (count($category['children'])>0)
                <i class="fas fa-chevron-down float-right"></i>
                <ul class="navbar-nav collapse pl-3">
                    @foreach ($category['children'] as $subcategory)
                    <li class="nav-item"><a
                            href="/category/{{strtolower($category->name)}}/{{strtolower($subcategory->name)}}">{{ $subcategory->name }}</a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </li>
            @endforeach
            <li class="nav-item mt-3">
                <div class="input-group p-0  w-100">
                    <input class="form-control" type="text" placeholder="Search products..." />
                    <div class="input-group-append">
                        <button class="btn bg-white border">
                            <i class="fas fa-search dark-pink"></i>
                        </button>
                    </div>
                </div>
            </li>
        </ul>
    </nav>
    @endnotadmin
</header>