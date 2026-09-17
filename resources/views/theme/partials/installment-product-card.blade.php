@php
    $imagePath = 'assets/theme/images/products/'.$product->image;
    $imageUrl = $product->image && is_file(public_path($imagePath)) ? asset($imagePath) : null;
@endphp
<a class="offer-installment-card" href="{{ route('product.show', $product->slug) }}">
    <div class="offer-installment-image">@if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $product->name }}">@else<span><i class="fa-solid fa-box-open"></i></span>@endif</div>
    <div class="offer-installment-info"><small>{{ $product->brand?->name ?: $product->category?->name }}</small><strong>{{ $product->name }}</strong><div class="offer-installment-numbers"><span><small>ডাউন পেমেন্ট</small><b>৳{{ number_format($product->offer_plan_down_payment) }}</b></span><span><small>প্রতি কিস্তি</small><b>৳{{ number_format($product->offer_plan_amount) }}</b></span></div>@if($product->offer_plan_count)<em>{{ $product->offer_plan_count }} কিস্তি</em>@endif</div>
</a>
