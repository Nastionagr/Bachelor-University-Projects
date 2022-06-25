<article
    class="@if(!isset($product) || (isset($product) && !isset($product->carousel))) col-lg-3 col-md-6 @endif @admin() @if(isset($product) && !$product->is_offered) product-disabled @endif @endadmin">
    <div class="card light-pink-bg shadow">
        @if (isset($product))
        <a href="@admin() /admin/edit/{{$product->id}} @endadmin @notadmin() /products/{{ $product->id }} @endnotadmin">
            <img src="{{ config('app.image_path').$product->images[0] }}" alt="Product thumbnail"
                class="card-img m-auto p-3" />
        </a>
        <div class="card-body pt-0 pb-2 px-3">
            <a
                href="@admin() /admin/edit/{{$product->id}} @endadmin @notadmin() /products/{{ $product->id }} @endnotadmin">
                <h6 class="mb-1">{{ $product->brand }}</h6>
                <h6 class="card-description">{{ $product->name }}</h6>
            </a>
            <div class="mt-1 d-flex justify-content-between align-items-center">
                @notadmin()
                <a class="my-auto" href="{{ URL::to('/cart/add/'.$product->id) }}">
                    <i class="fas fa-shopping-cart font-25 dark-red"></i>
                </a>
                @endnotadmin
                <span>
                    @if ($product->discount > 0)
                    @notadmin()
                    <s class="dark-pink">€{{ $product->price }}</s>
                    @endnotadmin
                    <strong class="font-10 dark-red">€{{ $product->price_discounted}}</strong>
                    @else
                    <strong class="font-10 dark-red">€{{ $product->price}}</strong>
                    @endif
                </span>
                @admin()
                <span>
                    <a href="/admin/edit/{{$product->id}}">
                        <i class="fas fa-edit font-25 dark-red"></i>
                    </a>
                    <a href="#modal-delete" class="trigger-btn" data-toggle="modal"
                        onclick="$('#modal-delete').attr('action','/admin/delete/'+{{ $product->id }})">
                        <i class="fas fa-trash-alt font-25 dark-red"></i>
                    </a>
                </span>
                @endadmin
            </div>
        </div>

        @if ( $product->discount > 0)
        <div class="product-badge">-{{$product->discount}}%</div>
        @endif
        @else
        <a href="/admin/new">
            <div class="card-img p-3 text-center">
                <i class="fas fa-plus-circle fa-5x dark-red" style="line-height:2em"></i>
            </div>
            <div class="card-body text-center">
                <h5 class="dark-red">Add a new product</h5>
            </div>
        </a>
        @endif
    </div>
</article>