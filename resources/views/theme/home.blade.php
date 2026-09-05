<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>বন্ডন গ্রুপ</title>
    <meta name="description" content="বন্ডন গ্রুপে মোবাইল, ল্যাপটপ, ইলেকট্রনিক্স ও দৈনন্দিন প্রয়োজনীয় পণ্য সহজে খুঁজুন, অফার দেখুন এবং কিস্তি সুবিধায় কেনাকাটা করুন।" />
    <meta name="robots" content="index,follow" />
    <link rel="preload" as="image" href="assets/shopping-installment-banner.webp" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&amp;family=Inter:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet" />


    <link rel="stylesheet" href="{{ asset('assets/theme/css/style.css') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>
    <!--   HEADER  -->
        @include('theme.partials.header')
    <!--   HEADER END  -->
    <!--   HERO  -->
    <main>
        <div class="container-site">
            <section class="hero">
                <picture>
                    <img src="{{ asset('assets/theme/images/banner/shopping-installment-banner.webp') }}" alt="কিস্তিতে কেনাকাটা" fetchpriority="high" decoding="async">
                </picture>
            </section>
            <div class="discovery-stack search-only-stack">
                <!--  MAIN SEARCH  -->
                <section class="main-search">
                    <div class="main-search-inner">
                        <div class="main-search-box" id="mainSearch">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input class="search-input" placeholder="পণ্য খুঁজুন (যেমন: মোবাইল, ল্যাপটপ, ফ্রিজ...)"
                                type="search" aria-label="পণ্য খুঁজুন" autocomplete="off" />
                            <button><span class="search-btn-text">খুঁজুন</span><i
                                    class="fa-solid fa-magnifying-glass search-btn-icon"></i></button>
                            <div class="search-dropdown">
                                <div class="search-item"><i class="fa-solid fa-magnifying-glass"></i> Samsung Galaxy A55
                                </div>
                                <div class="search-item"><i class="fa-solid fa-magnifying-glass"></i> iPhone 15</div>
                                <div class="search-item"><i class="fa-solid fa-magnifying-glass"></i> Dell Inspiron 3530
                                </div>
                                <div class="search-item"><i class="fa-solid fa-magnifying-glass"></i> Samsung
                                    Refrigerator</div>
                            </div>
                        </div>
                        <div class="popular-tags">
                            <span>জনপ্রিয়:</span>
                            @foreach($randomCategories as $category)
                                <a class="tag" href=" ">{{ $category->name }}</a>
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
            <!--   CATEGORY -->
            <section class="section category-section">
                <div class="section-head">
                    <h2>শ্রেণি বিভাগ</h2>
                    <a href="#">সব দেখুন <i class="fa-solid fa-chevron-right"></i></a>
                </div>
                <div class="category-grid row row-cols-7 row-cols-lg-10 g-2">
                    @foreach($categories as $category)
                        <div class="col category-col">
                            <a class="category-card" href="">
                                <img class="category-icon" src="{{ asset('assets/theme/images/categories/' . $category->image) }}" alt="{{ $category->name }}" />
                                <div class="category-title">{{ $category->name }}</div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
        <!--  PRODUCT SECTIONS  -->
        <section class="product-section-band">
            <div class="container-site">
                <section class="stacked-product-block popular-products-block">
                    <div class="section-head product-section-head">
                        <h2>জনপ্রিয় পণ্য</h2>
                        <a href="#">সব দেখুন <i class="fa-solid fa-chevron-right"></i></a>
                    </div>
                    <div class="product-grid row g-2 g-lg-3">
                        <div class="col-4 col-lg-2 product-col">
                            <div class="product-card">
                                <span class="product-badge">10% ছাড়</span>
                                <div class="product-image"><img alt="Samsung Galaxy A55"
                                        src="assets/samsung-galaxy-a55.png" loading="lazy" decoding="async" /></div>
                                <div class="product-body">
                                    <div class="product-name">Samsung Galaxy A55 5G</div>
                                    <div class="product-brand">Samsung</div>
                                    <div class="product-price"><strong>৳ 34,990</strong><del>৳ 38,990</del></div>
                                    <div class="installment-info"><i class="fa-solid fa-circle-check"></i> কিস্তি সুবিধা
                                        আছে</div>
                                    <button class="product-btn">বিস্তারিত দেখুন</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-lg-2 product-col">
                            <div class="product-card">
                                <span class="product-badge">10% ছাড়</span>
                                <div class="product-image"><img alt="HP Laptop" src="assets/hp-i5-laptop.png" loading="lazy" decoding="async" /></div>
                                <div class="product-body">
                                    <div class="product-name">HP 15s Core i5 12th Gen</div>
                                    <div class="product-brand">HP</div>
                                    <div class="product-price"><strong>৳ 52,900</strong><del>৳ 58,900</del></div>
                                    <div class="installment-info"><i class="fa-solid fa-circle-check"></i> কিস্তি সুবিধা
                                        আছে</div>
                                    <button class="product-btn">বিস্তারিত দেখুন</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-lg-2 product-col">
                            <div class="product-card">
                                <span class="product-badge">15% ছাড়</span>
                                <div class="product-image"><img alt="Walton TV" src="assets/walton-smart-tv.png" loading="lazy" decoding="async" />
                                </div>
                                <div class="product-body">
                                    <div class="product-name">Walton 43" FHD Smart TV</div>
                                    <div class="product-brand">Walton</div>
                                    <div class="product-price"><strong>৳ 28,990</strong><del>৳ 33,990</del></div>
                                    <div class="installment-info"><i class="fa-solid fa-circle-check"></i> কিস্তি সুবিধা
                                        আছে</div>
                                    <button class="product-btn">বিস্তারিত দেখুন</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-lg-2 product-col">
                            <div class="product-card">
                                <span class="product-badge">8% ছাড়</span>
                                <div class="product-image"><img alt="A4Tech Headphone"
                                        src="assets/a4tech-hs50-headphone.png" loading="lazy" decoding="async" /></div>
                                <div class="product-body">
                                    <div class="product-name">A4Tech HS-50 Headphone</div>
                                    <div class="product-brand">A4Tech</div>
                                    <div class="product-price"><strong>৳ 1,350</strong><del>৳ 1,650</del></div>
                                    <div class="installment-info"><i class="fa-solid fa-circle-check"></i> কিস্তি সুবিধা
                                        আছে</div>
                                    <button class="product-btn">বিস্তারিত দেখুন</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-lg-2 product-col">
                            <div class="product-card">
                                <span class="product-badge">7% ছাড়</span>
                                <div class="product-image"><img alt="iPhone 15" src="assets/iphone-15.png" loading="lazy" decoding="async" /></div>
                                <div class="product-body">
                                    <div class="product-name">iPhone 15 (128GB)</div>
                                    <div class="product-brand">Apple</div>
                                    <div class="product-price"><strong>৳ 68,500</strong></div>
                                    <div class="installment-info"><i class="fa-solid fa-circle-check"></i> কিস্তি সুবিধা
                                        আছে</div>
                                    <button class="product-btn">বিস্তারিত দেখুন</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-lg-2 product-col">
                            <div class="product-card">
                                <span class="product-badge">9% ছাড়</span>
                                <div class="product-image"><img alt="Dell Inspiron 3530"
                                        src="assets/dell-inspiron-3530.png" loading="lazy" decoding="async" /></div>
                                <div class="product-body">
                                    <div class="product-name">Dell Inspiron 3530</div>
                                    <div class="product-brand">Dell</div>
                                    <div class="product-price"><strong>৳ 36,990</strong></div>
                                    <div class="installment-info"><i class="fa-solid fa-circle-check"></i> কিস্তি সুবিধা
                                        আছে</div>
                                    <button class="product-btn">বিস্তারিত দেখুন</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="stacked-product-block installment-products-block">
                    <div class="section-head product-section-head">
                        <h2>কিস্তিতে জনপ্রিয় পণ্য</h2>
                        <a href="#">সব দেখুন <i class="fa-solid fa-chevron-right"></i></a>
                    </div>
                    <div class="product-grid row g-2 g-lg-3">
                        <div class="col-4 col-lg-2 product-col">
                            <div class="product-card">
                                <span class="product-badge installment">কিস্তিতে</span>
                                <span class="product-brand installment">Apple</span>
                                <div class="product-image"><img alt="iPhone 15" src="assets/iphone-15.png" loading="lazy" decoding="async" /></div>
                                <div class="product-body">
                                    <div class="product-name">iPhone 15 (128GB)</div>
                                    <div class="product-price"><strong>৳ 68,500</strong></div>
                                    <div class="monthly-text">মাসিক শুরু <strong>৳ 5,708</strong></div>
                                    <div class="installment-info"><i class="fa-solid fa-circle-check"></i> ১২ মাসের কিস্তি</div>
                                    <button class="product-btn">বিস্তারিত দেখুন</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-lg-2 product-col">
                            <div class="product-card">
                                <span class="product-badge installment">কিস্তিতে</span>
                                <div class="product-image"><img alt="Dell Laptop" src="assets/dell-inspiron-3530.png" loading="lazy" decoding="async" />
                                </div>
                                <div class="product-body">
                                    <div class="product-name">Dell Inspiron 3530</div>
                                    <div class="product-brand">Dell</div>
                                    <div class="product-price"><strong>৳ 36,990</strong></div>
                                    <div class="monthly-text">মাসিক শুরু <strong>৳ 3,083</strong></div>
                                    <div class="installment-info"><i class="fa-solid fa-circle-check"></i> ১২ মাসের
                                        কিস্তি</div>
                                    <button class="product-btn">বিস্তারিত দেখুন</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-lg-2 product-col">
                            <div class="product-card">
                                <span class="product-badge installment">কিস্তিতে</span>
                                <div class="product-image"><img alt="Samsung Refrigerator"
                                        src="assets/samsung-refrigerator-253l.png" loading="lazy" decoding="async" /></div>
                                <div class="product-body">
                                    <div class="product-name">Samsung Refrigerator 253L</div>
                                    <div class="product-brand">Samsung</div>
                                    <div class="product-price"><strong>৳ 30,990</strong></div>
                                    <div class="monthly-text">মাসিক শুরু <strong>৳ 2,583</strong></div>
                                    <div class="installment-info"><i class="fa-solid fa-circle-check"></i> ১২ মাসের
                                        কিস্তি</div>
                                    <button class="product-btn">বিস্তারিত দেখুন</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-lg-2 product-col">
                            <div class="product-card">
                                <span class="product-badge installment">কিস্তিতে</span>
                                <div class="product-image"><img alt="HP Laptop" src="assets/hp-i5-laptop.png" loading="lazy" decoding="async" /></div>
                                <div class="product-body">
                                    <div class="product-name">HP Core i5 Laptop</div>
                                    <div class="product-brand">HP</div>
                                    <div class="product-price"><strong>৳ 52,900</strong></div>
                                    <div class="monthly-text">মাসিক শুরু <strong>৳ 4,408</strong></div>
                                    <div class="installment-info"><i class="fa-solid fa-circle-check"></i> ১২ মাসের
                                        কিস্তি</div>
                                    <button class="product-btn">বিস্তারিত দেখুন</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-lg-2 product-col">
                            <div class="product-card">
                                <span class="product-badge installment">কিস্তিতে</span>
                                <div class="product-image"><img alt="Samsung Galaxy A55"
                                        src="assets/samsung-galaxy-a55.png" loading="lazy" decoding="async" /></div>
                                <div class="product-body">
                                    <div class="product-name">Samsung Galaxy A55 5G</div>
                                    <div class="product-brand">Samsung</div>
                                    <div class="product-price"><strong>৳ 34,990</strong></div>
                                    <div class="monthly-text">মাসিক শুরু <strong>৳ 2,916</strong></div>
                                    <div class="installment-info"><i class="fa-solid fa-circle-check"></i> ১২ মাসের
                                        কিস্তি</div>
                                    <button class="product-btn">বিস্তারিত দেখুন</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-lg-2 product-col">
                            <div class="product-card">
                                <span class="product-badge installment">কিস্তিতে</span>
                                <div class="product-image"><img alt="Walton Smart TV"
                                        src="assets/walton-smart-tv.png" loading="lazy" decoding="async" /></div>
                                <div class="product-body">
                                    <div class="product-name">Walton 43" FHD Smart TV</div>
                                    <div class="product-brand">Walton</div>
                                    <div class="product-price"><strong>৳ 28,990</strong></div>
                                    <div class="monthly-text">মাসিক শুরু <strong>৳ 2,416</strong></div>
                                    <div class="installment-info"><i class="fa-solid fa-circle-check"></i> ১২ মাসের
                                        কিস্তি</div>
                                    <button class="product-btn">বিস্তারিত দেখুন</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </section>
        <div class="container-site catalog-highlight-sections">
            <!--  FLASH SALE  -->
            <section class="flash-sale-wrap" id="flashSaleCountdown" data-deadline="2026-09-15T23:59:59">

                <div class="flash-sale-left">
                    <h2>ফ্ল্যাশ সেল</h2>
                    <p>সীমিত সময়ের জন্য নির্বাচিত পণ্যে বিশেষ ছাড়</p>
                </div>

                <div class="flash-sale-products">

                    <a href="#" class="flash-sale-card">
                        <div class="flash-sale-product-image">
                            <img src="assets/a4tech-hs50-headphone.png" alt="A4Tech HS50" loading="lazy" decoding="async">
                        </div>
                        <div class="flash-sale-product-info">
                            <h3>A4Tech HS50 Headphone</h3>
                            <div class="flash-sale-product-price">
                                <strong>৳ 1,299</strong>
                                <del>৳ 1,990</del>
                            </div>
                            <span class="flash-sale-discount">35% OFF</span>
                        </div>
                    </a>

                    <a href="#" class="flash-sale-card">
                        <div class="flash-sale-product-image">
                            <img src="assets/samsung-galaxy-a55.png" alt="Samsung Galaxy A55" loading="lazy" decoding="async">
                        </div>
                        <div class="flash-sale-product-info">
                            <h3>Samsung Galaxy A55 5G</h3>
                            <div class="flash-sale-product-price">
                                <strong>৳ 34,990</strong>
                                <del>৳ 36,500</del>
                            </div>
                            <span class="flash-sale-discount">18% OFF</span>
                        </div>
                    </a>

                    <a href="#" class="flash-sale-card">
                        <div class="flash-sale-product-image">
                            <img src="assets/walton-smart-tv.png" alt="Walton Smart TV" loading="lazy" decoding="async">
                        </div>
                        <div class="flash-sale-product-info">
                            <h3>Walton 43&quot; FHD Smart TV</h3>
                            <div class="flash-sale-product-price">
                                <strong>৳ 28,990</strong>
                                <del>৳ 31,500</del>
                            </div>
                            <span class="flash-sale-discount">12% OFF</span>
                        </div>
                    </a>

                </div>

                <div class="flash-sale-timer">
                    <small>অফার শেষ হতে বাকি</small>

                    <div class="flash-sale-time">
                        <div class="flash-sale-time-box">
                            <strong id="flashDays">00</strong>
                            <span>দিন</span>
                        </div>
                        <div class="flash-sale-time-box">
                            <strong id="flashHours">00</strong>
                            <span>ঘণ্টা</span>
                        </div>
                        <div class="flash-sale-time-box">
                            <strong id="flashMinutes">00</strong>
                            <span>মিনিট</span>
                        </div>
                        <div class="flash-sale-time-box">
                            <strong id="flashSeconds">00</strong>
                            <span>সেকেন্ড</span>
                        </div>
                    </div>

                    <a href="#" class="flash-sale-btn">
                        সব ফ্ল্যাশ সেল দেখুন
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

            </section>
        </div>

        <div class="container-site homepage-commerce-sections">

            <!--   SPECIAL OFFER -->
            <section class="homepage-content-section">

                <div class="homepage-section-header">
                    <h2>বিশেষ অফার</h2>

                    <a href="#">
                        সব অফার দেখুন
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
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

                            <img
                                src="assets/iphone-15.png"
                                alt="iPhone Offer"
                                class="promotional-phone-image" loading="lazy" decoding="async">

                            <img
                                src="assets/dell-inspiron-3530.png"
                                alt="Laptop Offer"
                                class="promotional-laptop-image" loading="lazy" decoding="async">

                            <img
                                src="assets/a4tech-hs50-headphone.png"
                                alt="Headphone Offer"
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

                            <img
                                src="assets/samsung-refrigerator-253l.png"
                                alt="Refrigerator Offer"
                                class="promotional-appliance-image" loading="lazy" decoding="async">

                            <div class="promotional-shopping-bag">
                                <i
                                    class="fa-solid fa-bag-shopping"
                                    style="font-size:22px;margin-bottom:6px;"></i>
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

                            <button
                                type="button"
                                class="customer-review-nav-button customer-review-prev"
                                aria-label="Previous review">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>

                            <button
                                type="button"
                                class="customer-review-nav-button customer-review-next"
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

                            <i
                                class="fa-solid fa-quote-right customer-review-quote"></i>

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
                                    <img
                                        src="assets/samsung-galaxy-a55.png"
                                        alt="Samsung Galaxy" loading="lazy" decoding="async">
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

                            <i
                                class="fa-solid fa-quote-right customer-review-quote"></i>

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
                                    <img
                                        src="assets/iphone-15.png"
                                        alt="iPhone 15" loading="lazy" decoding="async">
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

                            <i
                                class="fa-solid fa-quote-right customer-review-quote"></i>

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
                                    <img
                                        src="assets/hp-i5-laptop.png"
                                        alt="HP Laptop" loading="lazy" decoding="async">
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

                            <i
                                class="fa-solid fa-quote-right customer-review-quote"></i>

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
                                    <img
                                        src="assets/walton-smart-tv.png"
                                        alt="Walton Smart TV" loading="lazy" decoding="async">
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


                    <form
                        class="newsletter-form"
                        action="#"
                        method="post">

                        <div class="newsletter-input-wrapper">

                            <i class="fa-regular fa-envelope"></i>

                            <input
                                type="text"
                                name="subscriber"
                                placeholder="আপনার ইমেইল বা মোবাইল নম্বর লিখুন"
                                aria-label="ইমেইল বা মোবাইল নম্বর"
                                autocomplete="email"
                                required>

                        </div>

                        <button
                            type="submit"
                            class="newsletter-submit-button">
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

        <div class="container-site after-products-container">
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
                    <div class="membership-benefits-panel col-12 col-lg-7">
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
                    <div class="customer-support-panel col-12 col-lg-5">
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
                        <img alt="Support Agent" class="customer-support-agent" src="{{ asset('assets/theme/images/support/customer-support-agent.png') }}" loading="lazy" decoding="async" />
                    </div>
                </div>
            </section>
        </div>
    </main>
    <!--  FOOTER  -->
    @include('theme.partials.footer')
    <!-- MOBILE MENU -->
    @include('theme.partials.mobile-menu')

    <!--   MOBILE BOTTOM NAV  -->
    @include('theme.partials.mobile-bottom-nav')

    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /* =====================================================
           MEGA MENU
        ===================================================== */
        const navItems = document.querySelectorAll(".nav-item");
        navItems.forEach(item => {
            const trigger = item.querySelector(".mega-trigger");
            let openTimer;
            let closeTimer;
            item.addEventListener("mouseenter", () => {
                clearTimeout(closeTimer);
                openTimer = setTimeout(() => {
                    navItems.forEach(other => {
                        if (other !== item) {
                            other.classList.remove("open");
                        }
                    });
                    item.classList.add("open");
                }, 80);
            });
            item.addEventListener("mouseleave", () => {
                clearTimeout(openTimer);
                closeTimer = setTimeout(() => {
                    item.classList.remove("open");
                }, 120);
            });
            trigger.addEventListener("click", e => {
                e.preventDefault();
                const isOpen = item.classList.contains("open");
                navItems.forEach(other => other.classList.remove("open"));
                if (!isOpen) {
                    item.classList.add("open");
                }
            });
        });
        document.addEventListener("click", e => {
            if (!e.target.closest(".nav-item")) {
                navItems.forEach(item => item.classList.remove("open"));
            }
        });
        /* =====================================================
           SEARCH DROPDOWN
        ===================================================== */
        const searchAreas = document.querySelectorAll("#topSearch, #mainSearch");
        searchAreas.forEach(area => {
            const input = area.querySelector(".search-input");
            const dropdown = area.querySelector(".search-dropdown");
            const items = area.querySelectorAll(".search-item");
            input.addEventListener("focus", () => {
                dropdown.classList.add("show");
            });
            input.addEventListener("input", () => {
                dropdown.classList.add("show");
            });
            items.forEach(item => {
                item.addEventListener("click", () => {
                    input.value = item.textContent.trim();
                    dropdown.classList.remove("show");
                });
            });
        });
        document.addEventListener("click", e => {
            searchAreas.forEach(area => {
                if (!area.contains(e.target)) {
                    const dropdown = area.querySelector(".search-dropdown");
                    dropdown.classList.remove("show");
                }
            });
        });
        /* =====================================================
           MOBILE CATEGORY PANEL
        ===================================================== */
        const mobileMenu = document.getElementById("mobileMenu");
        const mobileCategoryBack = document.getElementById("mobileCategoryBack");
        const mobileCategoryTriggers = document.querySelectorAll(".mobile-category-trigger");
        mobileCategoryTriggers.forEach(trigger => {
            trigger.addEventListener("click", () => {
                mobileMenu.classList.add("category-mode");
            });
        });
        mobileCategoryBack.addEventListener("click", () => {
            mobileMenu.classList.remove("category-mode");
        });
        mobileMenu.addEventListener("hidden.bs.offcanvas", () => {
            mobileMenu.classList.remove("category-mode");
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const slider = document.getElementById('customerReviewsSlider');
            const prevBtn = document.querySelector('.customer-review-prev');
            const nextBtn = document.querySelector('.customer-review-next');

            if (!slider || !prevBtn || !nextBtn) {
                return;
            }

            function getScrollAmount() {
                const card = slider.querySelector('.customer-review-card');

                if (!card) {
                    return 300;
                }

                const track = slider.querySelector('.customer-reviews-track');
                const gap = parseFloat(
                    window.getComputedStyle(track).gap
                ) || 14;

                return card.offsetWidth + gap;
            }

            nextBtn.addEventListener('click', function() {

                slider.scrollBy({
                    left: getScrollAmount(),
                    behavior: 'smooth'
                });

            });

            prevBtn.addEventListener('click', function() {

                slider.scrollBy({
                    left: -getScrollAmount(),
                    behavior: 'smooth'
                });

            });


            /* Mouse Drag Support */
            let isDown = false;
            let startX;
            let scrollLeft;

            slider.addEventListener('mousedown', function(e) {

                isDown = true;

                slider.style.cursor = 'grabbing';

                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;

            });

            slider.addEventListener('mouseleave', function() {

                isDown = false;
                slider.style.cursor = 'grab';

            });

            slider.addEventListener('mouseup', function() {

                isDown = false;
                slider.style.cursor = 'grab';

            });

            slider.addEventListener('mousemove', function(e) {

                if (!isDown) return;

                e.preventDefault();

                const x = e.pageX - slider.offsetLeft;

                const walk = (x - startX) * 1.3;

                slider.scrollLeft = scrollLeft - walk;

            });

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flashSale = document.getElementById('flashSaleCountdown');

            if (!flashSale) return;

            const deadline = flashSale.getAttribute('data-deadline');
            const dayEl = document.getElementById('flashDays');
            const hourEl = document.getElementById('flashHours');
            const minuteEl = document.getElementById('flashMinutes');
            const secondEl = document.getElementById('flashSeconds');

            function formatNumber(number) {
                return String(number).padStart(2, '0');
            }

            function updateCountdown() {
                const endTime = new Date(deadline).getTime();
                const now = new Date().getTime();
                const distance = endTime - now;

                if (distance <= 0) {
                    dayEl.textContent = '00';
                    hourEl.textContent = '00';
                    minuteEl.textContent = '00';
                    secondEl.textContent = '00';
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                dayEl.textContent = formatNumber(days);
                hourEl.textContent = formatNumber(hours);
                minuteEl.textContent = formatNumber(minutes);
                secondEl.textContent = formatNumber(seconds);
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
        });
    </script>
</body>

</html>