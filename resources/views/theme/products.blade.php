@extends('theme.layouts.frontend')
@section('content')
<section class="catalog" id="catalog" aria-label="পণ্য তালিকা">
    <nav class="catalog-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home') }}" aria-label="হোম"><i class="fa-solid fa-house" aria-hidden="true"></i></a><span>/</span><a href="{{ route('categories') }}">ক্যাটাগরি</a><span>/</span><span id="catalog-crumb">{{ $pageTitle }}</span></nav>
    <form id="catalog-form" action="{{ route('products') }}" method="get" data-endpoint="{{ route('products.results') }}">
        @if($currentType)<input type="hidden" name="type" value="{{ $currentType }}">@endif
        <div class="catalog-layout">
            <aside class="catalog-sidebar" id="catalog-filters" aria-label="পণ্য ফিল্টার">
                <div class="catalog-filter-heading"><strong><i class="fa-solid fa-filter" aria-hidden="true"></i> ফিল্টার করুন</strong><button type="button" data-reset>সব রিসেট</button><button type="button" class="catalog-close-filters" aria-label="ফিল্টার বন্ধ করুন">×</button></div>
                <div class="catalog-filter-status" role="status" aria-live="polite" hidden></div>
                <div class="catalog-filter-section">
                    <label class="catalog-label" for="catalog-max-range">দাম (৳)</label>
                    <div class="catalog-range"><input type="range" id="catalog-max-range" min="0" max="{{ $priceCeiling }}" step="1" value="{{ request('max_price', $priceCeiling) }}" aria-label="সর্বোচ্চ দাম"></div>
                    <div class="catalog-price-inputs"><label><span>৳</span><input type="number" name="min_price" min="0" max="999999999" step="any" value="{{ request('min_price') }}" placeholder="0" aria-label="সর্বনিম্ন দাম"></label><label><span>৳</span><input type="number" name="max_price" min="0" max="999999999" step="any" value="{{ request('max_price') }}" placeholder="{{ number_format($priceCeiling) }}" aria-label="সর্বোচ্চ দাম"></label></div>
                    <button type="submit" class="catalog-apply">প্রয়োগ করুন</button>
                </div>
                <div class="catalog-filter-section"><label class="catalog-label" for="catalog-search">পণ্যের নাম দিয়ে খুঁজুন</label><div class="catalog-search-box"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><input type="search" id="catalog-search" name="search" value="{{ request('search') }}" maxlength="160" placeholder="যেমন: Galaxy, iPhone, A55 ..." autocomplete="off"></div></div>
                <details class="catalog-filter-section" open><summary>ব্র্যান্ড <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></summary><div class="catalog-options">@foreach($brands as $brand)<label><input type="checkbox" name="brand[]" value="{{ $brand->slug }}" @checked(in_array($brand->slug, $selectedBrands))><span>{{ $brand->name }} <small>({{ $brand->products_count }})</small></span></label>@endforeach</div></details>
                <details class="catalog-filter-section" open><summary>ক্যাটাগরি <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></summary><select name="category" aria-label="ক্যাটাগরি নির্বাচন করুন"><option value="">সব ক্যাটাগরি</option>@foreach($categories as $category)<option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }} ({{ $category->products_count }})</option>@endforeach</select></details>
                @foreach($attributes as $attribute)
                    @if($attribute->values->isNotEmpty())
                        <details class="catalog-filter-section" open><summary>{{ ['color' => 'কালার', 'size' => 'সাইজ', 'storage' => 'স্টোরেজ (ROM)', 'ram' => 'র‍্যাম (RAM)', 'rom' => 'স্টোরেজ (ROM)'][$attribute->slug] ?? $attribute->name }} <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></summary><div class="catalog-options">@foreach($attribute->values as $value)<label><input type="checkbox" name="attributes[{{ $attribute->id }}][]" value="{{ $value->id }}" @checked(in_array($value->id, (array) request('attributes.' . $attribute->id, [])))><span>{{ $value->value }}</span></label>@endforeach</div></details>
                    @endif
                @endforeach
                <details class="catalog-filter-section" open><summary>রেটিং <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></summary><div class="catalog-options catalog-rating-options"><label><input type="radio" name="rating" value="" @checked(!request('rating'))><span>সব রেটিং</span></label>@foreach([5, 4, 3, 2, 1] as $stars)<label><input type="radio" name="rating" value="{{ $stars }}" @checked(request('rating') == $stars)><span class="catalog-stars" aria-hidden="true">{{ str_repeat('★', $stars) }}<span>{{ str_repeat('★', 5 - $stars) }}</span></span><small>{{ $stars }} ও তার বেশি</small></label>@endforeach</div></details>
                <details class="catalog-filter-section" open><summary>সুবিধা <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></summary><div class="catalog-options"><label><input type="checkbox" name="in_stock" value="1" @checked(request()->boolean('in_stock'))><span>শুধু স্টকে থাকা পণ্য</span></label><label><input type="checkbox" name="installment" value="1" @checked(request()->boolean('installment'))><span>কিস্তি সুবিধা আছে</span></label></div></details>
            </aside>
            <div class="catalog-main">
                <div class="catalog-heading"><div><h1 id="catalog-title">{{ $pageTitle }}</h1><p>পছন্দের পণ্য খুঁজুন, সেরা দামে</p></div><div class="catalog-toolbar"><span class="catalog-count">মোট <strong id="catalog-total">{{ $products->total() }}</strong> টি পণ্য পাওয়া গেছে</span><label class="catalog-sort">সাজান: <select name="sort" aria-label="পণ্য সাজান">@foreach(['popular' => 'জনপ্রিয়তা অনুযায়ী', 'newest' => 'নতুন আগে', 'price_low' => 'দাম: কম থেকে বেশি', 'price_high' => 'দাম: বেশি থেকে কম', 'rating' => 'রেটিং অনুযায়ী'] as $key => $label)<option value="{{ $key }}" @selected($currentSort === $key)>{{ $label }}</option>@endforeach</select></label><div class="catalog-view"><button type="button" class="is-active" data-view="grid" aria-label="গ্রিড ভিউ" aria-pressed="true"><i class="fa-solid fa-border-all" aria-hidden="true"></i></button><button type="button" data-view="list" aria-label="লিস্ট ভিউ" aria-pressed="false"><i class="fa-solid fa-list" aria-hidden="true"></i></button></div></div></div>
                <div class="catalog-mobile-search"><button type="button" class="catalog-open-filters" aria-controls="catalog-filters" aria-expanded="false"><i class="fa-solid fa-sliders" aria-hidden="true"></i> ফিল্টার</button><div class="catalog-search-box"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><input type="search" id="catalog-mobile-search" value="{{ request('search') }}" maxlength="160" placeholder="পণ্যের নাম দিয়ে খুঁজুন…" aria-label="পণ্যের নাম দিয়ে খুঁজুন" autocomplete="off"></div></div>
                <div class="catalog-brand-chips" aria-label="ব্র্যান্ড ফিল্টার"><button type="button" class="catalog-chip {{ empty($selectedBrands) ? 'is-active' : '' }}" data-brand="" aria-pressed="{{ empty($selectedBrands) ? 'true' : 'false' }}"><span class="catalog-brand-mark"><i class="fa-solid fa-border-all" aria-hidden="true"></i></span>সব</button>@foreach($brands as $brand)<button type="button" class="catalog-chip {{ in_array($brand->slug, $selectedBrands) ? 'is-active' : '' }}" data-brand="{{ $brand->slug }}" aria-pressed="{{ in_array($brand->slug, $selectedBrands) ? 'true' : 'false' }}"><span class="catalog-brand-mark" style="--brand-color: {{ ['#075ac7', '#ff6900', '#8a1538', '#169b62', '#634edb'][$loop->index % 5] }}">{{ mb_substr($brand->name, 0, 1) }}</span>{{ $brand->name }}</button>@endforeach</div>
                <div class="catalog-feedback" id="catalog-feedback" role="status" aria-live="polite" hidden><span class="catalog-spinner" aria-hidden="true"></span><span id="catalog-feedback-text"></span><button type="button" id="catalog-retry" hidden>আবার চেষ্টা করুন</button></div>
                <div id="catalog-results" aria-busy="false">
                    @fragment('catalog-results')
                    <div class="catalog-grid">@forelse($products as $product)@include('theme.partials.product-card', ['product' => $product])@empty<div class="catalog-empty"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><h2>কোনো পণ্য পাওয়া যায়নি</h2><p>অন্য নাম দিয়ে খুঁজুন অথবা ফিল্টার পরিবর্তন করুন।</p><button type="button" class="catalog-apply" data-reset>সব ফিল্টার মুছুন</button></div>@endforelse</div>
                    <div class="catalog-bottom"><span class="catalog-showing">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} / {{ $products->total() }} পণ্য</span><nav class="catalog-pagination" aria-label="পণ্য পৃষ্ঠা">
                        @if($products->hasPages())
                            @if($products->onFirstPage())<span aria-disabled="true" aria-label="আগের পৃষ্ঠা">‹</span>@else<a href="{{ $products->previousPageUrl() }}" aria-label="আগের পৃষ্ঠা">‹</a>@endif
                            @php($previousPage = 0)
                            @foreach(collect([1, $products->currentPage() - 1, $products->currentPage(), $products->currentPage() + 1, $products->lastPage()])->filter(fn ($page) => $page >= 1 && $page <= $products->lastPage())->unique()->sort() as $page)
                                @if($previousPage && $page - $previousPage > 1)<span class="catalog-ellipsis">…</span>@endif
                                @if($page === $products->currentPage())<span class="is-active" aria-current="page">{{ $page }}</span>@else<a href="{{ $products->url($page) }}" aria-label="পৃষ্ঠা {{ $page }}">{{ $page }}</a>@endif
                                @php($previousPage = $page)
                            @endforeach
                            @if($products->hasMorePages())<a href="{{ $products->nextPageUrl() }}" aria-label="পরের পৃষ্ঠা">›</a>@else<span aria-disabled="true" aria-label="পরের পৃষ্ঠা">›</span>@endif
                        @endif
                    </nav><label class="catalog-per-page">প্রতি পৃষ্ঠায় <select name="per_page" aria-label="প্রতি পৃষ্ঠায় পণ্য">@foreach([12, 24, 48] as $size)<option value="{{ $size }}" @selected($products->perPage() === $size)>{{ $size }}</option>@endforeach</select></label></div>
                    @endfragment
                </div>
            </div>
        </div>
    </form>
    <button type="button" class="catalog-backdrop" aria-label="ফিল্টার বন্ধ করুন" tabindex="-1" hidden></button>
</section>
@endsection
@pushOnce('style', 'catalog-product-card-style')
    <link rel="stylesheet" href="{{ asset('assets/theme/css/product-card.css') }}">
@endPushOnce
@push('style')
<style>
.catalog{--cat-blue:#0866ff;--cat-ink:#0e204c;--cat-muted:#7485a7;--cat-border:#e7edf6;color:var(--cat-ink);padding:18px 0 38px;position:relative;isolation:isolate;font-size:14px}
.catalog:before{content:"";position:absolute;inset:0 calc(50% - 50vw);background:#f8fbff;z-index:-1;pointer-events:none}
.catalog button,.catalog input,.catalog select{font-family:inherit}.catalog button{cursor:pointer}.catalog button:focus-visible,.catalog a:focus-visible,.catalog input:focus-visible,.catalog select:focus-visible,.catalog summary:focus-visible{outline:3px solid #94bdff;outline-offset:3px}.catalog [hidden]{display:none!important}
.catalog-breadcrumb{display:flex;align-items:center;gap:10px;font-size:12px;color:#62759b;margin:0 0 17px}.catalog-breadcrumb a:hover{color:var(--cat-blue)}.catalog-breadcrumb i{font-size:11px}
.catalog-layout{display:grid;grid-template-columns:225px minmax(0,1fr);gap:25px;align-items:start}.catalog-main{min-width:0}.catalog-sidebar{background:#fff;border:1px solid var(--cat-border);border-radius:9px;box-shadow:0 3px 14px #15396305;padding:0 16px}
.catalog-filter-heading{min-height:51px;display:flex;align-items:center;justify-content:space-between;gap:8px}.catalog-filter-heading strong{font-weight:600}.catalog-filter-heading strong i{font-size:12px;margin-right:5px}.catalog-filter-heading button{border:1px solid var(--cat-border);background:#fff;color:var(--cat-blue);border-radius:5px;padding:2px 6px;font-size:11px}.catalog-close-filters{display:none}
.catalog-filter-section{padding:14px 0;border-top:1px solid #f0f3f8}.catalog-label,.catalog-filter-section summary{font-size:13px;font-weight:600;display:block;margin-bottom:9px}.catalog-filter-section summary{list-style:none;cursor:pointer;display:flex;justify-content:space-between;align-items:center;margin:0}.catalog-filter-section summary::-webkit-details-marker{display:none}.catalog-filter-section summary i{font-size:9px;color:#6e84ad;transition:transform .2s}.catalog-filter-section[open] summary{margin-bottom:10px}.catalog-filter-section[open] summary i{transform:rotate(180deg)}
.catalog-range{padding:0 0 6px}.catalog-range input{width:100%;height:5px;accent-color:var(--cat-blue);cursor:pointer}.catalog-price-inputs{display:flex;gap:8px;margin:8px 0}.catalog-price-inputs label{display:flex;align-items:center;border:1px solid var(--cat-border);border-radius:5px;width:50%;padding:5px 7px;gap:4px;font-size:11px}.catalog-price-inputs input{border:0;outline:0;min-width:0;width:100%;background:transparent;color:var(--cat-ink);appearance:textfield}.catalog-price-inputs input::-webkit-inner-spin-button{appearance:none}.catalog-apply{border:0;background:var(--cat-blue);color:#fff;width:100%;min-height:32px;border-radius:5px;font-size:12px;font-weight:500}.catalog-search-box{display:flex;align-items:center;border:1px solid var(--cat-border);border-radius:5px;background:#fff;gap:8px;padding:6px 9px}.catalog-search-box i{font-size:12px;color:#607da9}.catalog-search-box input{border:0;background:transparent;outline:0;min-width:0;width:100%;font-size:11px;color:var(--cat-ink)}.catalog-search-box input::placeholder{color:#90a0bd}.catalog-search-box:focus-within{border-color:#75aaff;box-shadow:0 0 0 2px #0866ff13}
.catalog-options{display:flex;flex-direction:column;gap:7px;max-height:195px;overflow:auto}.catalog-options label{display:flex;align-items:center;gap:7px;font:11px "Inter","Hind Siliguri",sans-serif;cursor:pointer}.catalog-options input{width:13px;height:13px;accent-color:var(--cat-blue);flex-shrink:0}.catalog-options small{color:var(--cat-muted);font-size:10px}.catalog-filter-section select{width:100%;font-size:11px}.catalog select{border:1px solid var(--cat-border);border-radius:5px;background:#fff;color:var(--cat-ink);padding:8px 9px;max-width:100%}.catalog-stars{color:#ffa000;font-size:14px;letter-spacing:1px;white-space:nowrap}.catalog-stars span{color:#dce4ef}.catalog-rating-options{gap:5px}
.catalog-heading{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:0 0 22px;flex-wrap:wrap}.catalog-heading h1{font-size:29px;font-weight:700;line-height:1.2;margin:0;color:#091b43}.catalog-heading p{color:var(--cat-muted);font-size:13px;margin:0}.catalog-toolbar{display:flex;align-items:center;gap:12px;flex-wrap:wrap}.catalog-count{color:#60749b;font-size:12px;white-space:nowrap}.catalog-count strong{color:var(--cat-ink)}.catalog-sort{display:flex;align-items:center;gap:8px;font-size:12px;white-space:nowrap}.catalog-sort select{width:159px;font-weight:500}.catalog-view{display:flex;gap:5px}.catalog-view button{height:35px;width:35px;display:grid;place-items:center;border:1px solid var(--cat-border);border-radius:6px;background:#fff;color:#244476}.catalog-view button.is-active{background:var(--cat-blue);border-color:var(--cat-blue);color:#fff}
.catalog-brand-chips,.catalog-quick-filters{display:flex;gap:9px;align-items:center;overflow-x:auto;padding:1px 1px 5px;scrollbar-width:none;-ms-overflow-style:none;}.catalog-brand-chips{margin-bottom:10px}.catalog-quick-filters{margin-bottom:17px}.catalog-chip{flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;gap:7px;background:#fff;border:1px solid var(--cat-border);border-radius:8px;padding:8px 11px;min-height:34px;color:var(--cat-ink);font-size:12px;white-space:nowrap;transition:background .15s,border-color .15s}.catalog-brand-chips .catalog-chip{font-size:11px;font-family:"Inter","Hind Siliguri",sans-serif}.catalog-brand-mark{--brand-color:#0866ff;display:grid;place-items:center;width:19px;height:19px;background:var(--brand-color);color:#fff;border-radius:50%;font-size:10px;font-weight:700}.catalog-chip:hover{border-color:#a6c8ff}.catalog-chip.is-active,.catalog-chip[aria-pressed="true"]{color:#fff;background:var(--cat-blue);border-color:var(--cat-blue)}.catalog-chip.is-active .catalog-brand-mark{background:#ffffff40}.catalog-stock-chip i{color:#13964c;font-size:11px}.catalog-stock-chip[aria-pressed="true"] i{color:#fff}
.catalog-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:12px}.catalog-apply:hover{background:#0057e4}
.catalog-bottom{display:flex;align-items:center;justify-content:space-between;gap:12px;padding-top:28px;flex-wrap:wrap}.catalog-showing{color:var(--cat-muted);font-size:11px}.catalog-pagination{display:flex;gap:5px;align-items:center}.catalog-pagination a,.catalog-pagination>span{display:grid;place-items:center;min-width:31px;height:33px;padding:0 7px;background:#fff;border:1px solid var(--cat-border);border-radius:5px;font:12px "Inter",sans-serif}.catalog-pagination .is-active{background:var(--cat-blue);border-color:var(--cat-blue);color:#fff}.catalog-pagination [aria-disabled]{opacity:.4}.catalog-pagination a:hover{border-color:var(--cat-blue);color:var(--cat-blue)}.catalog-per-page{display:flex;align-items:center;gap:9px;font-size:12px;color:#60749b}.catalog-per-page select{font-family:"Inter",sans-serif;padding:7px;min-width:59px}.catalog-empty{grid-column:1/-1;text-align:center;padding:75px 15px;background:#fff;border:1px solid var(--cat-border);border-radius:8px}.catalog-empty>i{font-size:34px;color:#97b5e3;margin-bottom:18px}.catalog-empty h2{font-size:21px}.catalog-empty p{color:var(--cat-muted)}.catalog-empty .catalog-apply{width:auto;padding:6px 20px}
.catalog-feedback{display:flex;align-items:center;gap:9px;padding:10px 13px;margin:0 0 12px;border-radius:6px;background:#eaf2ff;color:#1256ba;font-size:13px}.catalog-feedback.is-error{background:#fff0f0;color:#b42c32}.catalog-feedback.is-error .catalog-spinner{display:none}.catalog-feedback button{border:0;border-bottom:1px solid currentColor;background:transparent;color:inherit;margin-left:auto}.catalog-spinner{width:16px;height:16px;flex-shrink:0;border:2px solid #0866ff30;border-top-color:var(--cat-blue);border-radius:50%;animation:catalog-spin .65s linear infinite}@keyframes catalog-spin{to{transform:rotate(360deg)}}#catalog-results{transition:opacity .15s}#catalog-results[aria-busy="true"]{opacity:.45;pointer-events:none}
.catalog-mobile-search{display:none}
.catalog.is-list .catalog-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.catalog.is-list .catalog-card{display:grid;grid-template-columns:38% minmax(0,1fr);align-items:center}.catalog.is-list .catalog-image{height:190px}.catalog.is-list .catalog-card-body{padding:20px 12px}.catalog.is-list .catalog-name{font-size:13px!important;height:40px}.catalog.is-list .catalog-price{min-height:0;margin:10px 0}
@media(min-width:1400px){.catalog-layout{grid-template-columns:230px minmax(0,1fr);gap:28px}.catalog-heading{align-items:flex-end}.catalog-toolbar{gap:10px}}
@media(min-width:768px) and (max-width:1099px){.catalog-layout{grid-template-columns:195px minmax(0,1fr);gap:15px}.catalog-sidebar{padding:0 12px}.catalog-grid{gap:9px}.catalog-toolbar{gap:8px}.catalog-view{display:none}}
@media(min-width:768px) and (max-width:991px){.catalog-grid{grid-template-columns:repeat(4,minmax(0,1fr))}}
@media(max-width:767px){.catalog{padding:13px 0 25px;font-size:12px}.catalog-breadcrumb{font-size:11px;margin-bottom:10px;gap:8px}.catalog-layout{display:block}.catalog-heading{margin-bottom:5px;gap:13px}.catalog-heading>div:first-child{width:100%}.catalog-heading h1{font-size:25px}.catalog-heading p{font-size:12px}.catalog-toolbar{justify-content:space-between;gap:7px;width:100%}.catalog-count{font-size:10px}.catalog-sort{font-size:10px;gap:4px}.catalog-sort select{width:134px;padding:3px 0px;font-size:10px}.catalog-view{display:none}.catalog-mobile-search{display:flex;gap:8px;margin-bottom:13px}.catalog-open-filters{border:1px solid #dce8fb;border-radius:6px;background:#fff;color:var(--cat-blue);padding:8px 12px;white-space:nowrap}.catalog-mobile-search .catalog-search-box{flex:1;min-width:0}.catalog-mobile-search input{font-size:12px}.catalog-brand-chips,.catalog-quick-filters{gap:6px}.catalog-brand-chips{margin-bottom:6px}.catalog-quick-filters{margin-bottom:12px}.catalog-chip{font-size:11px;padding:6px 9px;min-height:32px;border-radius:6px}.catalog-brand-chips .catalog-chip{font-size:10px}.catalog-brand-mark{width:17px;height:17px;font-size:9px}.catalog-grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:7px}.catalog-bottom{gap:12px;padding-top:20px}.catalog-showing{font-size:10px}.catalog-per-page{font-size:11px}.catalog-pagination{order:3;width:100%;justify-content:center}
.catalog-sidebar{
    display:block;
    position:fixed;
    inset:0 auto 0 0;
    width:min(310px,88vw);
    border:0;
    border-radius:0;
    overflow:auto;
    z-index:1055;
    padding:0 18px 35px;
    overscroll-behavior:contain;
    background:#fff;
    transform:translateX(-105%);
    opacity:0;
    pointer-events:none;
    transition:transform .35s ease, opacity .25s ease;
}

.catalog.filters-open .catalog-sidebar{
    transform:translateX(0);
    opacity:1;
    pointer-events:auto;
}
.catalog-backdrop{
    position:fixed;
    inset:0;
    border:0;
    background:#10234a70;
    z-index:1050;
    opacity:0;
    pointer-events:none;
    transition:opacity .3s ease;
}

.catalog.filters-open .catalog-backdrop{
    opacity:1;
    pointer-events:auto;
}
.catalog-close-filters{display:block}.catalog-filter-heading{position:sticky;top:0;background:#fff;z-index:2;min-height:59px}.catalog-filter-heading .catalog-close-filters{font-size:22px;color:var(--cat-ink);border:0}.catalog-filter-heading strong{margin-right:auto}.catalog-backdrop{position:fixed;inset:0;border:0;background:#10234a70;z-index:1050}.catalog-sidebar .catalog-options label{font-size:13px;min-height:25px}.catalog-sidebar .catalog-filter-section select{font-size:13px}.catalog-sidebar .catalog-search-box input{font-size:12px}.catalog-sidebar .catalog-options input{width:16px;height:16px}.catalog-sidebar .catalog-apply{min-height:38px}.catalog-empty{padding:50px 12px}.catalog-empty h2{font-size:19px}.catalog-feedback{font-size:12px}.catalog.is-list .catalog-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.catalog.is-list .catalog-card{display:block}.catalog.is-list .catalog-image{height:125px}.catalog.is-list .catalog-card-body{padding:5px 6px 7px}.catalog.is-list .catalog-name{font-size:10px!important;height:30px}}
@media(max-width:374px){.catalog-grid{gap:5px}.catalog-sort select{width:117px}.catalog-count{font-size:9px}}
@media(prefers-reduced-motion:reduce){.catalog *{animation:none!important;transition:none!important;scroll-behavior:auto!important}}
.catalog.filters-open{z-index:1400}.catalog-filter-section{padding:11px 0}.catalog-heading{scroll-margin-top:85px}.catalog-filter-status{color:var(--cat-blue);font-size:12px;padding:8px 0}.catalog-filter-status.is-error{color:#b42c32}
@media(min-width:1200px){.catalog{width:min(1440px,calc(100vw - 64px));margin-left:calc((100% - min(1440px,calc(100vw - 64px))) / 2)}.catalog-layout{grid-template-columns:250px minmax(0,1fr);gap:28px}.catalog-heading{margin-bottom:19px}}




.catalog button:focus-visible, .catalog a:focus-visible, .catalog input:focus-visible, .catalog select:focus-visible, .catalog summary:focus-visible{
    outline: none;
    outline-offset: 0;
}

.catalog-brand-chips::-webkit-scrollbar,
.catalog-quick-filters::-webkit-scrollbar{
    display:none;
}





@media(max-width:767px){

.catalog-heading{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:nowrap;
}

.catalog-heading h1{
    font-size:24px;
}

.catalog-heading p{
    display:none;
}

.catalog-toolbar{
    width:auto;
    margin-top:0;
}

.catalog-count{
    display:none;
}

.catalog-sort select{
    width:120px;
}

}

</style>
@endpush
@push('script')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('catalog');
    const form = document.getElementById('catalog-form');
    const results = document.getElementById('catalog-results');
    const search = document.getElementById('catalog-search');
    const mobileSearch = document.getElementById('catalog-mobile-search');
    const feedback = document.getElementById('catalog-feedback');
    const feedbackText = document.getElementById('catalog-feedback-text');
    const retry = document.getElementById('catalog-retry');
    const filterStatus = root.querySelector('.catalog-filter-status');
    const range = document.getElementById('catalog-max-range');
    let timer, controller, sequence = 0, pendingUrl;
    let saved = new Set();
    try { const value = JSON.parse(localStorage.getItem('bondon.catalog.saved') || '[]'); if (Array.isArray(value)) saved = new Set(value.map(String)); } catch (_) {}
    const field = name => form.elements.namedItem(name);
    const number = value => new Intl.NumberFormat('bn-BD').format(value);

    function syncControls() {
        mobileSearch.value = search.value;
        document.querySelectorAll('#topSearch .search-input, #mainSearch .search-input').forEach(input => input.value = search.value);
        root.querySelectorAll('[data-brand]').forEach(button => {
            const boxes = [...form.querySelectorAll('input[name="brand[]"]')];
            const active = button.dataset.brand ? boxes.some(box => box.value === button.dataset.brand && box.checked) : boxes.every(box => !box.checked);
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-pressed', String(active));
        });
        root.querySelectorAll('[data-toggle]').forEach(button => button.setAttribute('aria-pressed', String(field(button.dataset.toggle).checked)));
        root.querySelectorAll('[data-sort]').forEach(button => button.setAttribute('aria-pressed', String(field('sort').value === button.dataset.sort)));
        root.querySelectorAll('[data-save]').forEach(button => {
            const active = saved.has(button.dataset.save);
            button.setAttribute('aria-pressed', String(active));
            button.querySelector('i').className = (active ? 'fa-solid' : 'fa-regular') + ' fa-heart';
        });
    }

    function urlFromForm() {
        const url = new URL(form.action);
        for (const [key, value] of new FormData(form)) { if (String(value).trim() !== '') url.searchParams.append(key, String(value).trim()); }
        return url;
    }
    function busy() {
        feedback.hidden = false;
        feedback.classList.remove('is-error');
        feedbackText.textContent = 'পণ্য খোঁজা হচ্ছে…';
        retry.hidden = true;
        results.setAttribute('aria-busy', 'true');
        filterStatus.hidden = false;
        filterStatus.classList.remove('is-error');
        filterStatus.textContent = 'পণ্য খোঁজা হচ্ছে…';
    }
    async function load(url = urlFromForm(), push = true) {
        clearTimeout(timer);
        controller?.abort();
        controller = new AbortController();
        const id = ++sequence;
        pendingUrl = url;
        syncControls();
        busy();
        const endpoint = new URL(form.dataset.endpoint);
        endpoint.search = url.search;
        try {
            const response = await fetch(endpoint, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, signal: controller.signal });
            if (!response.ok) throw new Error(response.status === 422 ? 'ফিল্টারের মান ঠিক করুন। সর্বোচ্চ দাম সর্বনিম্ন দামের চেয়ে কম হতে পারবে না।' : 'পণ্য আনা যায়নি। সংযোগ পরীক্ষা করে আবার চেষ্টা করুন।');
            const data = await response.json();
            if (id !== sequence) return;
            if (typeof data.html !== 'string') throw new Error('পণ্য আনা যায়নি। আবার চেষ্টা করুন।');
            results.innerHTML = data.html;
            document.getElementById('catalog-total').textContent = number(data.total);
            document.getElementById('catalog-title').textContent = data.title;
            document.getElementById('catalog-crumb').textContent = data.title;
            document.title = data.title + ' | ' + @json(gs()->site_name ?? config('app.name'));
            feedback.hidden = true;
            filterStatus.textContent = number(data.total) + 'টি পণ্য পাওয়া গেছে';
            filterStatus.hidden = !root.classList.contains('filters-open');
            if (push && url.href !== location.href) history.pushState({}, '', url);
            syncControls();
        } catch (error) {
            if (error.name === 'AbortError' || id !== sequence) return;
            feedback.classList.add('is-error');
            feedbackText.textContent = error.message || 'পণ্য আনা যায়নি। আবার চেষ্টা করুন।';
            filterStatus.classList.add('is-error');
            filterStatus.textContent = feedbackText.textContent;
            window.notify({ type: 'error', message: feedbackText.textContent });
            retry.hidden = false;
        } finally {
            if (id === sequence) results.setAttribute('aria-busy', 'false');
        }
    }
    function schedule() {
        clearTimeout(timer);
        controller?.abort();
        ++sequence;
        syncControls();
        busy();
        timer = setTimeout(() => load(), 350);
    }
    form.addEventListener('submit', event => { event.preventDefault(); load(); });
    search.addEventListener('input', schedule);
    mobileSearch.addEventListener('input', () => { search.value = mobileSearch.value; schedule(); });
    form.addEventListener('change', event => {
        if (event.target === search || event.target === mobileSearch || event.target === range || ['min_price', 'max_price'].includes(event.target.name)) return;
        load();
    });
    range.addEventListener('input', () => { field('max_price').value = range.value; });
    range.addEventListener('change', () => load());
    field('max_price').addEventListener('input', () => { range.value = field('max_price').value || range.max; });
    retry.addEventListener('click', () => load(pendingUrl));
    function reset() {
        form.querySelectorAll('input').forEach(input => {
            if (input.type === 'hidden') return;
            if (['checkbox', 'radio'].includes(input.type)) input.checked = input.type === 'radio' && input.value === '';
            else if (input.type !== 'range') input.value = '';
        });
        field('category').value = @json($selectedCategory?->slug ?? '');
        field('sort').value = 'popular';
        field('per_page').value = '12';
        range.value = range.max;
        load();
    }
    function closeFilters() {
        root.classList.remove('filters-open');
        root.querySelector('.catalog-backdrop').hidden = true;
        root.querySelector('.catalog-open-filters').setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }
    root.addEventListener('click', event => {
        const button = event.target.closest('button');
        const link = event.target.closest('.catalog-pagination a');
        if (link && !event.ctrlKey && !event.metaKey && !event.shiftKey && !event.altKey) { event.preventDefault(); load(new URL(link.href)).then(() => root.querySelector('.catalog-heading').scrollIntoView({ block: 'start', behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth' })); return; }
        if (!button) return;
        if (button.hasAttribute('data-reset')) reset();
        if (button.hasAttribute('data-brand')) {
            form.querySelectorAll('input[name="brand[]"]').forEach(box => box.checked = box.value === button.dataset.brand);
            load();
        }
        if (button.dataset.toggle) { field(button.dataset.toggle).checked = !field(button.dataset.toggle).checked; load(); }
        if (button.dataset.sort) { field('sort').value = button.dataset.sort; load(); }
        if (button.dataset.view) {
            root.classList.toggle('is-list', button.dataset.view === 'list');
            root.querySelectorAll('[data-view]').forEach(item => { item.classList.toggle('is-active', item === button); item.setAttribute('aria-pressed', String(item === button)); });
        }
        if (button.dataset.save) {
            const id = button.dataset.save;
            saved.has(id) ? saved.delete(id) : saved.add(id);
            let message = saved.has(id) ? 'এই ব্রাউজারের পছন্দের তালিকায় রাখা হয়েছে' : 'পছন্দের তালিকা থেকে সরানো হয়েছে';
            try { localStorage.setItem('bondon.catalog.saved', JSON.stringify([...saved])); } catch (_) { message = 'এই সেশনের জন্য পছন্দের তালিকা আপডেট হয়েছে'; }
            syncControls();
            window.notify({ type: 'success', message });
        }
        if (button.classList.contains('catalog-open-filters')) {
            root.classList.add('filters-open');
            root.querySelector('.catalog-backdrop').hidden = false;
            button.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
            root.querySelector('.catalog-close-filters').focus();
        }
        if (button.matches('.catalog-close-filters,.catalog-backdrop')) { closeFilters(); root.querySelector('.catalog-open-filters').focus(); }
    });
    document.addEventListener('keydown', event => {
        if (!root.classList.contains('filters-open')) return;
        if (event.key === 'Escape') { closeFilters(); root.querySelector('.catalog-open-filters').focus(); }
        if (event.key === 'Tab') {
            const focusable = [...root.querySelector('.catalog-sidebar').querySelectorAll('button,input,select,summary')].filter(el => el.getClientRects().length);
            const first = focusable[0], last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
    });
    matchMedia('(min-width: 768px)').addEventListener('change', event => { if (event.matches) closeFilters(); });
    // Reuse the layout's search controls on this page without editing the header.
    document.querySelectorAll('#topSearch, #mainSearch').forEach(area => {
        const input = area.querySelector('.search-input');
        if (!input) return;
        input.addEventListener('input', () => { search.value = input.value; schedule(); area.querySelector('.search-dropdown')?.classList.remove('show'); });
        input.addEventListener('keydown', event => { if (event.key === 'Enter') { event.preventDefault(); search.value = input.value; load(); } });
        area.querySelector('button')?.addEventListener('click', () => { search.value = input.value; load(); });
        area.querySelectorAll('.search-item').forEach(item => item.addEventListener('click', () => { search.value = item.textContent.trim(); load(); }));
    });
    window.addEventListener('popstate', () => {
        const params = new URL(location.href).searchParams;
        form.querySelectorAll('input[name],select[name]').forEach(input => {
            // Laravel pagination encodes arrays as brand[0]; accept both URL formats.
            const values = [...params].filter(([key]) => key === input.name || (input.name.endsWith('[]') && key.replace(/\[\d+\]$/, '[]') === input.name)).map(([, value]) => value);
            if (['checkbox', 'radio'].includes(input.type)) input.checked = values.includes(input.value) || (input.name === 'rating' && input.value === '' && !params.has('rating'));
            else input.value = params.get(input.name) ?? (input.name === 'sort' ? 'popular' : input.name === 'per_page' ? '12' : '');
        });
        range.value = field('max_price').value || range.max;
        load(new URL(location.href), false);
    });
    syncControls();
    document.getElementById('catalog-total').textContent = number(@json($products->total()));
});
</script>
@endpush
