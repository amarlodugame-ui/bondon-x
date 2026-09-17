@php
    $product = $featuredSale->product;
    $salePrice = (float) $featuredSale->sale_price;
    $basePrice = (float) $product->price;
    $discount = $basePrice > $salePrice ? round((($basePrice - $salePrice) / $basePrice) * 100) : 0;
    $rating = round((float) ($product->approved_reviews_avg_rating ?? 0), 1);
    $reviews = (int) ($product->approved_reviews_count ?? 0);
    $sold = (int) ($product->order_items_sum_quantity ?? 0);
    $imagePath = 'assets/theme/images/products/'.$product->image;
    $imageUrl = $product->image && is_file(public_path($imagePath)) ? asset($imagePath) : null;
@endphp
<section class="offer-featured" data-live-sale data-end="{{ \Illuminate\Support\Carbon::parse($featuredSale->ends_at)->toIso8601String() }}">
    <div class="offer-section-heading">
        <div><span class="offer-section-icon featured"><i class="fa-solid fa-fire"></i></span><span><strong>আজকের Featured Deal</strong><small>চলমান ফ্ল্যাশ সেলের সেরা সাশ্রয়</small></span></div>
        @if($discount)<span class="offer-heading-badge">{{ $discount }}% ছাড়</span>@endif
    </div>
    <div class="offer-featured-card">
        <a class="offer-featured-image" href="{{ route('product.show', $product->slug) }}">
            @if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $product->name }}">@else<span><i class="fa-solid fa-box-open"></i><small>ছবি আসছে</small></span>@endif
        </a>
        <div class="offer-featured-info">
            <span class="offer-deal-label"><i class="fa-solid fa-bolt"></i>আজকের বিশেষ ডিল</span>
            <h2>{{ $product->name }}</h2>
            <p>{{ $product->brand?->name ?: 'বন্ধন গ্রুপ' }}@if($product->category) <span>•</span> {{ $product->category->name }}@endif</p>
            <div class="offer-feature-pills"><span><i class="fa-solid fa-certificate"></i>অরিজিনাল পণ্য</span><span><i class="fa-solid fa-truck-fast"></i>দ্রুত ডেলিভারি</span><span><i class="fa-solid fa-rotate-left"></i>সহজ রিটার্ন</span></div>
            <div class="offer-feature-rating"><span><i class="fa-solid fa-star"></i>{{ number_format($rating,1) }} <small>({{ $reviews }})</small></span><span class="stock {{ $product->stock > 0 ? '' : 'out' }}"><i class="fa-solid fa-circle"></i>{{ $product->stock > 0 ? 'স্টকে আছে' : 'স্টক নেই' }}</span></div>
        </div>
        <div class="offer-featured-buy">
            @if($discount)<span class="offer-save-badge">আপনি সাশ্রয় করছেন {{ $discount }}%</span>@endif
            <div class="offer-feature-price"><strong>৳{{ number_format($salePrice) }}</strong>@if($basePrice > $salePrice)<del>৳{{ number_format($basePrice) }}</del>@endif</div>
            <div class="offer-sale-progress"><span style="width:{{ min(100, max(10, $sold)) }}%"></span></div>
            <small>{{ $sold }} টি বিক্রি হয়েছে</small>
            <a href="{{ route('product.show', $product->slug) }}"><i class="fa-solid fa-eye"></i>বিস্তারিত দেখুন</a>
        </div>
    </div>
</section>
