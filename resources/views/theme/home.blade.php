@extends('theme.layouts.frontend')
@section('content')
<!--   HERO  -->
<section class="hero">
    <div id="homeHeroSlider" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4500"
        data-bs-touch="true">

        <div class="carousel-indicators">
            <button type="button" data-bs-target="#homeHeroSlider" data-bs-slide-to="0" class="active"
                aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#homeHeroSlider" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#homeHeroSlider" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#homeHeroSlider" data-bs-slide-to="3" aria-label="Slide 4"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <picture>
                    <img src="{{ asset('assets/theme/images/banner/shopping-installment-banner.webp') }}"
                        alt="কিস্তিতে কেনাকাটা" fetchpriority="high" decoding="async">
                </picture>
            </div>

            <div class="carousel-item">
                <picture>
                    <img src="{{ asset('assets/theme/images/banner/s1.png') }}" alt="বিশেষ অফার" loading="lazy"
                        decoding="async">
                </picture>
            </div>

            <div class="carousel-item">
                <picture>
                    <img src="{{ asset('assets/theme/images/banner/s2.png') }}" alt="নতুন পণ্যের অফার" loading="lazy"
                        decoding="async">
                </picture>
            </div>
            <div class="carousel-item">
                <picture>
                    <img src="{{ asset('assets/theme/images/banner/s3.png') }}" alt="নতুন পণ্যের অফার" loading="lazy"
                        decoding="async">
                </picture>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#homeHeroSlider" data-bs-slide="prev">
            <span class="hero-slider-control"><i class="fa-solid fa-chevron-left"></i></span>
            <span class="visually-hidden">Previous</span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#homeHeroSlider" data-bs-slide="next">
            <span class="hero-slider-control"><i class="fa-solid fa-chevron-right"></i></span>
            <span class="visually-hidden">Next</span>
        </button>

    </div>
</section>
<div class="discovery-stack search-only-stack">

    <!-- MAIN SEARCH -->
    <section class="main-search">

        <div class="main-search-inner">

            <form
                class="main-search-box"
                id="mainSearch"
                action="{{ route('products') }}"
                method="GET"
                data-suggestions-url="{{ route('products.suggestions') }}"
            >

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    class="search-input"
                    name="search"
                    placeholder="পণ্য খুঁজুন (যেমন: মোবাইল, ল্যাপটপ, ফ্রিজ...)"
                    type="search"
                    aria-label="পণ্য খুঁজুন"
                    autocomplete="off"
                    maxlength="160"
                />

                <button type="submit">
                    <span class="search-btn-text">
                        খুঁজুন
                    </span>

                    <i class="fa-solid fa-magnifying-glass search-btn-icon"></i>
                </button>


                <!-- Dynamic Search Result -->
                <div
                    class="search-dropdown"
                    role="listbox"
                    aria-label="পণ্য খোঁজার ফলাফল"
                ></div>

            </form>


            <!-- Popular Categories -->
            <div class="popular-tags">

                <span>জনপ্রিয়:</span>

                @foreach($randomCategories as $category)

                    <a
                        class="tag"
                        href="{{ route('products', ['category' => $category->slug]) }}"
                    >
                        {{ $category->name }}
                    </a>

                @endforeach

            </div>

        </div>

    </section>

</div>
<!--   CATEGORY -->
<section class="section category-section">

    <div class="section-head">

        <h2>শ্রেণি বিভাগ</h2>

        <a href="{{ route('categories') }}">
            সব দেখুন
            <i class="fa-solid fa-chevron-right"></i>
        </a>

    </div>


    <div class="category-grid row row-cols-7 row-cols-lg-10 g-2">

        @foreach($categories as $category)

            <div class="col category-col">

                <a
                    class="category-card"
                    href="{{ route('products', ['category' => $category->slug]) }}"
                >

                    <img
                        class="category-icon"
                        src="{{ asset('assets/theme/images/categories/' . $category->image) }}"
                        alt="{{ $category->name }}"
                    />

                    <div class="category-title">
                        {{ $category->name }}
                    </div>

                </a>

            </div>

        @endforeach

    </div>

</section>
<!--  PRODUCT SECTIONS  -->
<section class="product-section-band home-product-cards">
    <section class="stacked-product-block popular-products-block">
        <div class="section-head product-section-head">
            <h2>জনপ্রিয় পণ্য</h2>
            <a href="{{ route('products', ['type' => 'popular']) }}">সব দেখুন <i class="fa-solid fa-chevron-right"></i></a>
        </div>
        <div class="product-grid row g-2 g-lg-3">
            @forelse($popularProducts as $product)
                <div class="col-4 col-lg-2 product-col">
                    @include('theme.partials.product-card', ['product' => $product])
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-4">কোনো পণ্য পাওয়া যায়নি</div>
                </div>
            @endforelse
        </div>
    </section>
    <section class="stacked-product-block installment-products-block">
        <div class="section-head product-section-head">
            <h2>কিস্তিতে জনপ্রিয় পণ্য</h2>
            <a href="{{ route('products', ['type' => 'installment']) }}">সব দেখুন <i class="fa-solid fa-chevron-right"></i></a>
        </div>
        <div class="product-grid row g-2 g-lg-3">
            @forelse($installmentProducts as $product)
                <div class="col-4 col-lg-2 product-col">
                    @include('theme.partials.product-card', ['product' => $product])
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-4">কোনো কিস্তির পণ্য পাওয়া যায়নি</div>
                </div>
            @endforelse
        </div>
    </section>
</section>

@if($flashSales->count() && $flashSaleEnd)
<div class="catalog-highlight-sections">
    <section class="flash-sale-wrap" id="flashSaleCountdown" data-deadline="{{ $flashSaleEnd->format('Y-m-d\TH:i:s') }}">
        <div class="flash-sale-left">
            <h2>ফ্ল্যাশ সেল</h2>
            <p>সীমিত সময়ের জন্য নির্বাচিত পণ্যে বিশেষ ছাড়</p>
        </div>

        <div class="flash-sale-products home-product-cards">
            @foreach($flashSales as $flashSale)
                @include('theme.partials.product-card', ['product' => $flashSale->product])
            @endforeach
        </div>

        <div class="flash-sale-timer">
            <small>অফার শেষ হতে বাকি</small>
            <div class="flash-sale-time">
                <div class="flash-sale-time-box"><strong id="flashDays">00</strong><span>দিন</span></div>
                <div class="flash-sale-time-box"><strong id="flashHours">00</strong><span>ঘণ্টা</span></div>
                <div class="flash-sale-time-box"><strong id="flashMinutes">00</strong><span>মিনিট</span></div>
                <div class="flash-sale-time-box"><strong id="flashSeconds">00</strong><span>সেকেন্ড</span></div>
            </div>
            <a href="{{ route('products', ['type' => 'flash']) }}" class="flash-sale-btn">সব ফ্ল্যাশ সেল দেখুন <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </section>
</div>
@endif

<div class="homepage-commerce-sections">

    <!--   SPECIAL OFFER -->
    <section class="homepage-content-section">

        <div class="homepage-section-header">
            <h2>বিশেষ অফার</h2>

            <a href="{{ route('products', ['type' => 'offer']) }}">সব অফার দেখুন <i class="fa-solid fa-arrow-right"></i></a>

        </div>

        <div class="promotional-offer-grid">

            <!-- OFFER 1 -->
            <div class="promotional-offer-card promotional-offer-primary">

                <div class="promotional-discount-badge">
                    UP TO
                    <strong>40%</strong>
                    OFF
                </div>

                <div class="promotional-offer-content">

                    <div class="promotional-offer-tag">
                        <i class="fa-regular fa-clock"></i>
                        সীমিত সময়ের জন্য
                    </div>

                    <h3>
                        ইলেকট্রনিক্সে
                        <br>
                        সর্বোচ্চ <span>৪০%</span> ছাড়
                    </h3>

                    <p>
                        সেরা ব্র্যান্ডের ইলেকট্রনিক্সে
                        দারুণ ছাড়, আজই অর্ডার করুন!
                    </p>

                    <a href="#" class="promotional-offer-button">
                        অফার দেখুন
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

                <div class="promotional-product-gallery">

                    <img src="assets/iphone-15.png" alt="iPhone Offer" class="promotional-phone-image" loading="lazy"
                        decoding="async">

                    <img src="assets/dell-inspiron-3530.png" alt="Laptop Offer" class="promotional-laptop-image"
                        loading="lazy" decoding="async">

                    <img src="assets/a4tech-hs50-headphone.png" alt="Headphone Offer"
                        class="promotional-accessory-image" loading="lazy" decoding="async">

                </div>

            </div>


            <!-- OFFER 2 -->
            <div class="promotional-offer-card promotional-offer-secondary">

                <div class="promotional-offer-content">

                    <div class="promotional-offer-tag">
                        <i class="fa-solid fa-gift"></i>
                        শুধু নতুন গ্রাহকদের জন্য
                    </div>

                    <h3>
                        নতুন গ্রাহকদের জন্য
                        <br>
                        বিশেষ ডিল
                    </h3>

                    <p>
                        প্রথম অর্ডারে থাকছে
                        এক্সক্লুসিভ ছাড় ও বিশেষ সুবিধা।
                    </p>

                    <a href="#" class="promotional-offer-button">
                        এখনই কিনুন
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

                <div class="promotional-product-gallery">

                    <img src="assets/samsung-refrigerator-253l.png" alt="Refrigerator Offer"
                        class="promotional-appliance-image" loading="lazy" decoding="async">

                    <div class="promotional-shopping-bag">
                        <i class="fa-solid fa-bag-shopping" style="font-size:22px;margin-bottom:6px;"></i>
                        বন্ডন গ্রুপ
                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- = WHY SHOP WITH US  -->
    <section class="homepage-content-section">

        <div class="homepage-section-header">
            <h2>কেন আমাদের থেকে কিনবেন</h2>
        </div>

        <div class="shopping-benefits-wrapper">

            <div class="shopping-benefits-grid">

                <div class="shopping-benefit-card">

                    <div class="shopping-benefit-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>

                    <strong>
                        ১০০% নিরাপদ লেনদেন
                    </strong>

                    <span>
                        নিরাপদ পেমেন্ট ও তথ্য সুরক্ষা
                    </span>

                </div>


                <div class="shopping-benefit-card">

                    <div class="shopping-benefit-icon">
                        <i class="fa-solid fa-rotate"></i>
                    </div>

                    <strong>
                        সহজ রিটার্ন সুবিধা
                    </strong>

                    <span>
                        শর্তসাপেক্ষে দ্রুত রিটার্ন সুবিধা
                    </span>

                </div>


                <div class="shopping-benefit-card">

                    <div class="shopping-benefit-icon">
                        <i class="fa-solid fa-box-open"></i>
                    </div>

                    <strong>
                        অরিজিনাল পণ্য গ্যারান্টি
                    </strong>

                    <span>
                        বিশ্বস্ত ব্র্যান্ডের আসল পণ্য
                    </span>

                </div>


                <div class="shopping-benefit-card">

                    <div class="shopping-benefit-icon">
                        <i class="fa-solid fa-headset"></i>
                    </div>

                    <strong>
                        ২৪/৭ কাস্টমার সাপোর্ট
                    </strong>

                    <span>
                        যেকোনো সমস্যায় দ্রুত সহায়তা
                    </span>

                </div>

            </div>

        </div>

    </section>


    <!--   CUSTOMER REVIEW SLIDER  -->
    <section class="homepage-content-section customer-reviews-section">

        <div class="homepage-section-header">

            <h2>গ্রাহকদের মতামত</h2>

            <div class="customer-reviews-header-actions">

                <a href="#">
                    সব মতামত দেখুন
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

                <div class="customer-review-navigation">

                    <button type="button" class="customer-review-nav-button customer-review-prev"
                        aria-label="Previous review">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <button type="button" class="customer-review-nav-button customer-review-next"
                        aria-label="Next review">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                </div>

            </div>

        </div>


        <div class="customer-reviews-slider" id="customerReviewsSlider">

            <div class="customer-reviews-track">


                <!-- REVIEW 1 -->
                <article class="customer-review-card">

                    <i class="fa-solid fa-quote-right customer-review-quote"></i>

                    <div class="customer-review-author">

                        <div class="customer-review-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div class="customer-review-author-info">

                            <strong>সাবিনা আক্তার</strong>

                            <div class="customer-review-rating">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>

                        </div>

                    </div>

                    <div class="customer-review-message">
                        অর্ডার করার পর খুব দ্রুত সময়ে পণ্যটি পেয়েছি।
                        প্রোডাক্ট একদম অরিজিনাল এবং প্যাকেজিংও
                        খুব সুন্দর ছিল। সার্ভিস সত্যিই দারুণ।
                    </div>

                    <div class="customer-review-product">

                        <div class="customer-review-product-image">
                            <img src="assets/samsung-galaxy-a55.png" alt="Samsung Galaxy" loading="lazy"
                                decoding="async">
                        </div>

                        <div class="customer-review-product-info">

                            <strong>
                                পণ্য: Samsung Galaxy A55
                            </strong>

                            <div class="customer-review-location">
                                <i class="fa-solid fa-location-dot"></i>
                                ঢাকা, বাংলাদেশ
                            </div>

                        </div>

                    </div>

                </article>


                <!-- REVIEW 2 -->
                <article class="customer-review-card">

                    <i class="fa-solid fa-quote-right customer-review-quote"></i>

                    <div class="customer-review-author">

                        <div class="customer-review-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div class="customer-review-author-info">

                            <strong>রাহাত হোসেন</strong>

                            <div class="customer-review-rating">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>

                        </div>

                    </div>

                    <div class="customer-review-message">
                        ওয়েবসাইট থেকে অর্ডার করা খুবই সহজ ছিল।
                        ডেলিভারি দ্রুত পেয়েছি এবং পণ্যের কোয়ালিটি
                        খুব ভালো। অবশ্যই আবারও কিনবো।
                    </div>

                    <div class="customer-review-product">

                        <div class="customer-review-product-image">
                            <img src="assets/iphone-15.png" alt="iPhone 15" loading="lazy" decoding="async">
                        </div>

                        <div class="customer-review-product-info">

                            <strong>
                                পণ্য: iPhone 15
                            </strong>

                            <div class="customer-review-location">
                                <i class="fa-solid fa-location-dot"></i>
                                চট্টগ্রাম, বাংলাদেশ
                            </div>

                        </div>

                    </div>

                </article>


                <!-- REVIEW 3 -->
                <article class="customer-review-card">

                    <i class="fa-solid fa-quote-right customer-review-quote"></i>

                    <div class="customer-review-author">

                        <div class="customer-review-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div class="customer-review-author-info">

                            <strong>মেহেদী হাসান</strong>

                            <div class="customer-review-rating">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>

                        </div>

                    </div>

                    <div class="customer-review-message">
                        বন্ডন গ্রুপ আমার ভরসার জায়গা।
                        আসল পণ্য, ভালো প্যাকেজিং এবং দারুণ
                        কাস্টমার সাপোর্ট। সবাইকে রেকমেন্ড করবো।
                    </div>

                    <div class="customer-review-product">

                        <div class="customer-review-product-image">
                            <img src="assets/hp-i5-laptop.png" alt="HP Laptop" loading="lazy" decoding="async">
                        </div>

                        <div class="customer-review-product-info">

                            <strong>
                                পণ্য: HP Laptop
                            </strong>

                            <div class="customer-review-location">
                                <i class="fa-solid fa-location-dot"></i>
                                খুলনা, বাংলাদেশ
                            </div>

                        </div>

                    </div>

                </article>


                <!-- REVIEW 4 -->
                <article class="customer-review-card">

                    <i class="fa-solid fa-quote-right customer-review-quote"></i>

                    <div class="customer-review-author">

                        <div class="customer-review-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div class="customer-review-author-info">

                            <strong>তানভীর আহমেদ</strong>

                            <div class="customer-review-rating">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>

                        </div>

                    </div>

                    <div class="customer-review-message">
                        পণ্য অর্ডারের পুরো প্রসেস খুব সহজ।
                        সময়মতো ডেলিভারি পেয়েছি এবং পণ্যও
                        বর্ণনার সাথে সম্পূর্ণ মিলেছে।
                    </div>

                    <div class="customer-review-product">

                        <div class="customer-review-product-image">
                            <img src="assets/walton-smart-tv.png" alt="Walton Smart TV" loading="lazy" decoding="async">
                        </div>

                        <div class="customer-review-product-info">

                            <strong>
                                পণ্য: Walton Smart TV
                            </strong>

                            <div class="customer-review-location">
                                <i class="fa-solid fa-location-dot"></i>
                                রাজশাহী, বাংলাদেশ
                            </div>

                        </div>

                    </div>

                </article>


                <!--
                আরও review দিতে চাইলে
                উপরের একটি <article class="customer-review-card">
                সম্পূর্ণ copy করে এখানে paste করবে।
                -->

            </div>

        </div>

    </section>


    <!-- NEWSLETTER  -->
    <section class="newsletter-subscription">

        <div class="newsletter-visual">

            <div class="newsletter-mail-icon">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>

            <div class="newsletter-gift-icon">
                <i class="fa-solid fa-gift"></i>
            </div>

        </div>


        <div class="newsletter-content">

            <h2>
                অফার সবার আগে জানতে চান?
            </h2>

            <p>
                নতুন অফার, ডিসকাউন্ট ও আপডেট পেতে
                আপনার ইমেইল বা মোবাইল নম্বর দিন
            </p>


            <form class="newsletter-form" action="#" method="post">

                <div class="newsletter-input-wrapper">

                    <i class="fa-regular fa-envelope"></i>

                    <input type="text" name="subscriber" placeholder="আপনার ইমেইল বা মোবাইল নম্বর লিখুন"
                        aria-label="ইমেইল বা মোবাইল নম্বর" autocomplete="email" required>

                </div>

                <button type="submit" class="newsletter-submit-button">
                    সাবস্ক্রাইব করুন
                    <i class="fa-solid fa-arrow-right"></i>
                </button>

            </form>

            <div class="newsletter-privacy-note">
                <i class="fa-solid fa-lock"></i>
                স্প্যাম নয়, শুধু প্রয়োজনীয় অফার ও আপডেট
            </div>
        </div>

    </section>

</div>

<div class="after-products-container">
    <section class="installment-service-panel mb-3">
        <div class="installment-process">
            <div class="installment-section-title">
                কিস্তি কিভাবে কাজ করে
            </div>
            <div class="installment-process-grid row g-3">
                <div class="col-12 col-md-4">
                    <div class="installment-step-card">
                        <div class="installment-step-number">
                            1
                        </div>
                        <div class="installment-step-content">
                            <strong>পণ্য নির্বাচন করুন</strong>
                            <span>
                                আপনার পছন্দের পণ্য বেছে নিন এবং কিস্তি অপশন সিলেক্ট করুন
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="installment-step-card">
                        <div class="installment-step-number">
                            2
                        </div>
                        <div class="installment-step-content">
                            <strong>সহজ আবেদন</strong>
                            <span>
                                সহজ তথ্য পূরণ করুন ও প্রয়োজনীয় তথ্য প্রদান করুন
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="installment-step-card">
                        <div class="installment-step-number">
                            3
                        </div>
                        <div class="installment-step-content">
                            <strong>অনুমোদন ও গ্রহণ করুন</strong>
                            <span>
                                অনুমোদনের পরে পণ্য গ্রহণ করুন এবং নির্ধারিত কিস্তি পরিশোধ করুন
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="installment-support-grid row g-0">
            <div class="membership-benefits-panel col-12 col-lg-8">
                <div class="installment-section-title">
                    সদস্যতা নিন, উপভোগ করুন বিশেষ সুবিধা!
                </div>
                <div class="membership-benefits-grid row row-cols-2 row-cols-lg-4 g-2">
                    <div class="col">
                        <div class="membership-benefit-item">
                            <div class="membership-benefit-icon green">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            <div>
                                <strong>আরও সহজ EMI</strong>
                                <span>দ্রুত ও সহজ কিস্তি সুবিধা</span>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="membership-benefit-item">
                            <div class="membership-benefit-icon red">
                                <i class="fa-solid fa-percent"></i>
                            </div>
                            <div>
                                <strong>বিশেষ ছাড় ও অফার</strong>
                                <span>সদস্যদের জন্য Extra Discount</span>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="membership-benefit-item">
                            <div class="membership-benefit-icon green">
                                <i class="fa-solid fa-file-circle-check"></i>
                            </div>
                            <div>
                                <strong>সহজ কিস্তি সীমা</strong>
                                <span>বেশি পণ্য কেনার সুবিধা</span>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="membership-benefit-item">
                            <div class="membership-benefit-icon blue">
                                <i class="fa-solid fa-headset"></i>
                            </div>
                            <div>
                                <strong>Priority Support</strong>
                                <span>দ্রুত Customer Support</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="customer-support-panel col-12 col-lg-4">
                <div class="customer-support-content">
                    <div class="installment-section-title">
                        সহায়তা প্রয়োজন?
                    </div>
                    <p>
                        আমাদের সাপোর্ট টিম আপনার পাশে আছে
                    </p>
                    <div class="customer-support-actions">
                        <a class="customer-support-link green" href="#">
                            <i class="fa-solid fa-phone"></i>
                            01733-206458
                        </a>
                        <a class="customer-support-link green" href="#">
                            <i class="fa-brands fa-whatsapp"></i>
                            WhatsApp
                        </a>
                    </div>
                </div>
                <img alt="Support Agent" class="customer-support-agent"
                    src="{{ asset('assets/theme/images/support/customer-support-agent.png') }}" loading="lazy"
                    decoding="async" />
            </div>
        </div>
    </section>
</div>
@endsection
@push('style')
<style>
.home-product-cards .product-col{display:flex}
.home-product-cards .product-col>.catalog-card{width:100%}
.home-product-cards .catalog-image{flex-shrink:0}
</style>
@endpush
@push('script')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const buttons = [...document.querySelectorAll('.home-product-cards [data-save]')];
    if (!buttons.length) return;
    let saved = new Set();
    function readSaved() {
        try {
            const value = JSON.parse(localStorage.getItem('bondon.catalog.saved') || '[]');
            if (Array.isArray(value)) saved = new Set(value.map(String));
        } catch (_) {}
    }
    function sync() {
        buttons.forEach(button => {
            const active = saved.has(button.dataset.save);
            button.setAttribute('aria-pressed', String(active));
            button.querySelector('i').className = (active ? 'fa-solid' : 'fa-regular') + ' fa-heart';
        });
    }
    buttons.forEach(button => button.addEventListener('click', () => {
        readSaved();
        const id = button.dataset.save;
        saved.has(id) ? saved.delete(id) : saved.add(id);
        let message = saved.has(id) ? 'এই ব্রাউজারের পছন্দের তালিকায় রাখা হয়েছে' : 'পছন্দের তালিকা থেকে সরানো হয়েছে';
        try { localStorage.setItem('bondon.catalog.saved', JSON.stringify([...saved])); }
        catch (_) { message = 'এই সেশনের জন্য পছন্দের তালিকা আপডেট হয়েছে'; }
        sync();
        window.notify({ type: 'success', message });
    }));
    window.addEventListener('storage', event => {
        if (event.key === 'bondon.catalog.saved' || event.key === null) { saved.clear(); readSaved(); sync(); }
    });
    readSaved();
    sync();
});
</script>
@endpush
@push('style')
<style>
    .carousel-inner{border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;}
    .hero{width:100%;margin-top:0;position:relative}.hero .carousel,.hero .carousel-inner,.hero .carousel-item,.hero .carousel-item picture{width:100%}.hero .carousel-inner{overflow:hidden}.hero .carousel-item picture{display:block}.hero .carousel-item img{display:block;width:100%;height:auto;object-fit:cover}.hero .carousel-control-prev,.hero .carousel-control-next{width:55px;opacity:1;transition:opacity .25s ease}.hero:hover .carousel-control-prev,.hero:hover .carousel-control-next{opacity:1}.hero-slider-control{width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:#00000059;color:#fff;font-size:13px;backdrop-filter:blur(4px);transition:.2s ease}.hero-slider-control:hover{background:#0a4fc9d9}.hero .carousel-indicators{margin-bottom:10px;gap:5px}.hero .carousel-indicators [data-bs-target]{width:7px;height:7px;margin:0;border:0;border-radius:50%;background-color:#ffffffa6;opacity:1;transition:.25s ease}.hero .carousel-indicators .active{width:22px;border-radius:10px;background-color:#fff}@media (max-width: 767px){.hero .carousel-indicators{margin-bottom:6px}.hero .carousel-indicators [data-bs-target]{width:5px;height:5px}.hero .carousel-indicators .active{width:16px}}

    main {
        position: relative;
        isolation: isolate;
    }

    main::before {
        content: "";
        position: absolute;
        top: 0;
        left: 50%;
        width: 100vw;
        height: 620px;
        transform: translateX(-50%);
        background: url("/assets/theme/images/hero-background-small.png") top center / cover no-repeat;
        z-index: -1;
        pointer-events: none;
    }

</style>
@endpush
