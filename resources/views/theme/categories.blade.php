@extends('theme.layouts.frontend')

@section('content')
<div class="category-page">
    <div class="category-page-head">
        <div>
            <div class="shop-breadcrumb"><a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> হোম</a><i class="fa-solid fa-chevron-right"></i><span>শ্রেণি বিভাগ</span></div>
            <h1>শ্রেণি বিভাগ</h1>
            <p>আপনার প্রয়োজনের ক্যাটাগরি থেকে পণ্য দেখুন</p>
        </div>
        <div class="category-total-box"><strong>{{ $categories->count() }}</strong><span>টি ক্যাটাগরি</span></div>
    </div>

    <div class="category-page-grid">
        @forelse($categories as $category)
            <a href="{{ route('products', ['category' => $category->slug]) }}" class="category-page-card">
                <div class="category-page-image"><img src="{{ asset('assets/theme/images/categories/' . $category->image) }}" alt="{{ $category->name }}" loading="lazy" decoding="async"></div>
                <div class="category-page-info">
                    <h3>{{ $category->name }}</h3>
                    <p>{{ $category->products_count }} টি পণ্য</p>
                    <span>সব পণ্য দেখুন <i class="fa-solid fa-chevron-right"></i></span>
                </div>
            </a>
        @empty
            <div class="shop-empty"><i class="fa-solid fa-box-open"></i><strong>কোনো ক্যাটাগরি পাওয়া যায়নি</strong></div>
        @endforelse
    </div>
</div>
@endsection

@push('style')
<link rel="stylesheet" href="{{ asset('assets/theme/css/product-list.css') }}">
@endpush
