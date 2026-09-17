@php
    $product = $sale->product;
    $salePrice = (float) $sale->sale_price;
    $basePrice = (float) $product->price;
    $discount = $basePrice > $salePrice ? round((($basePrice - $salePrice) / $basePrice) * 100) : 0;
    $imagePath = $sale->hero_image ?: $product->image;
    $imageUrl = null;
    if ($imagePath) {
        $candidate = str_contains($imagePath, '/') ? $imagePath : 'assets/theme/images/products/'.$imagePath;
        if (is_file(public_path($candidate))) $imageUrl = asset($candidate);
    }
    $title = $sale->hero_title ?: $product->name.' এখন বিশেষ দামে';
    $subtitle = $sale->hero_subtitle ?: $discount.'% ছাড়ে সীমিত সময়ের জন্য '.$product->name.' অর্ডার করুন।';
@endphp
<article class="offer-hero-slide {{ $index === 0 ? 'active' : '' }}" data-offer-slide data-sale-id="{{ $sale->id }}" data-end="{{ \Illuminate\Support\Carbon::parse($sale->ends_at)->toIso8601String() }}">
    <div class="offer-hero-copy">
        <span class="offer-hero-kicker"><i class="fa-solid fa-bolt"></i>{{ $sale->hero_kicker ?: 'FLASH SALE' }}</span>
        <h1>{{ $title }}</h1>
        <p>{{ $subtitle }}</p>
        <div class="offer-hero-trust">
            <span><i class="fa-solid fa-truck-fast"></i>দ্রুত ডেলিভারি</span>
            <span><i class="fa-solid fa-shield-halved"></i>নিরাপদ কেনাকাটা</span>
            <span><i class="fa-solid fa-headset"></i>বিশ্বস্ত সাপোর্ট</span>
        </div>
    </div>
    <div class="offer-hero-visual">
        <div class="offer-hero-glow"></div>
        <div class="offer-hero-image">
            @if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $product->name }}">@else<div class="offer-hero-placeholder"><i class="fa-solid fa-box-open"></i></div>@endif
        </div>
        @if($discount > 0)<div class="offer-hero-discount"><small>সাশ্রয়</small><strong>{{ $discount }}%</strong><span>ছাড়</span></div>@endif
    </div>
    <div class="offer-hero-panel">
        <span class="offer-hero-panel-title">অফার শেষ হতে বাকি</span>
        <div class="offer-countdown">
            <div><strong data-days>00</strong><small>দিন</small></div>
            <div><strong data-hours>00</strong><small>ঘণ্টা</small></div>
            <div><strong data-minutes>00</strong><small>মিনিট</small></div>
            <div><strong data-seconds>00</strong><small>সেকেন্ড</small></div>
        </div>
        <div class="offer-hero-price"><small>ফ্ল্যাশ মূল্য</small><strong>৳{{ number_format($salePrice) }}</strong>@if($basePrice > $salePrice)<del>৳{{ number_format($basePrice) }}</del>@endif</div>
        <a href="{{ route('product.show', $product->slug) }}">{{ $sale->hero_button_text ?: 'এখনই দেখুন' }}<i class="fa-solid fa-arrow-right"></i></a>
    </div>
</article>
