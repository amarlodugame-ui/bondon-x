@pushOnce('style', 'catalog-product-card-style')
    <link rel="stylesheet" href="{{ asset('assets/theme/css/product-card.css') }}">
@endPushOnce
@php
    $sale = $product->relationLoaded('flashSale') ? $product->flashSale : null;
    $price = (float) ($sale?->sale_price ?? $product->price);
    $oldPrice = (float) ($sale ? $product->price : $product->old_price);
    $discount = $oldPrice > $price ? round(($oldPrice - $price) / $oldPrice * 100) : 0;
    $rating = round((float) ($product->approved_reviews_avg_rating ?? 0), 1);
    $reviews = (int) ($product->approved_reviews_count ?? 0);
    $imagePath = 'assets/theme/images/products/' . $product->image;
    $imageUrl = $product->image && is_file(public_path($imagePath)) ? asset($imagePath) : null;
    $specs = $product->relationLoaded('variants') ? $product->variants->first()?->attributeValues
        ->filter(fn ($value) => $value->status && $value->attribute?->status)
        ->take(3)->pluck('value')->implode('  |  ') : null;
@endphp
<article class="catalog-card" data-product-id="{{ $product->id }}">
    <div class="catalog-card-top">
        @if($discount > 0)<span class="catalog-discount">{{ $discount }}% ছাড়</span>@endif
        <button type="button" class="catalog-save" data-save="{{ $product->id }}" aria-pressed="false" aria-label="{{ $product->name }} পছন্দের তালিকায় রাখুন" title="এই ব্রাউজারে পছন্দের তালিকায় রাখুন"><i class="fa-regular fa-heart" aria-hidden="true"></i></button>
    </div>
    <a class="catalog-image" href="{{ route('product.show', $product->slug) }}" aria-label="{{ $product->name }} বিস্তারিত দেখুন">
        @if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy" decoding="async" width="220" height="180">
        @else<span class="catalog-placeholder"><i class="fa-solid fa-box-open" aria-hidden="true"></i><span>ছবি আসছে</span></span>@endif
    </a>
    <div class="catalog-card-body">
        <a class="catalog-name" href="{{ route('product.show', $product->slug) }}" title="{{ $product->name }}">{{ $product->name }}</a>
        <p class="catalog-specs" title="{{ $specs ?: $product->brand?->name }}">{{ $specs ?: ($product->brand?->name ?: $product->category?->name) }}</p>
        <div class="catalog-price"><strong>৳ {{ number_format($price) }}</strong>@if($oldPrice > $price)<del>৳ {{ number_format($oldPrice) }}</del>@endif</div>
        <div class="catalog-card-meta"><span class="catalog-rating" aria-label="রেটিং {{ $rating }}, {{ $reviews }}টি রিভিউ"><i class="fa-solid fa-star" aria-hidden="true"></i> {{ number_format($rating, 1) }} <span>({{ $reviews }})</span></span><span class="catalog-stock {{ $product->stock > 0 ? '' : 'is-unavailable' }}"><span aria-hidden="true">●</span> {{ $product->stock > 0 ? 'স্টকে আছে' : 'স্টক নেই' }}</span></div>
        <a class="catalog-action" href="{{ route('product.show', $product->slug) }}"><i class="fa-solid fa-eye" aria-hidden="true"></i><span>বিস্তারিত দেখুন</span></a>
    </div>
</article>
