@extends('layout.page')

@error('firstName') is-invalid @enderror
@error('firstName')
<span class="invalid-feedback" role="alert">
    <strong>{{ $message }}</strong>
</span>
@enderror

@section('content')
<main class="content container my-4">
    <!-- Product details container -->
    <form class="container flex-fill" action="{{ isset($product) ? '/admin/edit/'.$product->id : '/admin/new' }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row @if (isset($product)) justify-content-between @else justify-content-end @endif">
            @isset($product)
            <h5 class="px-3 my-auto">Product #{{ $product->id }} <span class="text-muted">( Sold:
                    {{$product->sold}} )</span></h5>
            @endisset
            <input type="submit" value="Save" class="btn dark-red-bg text-white">
        </div>
        <!-- Images & Description -->
        <div class="row">
            <div class="col-lg-5 mb-4 mb-lg-0">
                <div class="row">
                    <img id="product-detail-thumb" class="img-fluid"
                        src="{{ isset($product) ? config('app.image_path').$product->images[0] : ''}}" alt="" />
                    <i id="delete-icon" class="fas fa-times-circle dark-red pointer"></i>
                </div>
                <div class="row">
                    <div id="product-images" class="px-3 mx-auto owl-carousel">
                        @isset($product)
                        @foreach ($product->images as $image)
                        <div class="item col p-0">
                            <input type="hidden" name="images[]" value="{{$image}}" />
                            <img class="img-fluid" src="{{ config('app.image_path').$image }}" alt="" />
                        </div>
                        @endforeach
                        @endisset
                    </div>
                </div>
                <div class="row my-2">
                    @if (isset($product))
                    <h6 value="{{ count($product->images) }}" id="product-image-count" class="col">
                        {{ count($product->images) }} image(s)</h6>
                    @else
                    <h6 value="0" id="product-image-count" class="col">
                        0 image(s)</h6>
                    @endif
                </div>
                <div class="row">
                    <div class="custom-file">
                        <input type="file" name="images_new[]"
                            class="custom-file-input @error('images') is-invalid @enderror" id="images" multiple
                            accept="image/*">
                        @error('images')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <label class="custom-file-label" for="images">Choose image(s)</label>
                    </div>
                </div>
            </div>
            <div class="pl-0 pl-lg-5 col-lg-7 text-left">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="product-brand">Brand</label>
                        <input type="text" class="form-control @error('brand') is-invalid @enderror" id="product-brand"
                            name="brand" value="{{ isset($product) ? $product->brand : old('brand') }}">
                        @error('brand')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group col-md-8">
                        <label for="product-name">Product name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="product-name"
                            name="name" value="{{ isset($product) ? $product->name : old('name')}}">
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="product-desc">Description</label>
                    <textarea name="description" id="product-desc"
                        class="form-control @error('description') is-invalid @enderror" rows="4"
                        placeholder="Write some details about the product here.">{{ isset($product) ? $product->description : old('description') }}</textarea>
                    @error('description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="product-ingredients">Ingredients</label>
                    <textarea name="ingredients" id="product-ingredients"
                        class="form-control my-auto @error('ingredients') is-invalid @enderror" rows=3
                        placeholder="Enter ingredients delimited by comma.">{{ isset($product) ? $product->ingredients : old('ingredients') }}</textarea>
                    @error('ingredients')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="product-allergens">Allergens</label>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <small class="form-text text-muted">Enter allergens that the
                                product
                                <strong>contains:</strong></small>
                            <textarea name="allergens-contain" id="product-allergens"
                                class="form-control @error('allergens-contain') is-invalid @enderror" rows="3"
                                placeholder="e.g. Soybeans, Cocoa, Milk">{{ isset($product) ? implode(', ',array_column($product->allergens->where('type','contain')->all(),'name')) : old('allergens-contain') }}</textarea>
                            @error('allergens-contain')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <small class="form-text text-muted">Enter allergens that the
                                product
                                <strong>may contain:</strong></small>
                            <textarea name="allergens-may" id="product-allergens"
                                class="form-control @error('allergens-may') is-invalid @enderror" rows="3"
                                placeholder="e.g. Coconut, Cinnamon, Hazelnuts, Pistachio">{{ isset($product) ? implode(', ',array_column($product->allergens->where('type','may contain')->all(),'name')) : old('allergens-may') }}</textarea>
                            @error('allergens-may')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md">
                        <label for="product-country">Origin country</label>
                        <input type="text" class="form-control @error('country') is-invalid @enderror"
                            id="product-country" name="country"
                            value="{{ isset($product) ? $product->origin_country : old('country') }}">
                        @error('country')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label for="product-weight">Weight (g)</label>
                        <input type="number" min="1" class="form-control @error('weight') is-invalid @enderror"
                            id="product-weight" name="weight"
                            value="{{ isset($product) ? $product->weight : old('weight') }}">
                        @error('weight')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group col-md">
                        <label>Dimensions L x W x H (cm)</label>
                        <div class="d-flex justify-content-center align-items-center">
                            <input type="number" min="0"
                                class="form-control p-1 mr-1 @error('dim') is-invalid @enderror"
                                id="product-dimension-length"
                                value="{{ isset($product) ? trim(explode(',', $product->dimensions)[0]) : old('dim.0') }}"
                                name="dim[]">
                            <span class="px-1">x</span>
                            <input type="number" min="0"
                                class="form-control p-1 mr-1 @error('dim') is-invalid @enderror"
                                id="product-dimension-width"
                                value="{{ isset($product) ? trim(explode(',', $product->dimensions)[1]) : old('dim.1') }}"
                                name="dim[]">
                            <span class="px-1">x</span>
                            <input type="number" min="0" class="form-control p-1 @error('dim') is-invalid @enderror"
                                id="product-dimension-height"
                                value="{{ isset($product) ? trim(explode(',', $product->dimensions)[2]) : old('dim.2') }}"
                                name="dim[]">
                        </div>
                        @error('dim')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="form-row justify-content-between">
                    <div class="form-group col-md-4">
                        <label for="product-price">Price (€)</label>
                        <input type="number" class="form-control @error('price') is-invalid @enderror"
                            id="product-price" min="0.01" step="0.01"
                            value="{{ isset($product) ? $product->price : old('price') }}" name="price">
                        @error('price')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input @error('is_discount') is-invalid @enderror" type="checkbox"
                                id="product-is-discount" name="is_discount"
                                {{ isset($product) ? ($product->discount > 0 ? 'checked':'') : (old('agreement') == 'on' ? 'checked' : '') }}
                                onchange="$('#product-discount, #product-price-discount').prop('disabled', function(i, v) { return !v; });">
                            <label class="form-check-label" for="product-is-discount">
                                Discount (%)
                            </label>
                            @error('is_discount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <input type="number" class="form-control  @error('discount') is-invalid @enderror"
                            id="product-discount" name="discount"
                            value="{{ isset($product) ? ( $product->discount > 0 ? $product->discount : '') : (old('is_discount') ? old('discount') : '') }}"
                            {{ isset($product) ? ( $product->discount > 0 ? '' : 'disabled') : (old('is_discount') ? '' : 'disabled') }}
                            min="0" max="100"
                            onchange="$('#product-price-discount').val(($('#product-price').val() - $('#product-price').val()*($('#product-discount').val() / 100.0)).toFixed(2) )">
                        @error('discount')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="product-price-discount">Discounted price (€)</label>
                        <input type="number" class="form-control" id="product-price-discount" min="0.01" step="0.01"
                            name="price-discounted"
                            value="{{ isset($product) ? ( $product->discount > 0 ? $product->price_discounted : '') : (old('is_discount') ? old('price-discounted') : '') }}"
                            {{ isset($product) ? ( $product->discount > 0 ? '' : 'disabled') : (old('is_discount') ? '' : 'disabled') }}
                            onchange="$('#product-discount').val((($('#product-price').val() - $('#product-price-discount').val())/$('#product-price').val() * 100).toFixed(0) )">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md">
                        <label for="product-category">Category</label>
                        <select class="custom-select mr-sm-2 @error('category') is-invalid @enderror"
                            id="product-category" name="category">
                            @foreach ($categories as $category)
                            @if (count($category['children'])>0)
                            <optgroup label="{{ $category->name }}">
                                @foreach ($category['children'] as $subcategory)
                                <option value="{{$subcategory->id}}">{{$subcategory->name}}</option>
                                @endforeach
                            </optgroup>
                            @endif
                            @endforeach
                        </select>
                        @error('category')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="product-quantity">Stock</label>
                        <input type="number" class="form-control @error('stock') is-invalid @enderror"
                            id="product-quantity" min="0" name="stock"
                            value="{{ isset($product) ? $product->quantity : old('stock') }}">
                        @error('stock')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label for="product-is-offered">
                            Visibility
                        </label>
                        <select class="custom-select mr-sm-2 @error('offered') is-invalid @enderror"
                            id="product-offered" name="offered">
                            <option value="false"
                                {{ isset($product) ? ($product->is_offered == 0 ? 'selected' : '') : (old('offered') == 'false' ? 'selected':'') }}>
                                Private
                            </option>
                            <option value="true"
                                {{ isset($product) ? ($product->is_offered == 1 ? 'selected' : '') : (old('offered') == 'true' ? 'selected':'') }}>
                                Public
                            </option>
                        </select>
                        @error('offered')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <!-- Additional specification -->
        <div class="d-flex text-left justify-content-center mt-3">
            <div class="col-lg-6 border rounded">
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
                            <td><input class="form-control  @error('nutrition-kj') is-invalid @enderror" type="number"
                                    min="0" name="nutrition-kj"
                                    value="{{ isset($product) && $product->nutrition ? $product->nutrition->energy_kj : old('nutrition-kj') }}"></input><span>
                                    kJ / </span>
                                <input class="form-control  @error('nutrition-kcal') is-invalid @enderror" type="number"
                                    min="0" name="nutrition-kcal"
                                    value="{{ isset($product) && $product->nutrition ? $product->nutrition->energy_kcal : old('nutrition-kcal') }}"></input>
                                <span>kcal</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Fat</td>
                            <td><input class="form-control  @error('nutrition-fat') is-invalid @enderror" type="number"
                                    min="0" step="0.01" name="nutrition-fat"
                                    value="{{ isset($product) && $product->nutrition ? $product->nutrition->fat : old('nutrition-fat') }}"></input><span>
                                    g</span></td>
                        </tr>
                        <tr>
                            <td class="pl-4">Saturated Fat</td>
                            <td><input class="form-control @error('nutrition-fat-saturated') is-invalid @enderror"
                                    type="number" min="0" step="0.01" name="nutrition-fat-saturated"
                                    value="{{ isset($product) && $product->nutrition ? $product->nutrition->fat_saturated : old('nutrition-fat-saturated')}}"></input><span>
                                    g</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Carbohydrates</td>
                            <td><input class="form-control @error('nutrition-carbohydrates') is-invalid @enderror"
                                    type="number" min="0" step="0.01" name="nutrition-carbohydrates"
                                    value="{{ isset($product) && $product->nutrition ? $product->nutrition->carbohydrates : old('nutrition-carbohydrates') }}"></input><span>
                                    g</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-4">Sugars</td>
                            <td><input class="form-control @error('nutrition-carbohydrates-sugar') is-invalid @enderror"
                                    type="number" min="0" step="0.01" name="nutrition-carbohydrates-sugar"
                                    value="{{ isset($product) && $product->nutrition ? $product->nutrition->carbohydrates_sugar : old('nutrition-carbohydrates-sugar') }}"></input><span>
                                    g</span></td>
                        </tr>
                        <tr>
                            <td>Protein</td>
                            <td><input class="form-control @error('nutrition-protein') is-invalid @enderror"
                                    type="number" min="0" step="0.01" name="nutrition-protein"
                                    value="{{ isset($product) && $product->nutrition ? $product->nutrition->protein : old('nutrition-protein') }}"></input><span>
                                    g</span></td>
                        </tr>
                    </tbody>
                </table>`
            </div>
        </div>
    </form>
</main>
@endsection