@extends('layout.page')

@section('content')
<main class="content container my-4">
    <!-- Path to product -->
    <nav class="catalogue-breadcrumb">
        <ol class="breadcrumb px-1">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item d-block d-sm-none">...</li>
            @foreach ($product->categories as $category)
            @if ($category->name != 'Sale' && !is_null($category->parent))
            <li class="breadcrumb-item d-none d-sm-block"><a
                    href="/category/{{$category->parent->name}}">{{ $category->parent->name }}</a>
            </li>
            <li class="breadcrumb-item d-none d-sm-block"><a
                    href="/category/{{$category->parent->name}}/{{$category->name}}">{{ $category->name }}</a>
            </li>
            @endif
            @endforeach
            <li class="breadcrumb-item active">{{ $product->brand . ' ' . $product->name }}</li>
        </ol>
    </nav>
    <!-- Product details container -->
    <div class="container flex-fill">
        <!-- Images & Description -->
        <div class="row">
            <div class="col-lg-5 mb-4 mb-lg-0">
                <div class="row">
                    <img id="product-detail-thumb" class="img-fluid"
                        src="{{config('app.image_path').$product->images[0]}}" alt="" />
                </div>
                <div class="row">
                    <div id="product-images" class="px-3 mx-auto owl-carousel">
                        @foreach ($product->images as $image)
                        <div class="item col p-0">
                            <img class="img-fluid" src="{{ config('app.image_path').$image }}" alt="" />
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="pl-0 pl-lg-5 col-lg-7">
                <div class="text-left">
                    <h3>{{ $product->brand }}</h3>
                    <h4>{{ $product->name }}</h4>
                    <h4>{{ $product->weight}}g</h4>
                    <p class="pb-4 pt-2 text-justify">
                        {{ $product->description }}
                    </p>
                    <h6>Ingredients:</h6>
                    <p class="text-justify">{{ $product->ingredients }}</p>
                    <h6>Allergens:</h6>
                    <p class="mb-0 text-justify">Contains:
                        {{ implode(', ',array_column($product->allergens->where('type','contain')->all(),'name')) }}</p>
                    <p class="text-justify">May contain:
                        {{ implode(', ',array_column($product->allergens->where('type','may contain')->all(),'name')) }}
                    </p>
                </div>
                <hr class="m-4" />
                <div class="d-flex justify-content-between">
                    <h3>
                        @if ($product->discount > 0)
                        <s class="text-muted h4">
                            €{{ $product->price_discounted }}</s>
                        @endif
                        €{{$product->price}}
                    </h3>
                    <form class="d-flex" method="GET" action="/cart/add/{{$product->id}}">
                        @csrf
                        <input value="1" min="1" name="quantity" class="form-control quantity-input" type="number" />
                        <input type="submit" value="Add to cart" class="btn btn-outline-dark buy-btn"></input>
                    </form>
                </div>
            </div>
        </div>
        <!-- Additional specification -->
        <div class="row row-gap col-12 my-5 justify-content-around text-left">
            <div class="col-lg-5 border rounded">
                <table class="product-information table table-sm table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Product information</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Catalogue number</td>
                            <td>{{ $product->id }}</td>
                        </tr>
                        <tr>
                            <td>Country of origin</td>
                            <td>{{ $product->origin_country }}</td>
                        </tr>
                        <tr>
                            <td>Brand</td>
                            <td>{{ $product->brand }}</td>
                        </tr>
                        <tr>
                            <td>Weight</td>
                            <td>{{$product->weight}}g</td>
                        </tr>
                        <tr>
                            <td>Dimensions</td>
                            <td>{{ implode(' x ',array_map('trim',explode(',', $product->dimensions))) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-5 border rounded">
                <table class="product-information table table-sm table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Nutrition values</th>
                            <th scope="col">per 100g</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Energy</td>
                            <td>{{ $product->nutrition && $product->nutrition->energy_kj }} kJ /
                                {{ $product->nutrition->energy_kcal }} kcal
                            </td>
                        </tr>
                        <tr>
                            <td>Fat</td>
                            <td>{{ $product->nutrition->fat }} g</td>
                        </tr>
                        <tr>
                            <td class="pl-4">Saturated Fat</td>
                            <td>{{ $product->nutrition->fat_saturated }} g</td>
                        </tr>
                        <tr>
                            <td>Carbohydrates</td>
                            <td>{{ $product->nutrition->carbohydrates }} g</td>
                        </tr>
                        <tr>
                            <td class="pl-4">Sugars</td>
                            <td>{{ $product->nutrition->carbohydrates_sugar }} g</td>
                        </tr>
                        <tr>
                            <td>Protein</td>
                            <td>{{ $product->nutrition->protein }} g</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
</main>
@endsection