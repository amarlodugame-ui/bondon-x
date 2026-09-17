@extends('theme.layouts.frontend')

@push('style')
<link rel="stylesheet" href="{{ asset('assets/theme/css/offers.css') }}">
@endpush

@section('content')
<div class="offers-page">
    <div class="offers-container">
        <section class="offer-hero" id="offerHero">
            @forelse($heroSales as $index => $sale)
                @include('theme.partials.offer-hero-slide', ['sale' => $sale, 'index' => $index])
            @empty
                <div class="offer-hero-empty"><span><i class="fa-solid fa-tags"></i></span><div><strong>নতুন অফার শীঘ্রই আসছে</strong><p>চলমান Flash Sale না থাকলে এখানে কোনো পুরোনো অফার দেখানো হবে না।</p></div><a href="{{ route('products') }}">সব পণ্য দেখুন</a></div>
            @endforelse
            @if($heroSales->count() > 1)
                <button class="offer-hero-arrow prev" type="button" data-hero-prev><i class="fa-solid fa-chevron-left"></i></button>
                <button class="offer-hero-arrow next" type="button" data-hero-next><i class="fa-solid fa-chevron-right"></i></button>
                <div class="offer-hero-dots">@foreach($heroSales as $index => $sale)<button type="button" class="{{ $index === 0 ? 'active' : '' }}" data-hero-dot="{{ $index }}"></button>@endforeach</div>
            @endif
        </section>

        @if($featuredSale)
            @include('theme.partials.offer-featured-deal', ['featuredSale' => $featuredSale])
        @endif

        <section class="offer-section" id="flashSaleSection">
            <div class="offer-section-heading"><div><span class="offer-section-icon flash"><i class="fa-solid fa-bolt"></i></span><span><strong>সব Flash Sale Products</strong><small>শুধু এখন চলমান ফ্ল্যাশ সেল</small></span></div><span class="offer-heading-badge" data-flash-count>{{ $activeFlashSales->count() }} টি অফার</span></div>
            <div class="offer-products-grid">
                @forelse($activeFlashSales as $sale)
                    <div class="offer-live-product" data-live-sale data-end="{{ \Illuminate\Support\Carbon::parse($sale->ends_at)->toIso8601String() }}">@include('theme.partials.product-card', ['product' => $sale->product, 'cardVariant' => 'offer'])</div>
                @empty
                    <div class="offer-empty">এই মুহূর্তে কোনো Flash Sale চলছে না।</div>
                @endforelse
            </div>
        </section>

        <section class="offer-section">
            <div class="offer-section-heading"><div><span class="offer-section-icon discount"><i class="fa-solid fa-percent"></i></span><span><strong>সর্বাধিক Discount Products</strong><small>Regular price-এর তুলনায় সবচেয়ে বেশি ছাড়</small></span></div><a href="{{ route('products') }}">সব দেখুন<i class="fa-solid fa-arrow-right"></i></a></div>
            <div class="offer-products-grid">@forelse($highestDiscountProducts as $product)<div>@include('theme.partials.product-card', ['product' => $product, 'cardVariant' => 'offer'])</div>@empty<div class="offer-empty">Discount product পাওয়া যায়নি।</div>@endforelse</div>
        </section>

        <section class="offer-section">
            <div class="offer-section-heading"><div><span class="offer-section-icon installment"><i class="fa-solid fa-calendar-check"></i></span><span><strong>Installment Offer Products</strong><small>কম ডাউন পেমেন্টে সহজ কিস্তি</small></span></div><a href="{{ route('products', ['installment' => 1]) }}">সব দেখুন<i class="fa-solid fa-arrow-right"></i></a></div>
            <div class="offer-installment-grid">@forelse($installmentProducts as $product)@include('theme.partials.installment-product-card', ['product' => $product])@empty<div class="offer-empty">কিস্তির পণ্য পাওয়া যায়নি।</div>@endforelse</div>
        </section>

        <section class="offer-section offer-coupon-section">
            <div class="offer-section-heading"><div><span class="offer-section-icon coupon"><i class="fa-solid fa-ticket"></i></span><span><strong>Coupon Section</strong><small>চলমান কুপন কপি করে Checkout-এ ব্যবহার করুন</small></span></div></div>
            <div class="offer-coupon-grid">
@forelse($coupons as $coupon)
    <article class="offer-coupon-card {{ $loop->even ? 'gold' : '' }}">
        <span class="offer-coupon-icon">
            <i class="fa-solid {{ $loop->even ? 'fa-crown' : 'fa-tags' }}"></i>
        </span>

        <div>
            <small>কুপন কোড</small>
            <strong>{{ $coupon->code }}</strong>

            <p>
                @if($coupon->discount_type === 'percentage')
                    {{ rtrim(rtrim(number_format($coupon->discount_value, 2), '0'), '.') }}% ছাড়
                @else
                    ৳{{ number_format($coupon->discount_value) }} ছাড়
                @endif

                @if($coupon->minimum_order > 0)
                    <span>• ন্যূনতম ৳{{ number_format($coupon->minimum_order) }}</span>
                @endif
            </p>
        </div>

        <button type="button" data-copy-coupon="{{ $coupon->code }}">
            <i class="fa-regular fa-copy"></i>
            <span>কপি করুন</span>
        </button>
    </article>
@empty
    <div class="offer-empty">এই মুহূর্তে কোনো কুপন নেই।</div>
@endforelse
            </div>
        </section>
    </div>

    <section class="offer-trust-strip">
        <div class="offers-container offer-trust-grid"><div><i class="fa-solid fa-truck-fast"></i><span><strong>দ্রুত ডেলিভারি</strong><small>সারা দেশে</small></span></div><div><i class="fa-solid fa-shield-halved"></i><span><strong>নিরাপদ কেনাকাটা</strong><small>বিশ্বস্ত সেবা</small></span></div><div><i class="fa-solid fa-rotate-left"></i><span><strong>সহজ রিটার্ন</strong><small>নীতিমালা অনুযায়ী</small></span></div><div><i class="fa-solid fa-headset"></i><span><strong>কাস্টমার সাপোর্ট</strong><small>{{ gs()->phone ?: 'সবসময় পাশে' }}</small></span></div></div>
    </section>
</div>
@endsection

@push('script')
<script src="{{ asset('assets/theme/js/offers.js') }}"></script>
@endpush
