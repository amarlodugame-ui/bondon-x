@php

        $categories = App\Models\Category::query()
            ->where('status', 1)
            ->orderBy('sort_order')
            ->select(['id', 'name', 'slug', 'image'])
            ->get();
@endphp

<header class="site-header">
    <div class="container-site">
        <div class="header-inner">
            <button class="menu-toggle" type="button" data-bs-target="#mobileMenu" data-bs-toggle="offcanvas" aria-controls="mobileMenu" aria-label="মেনু খুলুন">
                <i class="fa-solid fa-bars"></i>
            </button>

            <a class="brand" href="{{ route('home') }}">
                <img class="brand-badge" src="{{ asset('assets/logo/logo.png') }}" alt="Bondon">
            </a>

            <nav class="desktop-nav">
                <a class="nav-link-item {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">হোম</a>

                <div class="nav-item">
                    <a class="nav-link-item mega-trigger {{ request()->routeIs('categories', 'products') ? 'active' : '' }}" href="{{ route('categories') }}">
                        ক্যাটাগরি
                        <i class="fa-solid fa-chevron-down"></i>
                    </a>

                    <div class="mega-menu row g-3">
                        @if(isset($categories) && $categories->count())
                            @foreach($categories->take(9)->chunk(3) as $categoryGroup)
                                <div class="col-3">
                                    <div class="mega-col-title">ক্যাটাগরি</div>
                                    <div class="mega-list">
                                        @foreach($categoryGroup as $category)
                                            <a class="mega-link" href="{{ route('products', ['category' => $category->slug]) }}">
                                                <div class="mega-icon">
                                                    @if($category->image)
                                                        <img src="{{ asset('assets/theme/images/categories/' . $category->image) }}" alt="{{ $category->name }}">
                                                    @else
                                                        <i class="fa-solid fa-box"></i>
                                                    @endif
                                                </div>
                                                <div class="mega-content">
                                                    <strong>{{ $category->name }}</strong>
                                                    <span>পণ্য দেখুন</span>
                                                </div>
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-3">
                                <div class="mega-col-title">ক্যাটাগরি</div>
                                <div class="mega-list">
                                    <a class="mega-link" href="{{ route('categories') }}">
                                        <div class="mega-icon"><i class="fa-solid fa-layer-group"></i></div>
                                        <div class="mega-content">
                                            <strong>সকল ক্যাটাগরি</strong>
                                            <span>সব বিভাগ দেখুন</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a class="mega-link" href="{{ route('products') }}">
                                        <div class="mega-icon"><i class="fa-solid fa-box-open"></i></div>
                                        <div class="mega-content">
                                            <strong>সকল পণ্য</strong>
                                            <span>আমাদের সব পণ্য দেখুন</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="mega-promo col-3">
                            <img src="{{ asset('assets/theme/images/eid-offer-banner.png') }}" alt="Offer" loading="lazy" decoding="async">
                            <div class="mega-promo-body">
                                <strong>বিশেষ অফার</strong>
                                <p>নির্বাচিত পণ্যে বিশেষ ছাড় ও সহজ কিস্তি সুবিধা।</p>
                                <a href="{{ route('offers') }}">
                                    অফার দেখুন
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nav-item">
                    <a class="nav-link-item mega-trigger {{ request()->routeIs('installment.*') ? 'active' : '' }}" href="{{ route('installment.products') }}">
                        কিস্তিতে কেনাকাটা
                        <i class="fa-solid fa-chevron-down"></i>
                    </a>

                    <div class="mega-menu row g-3">
                        <div class="col-3">
                            <div class="mega-col-title">কিস্তির পণ্য</div>
                            <div class="mega-list">
                                <!-- <a class="mega-link" href="{{ route('installment.products') }}">
                                    <div class="mega-icon"><i class="fa-solid fa-bag-shopping"></i></div>
                                    <div class="mega-content">
                                        <strong>সকল কিস্তির পণ্য</strong>
                                        <span>কিস্তিতে পাওয়া যায় এমন পণ্য</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a> -->

                                <a class="mega-link" href="{{ route('products', ['installment' => 1]) }}">
                                    <div class="mega-icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
                                    <div class="mega-content">
                                        <strong>সহজ কিস্তি</strong>
                                        <span>পছন্দের পণ্য কিস্তিতে নিন</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="mega-col-title">কিস্তি সম্পর্কে</div>
                            <div class="mega-list">
                                <a class="mega-link" href="{{ route('installment.guide') }}">
                                    <div class="mega-icon"><i class="fa-solid fa-circle-info"></i></div>
                                    <div class="mega-content">
                                        <strong>কিভাবে কাজ করে?</strong>
                                        <span>কিস্তির সম্পূর্ণ নিয়ম জানুন</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>

                                <a class="mega-link" href="{{ route('membership') }}">
                                    <div class="mega-icon"><i class="fa-solid fa-id-card"></i></div>
                                    <div class="mega-content">
                                        <strong>সদস্যতা</strong>
                                        <span>Member হওয়ার বিস্তারিত</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="mega-col-title">সুবিধা</div>
                            <div class="mega-list">
                                <a class="mega-link" href="{{ route('installment.guide') }}">
                                    <div class="mega-icon"><i class="fa-solid fa-calendar-days"></i></div>
                                    <div class="mega-content">
                                        <strong>সহজ পেমেন্ট</strong>
                                        <span>সাপ্তাহিক বা মাসিক কিস্তি</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>

                                <a class="mega-link" href="{{ route('support') }}">
                                    <div class="mega-icon"><i class="fa-solid fa-headset"></i></div>
                                    <div class="mega-content">
                                        <strong>সহায়তা</strong>
                                        <span>প্রয়োজনে আমাদের সাথে যোগাযোগ করুন</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="mega-promo col-3">
                            <img src="{{ asset('assets/theme/images/shopping-installment-banner.webp') }}" alt="Installment" loading="lazy" decoding="async">
                            <div class="mega-promo-body">
                                <strong>সহজ কিস্তি</strong>
                                <p>আপনার পছন্দের পণ্য এখন সহজ কিস্তিতে কিনুন।</p>
                                <a href="{{ route('installment.products') }}">
                                    পণ্য দেখুন
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <a class="nav-link-item {{ request()->routeIs('offers') ? 'active' : '' }}" href="{{ route('offers') }}">অফার</a>
                <div class="nav-item">
                    <a class="nav-link-item mega-trigger {{ request()->routeIs('support', 'faq', 'membership', 'contact', 'policy.*') ? 'active' : '' }}" href="{{ route('support') }}">
                        সহায়তা
                        <i class="fa-solid fa-chevron-down"></i>
                    </a>

                    <div class="mega-menu row g-3">
                        <div class="col-4">
                            <div class="mega-col-title">সহায়তা</div>
                            <div class="mega-list">
                                <a class="mega-link" href="{{ route('support') }}">
                                    <div class="mega-icon"><i class="fa-solid fa-headset"></i></div>
                                    <div class="mega-content">
                                        <strong>হেল্প সেন্টার</strong>
                                        <span>প্রয়োজনীয় সাহায্য নিন</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>

                                <a class="mega-link" href="{{ route('faq') }}">
                                    <div class="mega-icon"><i class="fa-solid fa-circle-question"></i></div>
                                    <div class="mega-content">
                                        <strong>সাধারণ প্রশ্ন</strong>
                                        <span>প্রশ্ন ও উত্তর দেখুন</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>

                                <a class="mega-link" href="{{ route('membership') }}">
                                    <div class="mega-icon"><i class="fa-solid fa-user-group"></i></div>
                                    <div class="mega-content">
                                        <strong>সদস্যতা</strong>
                                        <span>সদস্যতা সম্পর্কে জানুন</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>

                            </div>
                        </div>
                        
                        <div class="col-4">
                            <div class="mega-col-title">নীতিমালা</div>
                            <div class="mega-list">
                                <a class="mega-link" href="{{ route('policy.return_refund') }}">
                                    <div class="mega-icon"><i class="fa-solid fa-rotate-left"></i></div>
                                    <div class="mega-content">
                                        <strong>রিটার্ন ও রিফান্ড</strong>
                                        <span>Return এবং Refund Policy</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>

                                <a class="mega-link" href="{{ route('policy.shipping') }}">
                                    <div class="mega-icon"><i class="fa-solid fa-truck-fast"></i></div>
                                    <div class="mega-content">
                                        <strong>ডেলিভারি নীতি</strong>
                                        <span>Shipping ও Delivery তথ্য</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                                <a class="mega-link" href="{{ route('contact') }}">
                                    <div class="mega-icon"><i class="fa-solid fa-envelope"></i></div>
                                    <div class="mega-content">
                                        <strong>যোগাযোগ</strong>
                                        <span>আমাদের সাথে যোগাযোগ করুন</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="mega-col-title">আরও তথ্য</div>
                            <div class="mega-list">
                                <a class="mega-link" href="{{ route('policy.terms') }}">
                                    <div class="mega-icon"><i class="fa-solid fa-file-lines"></i></div>
                                    <div class="mega-content">
                                        <strong>শর্তাবলী</strong>
                                        <span>Terms & Conditions</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>

                                <a class="mega-link" href="{{ route('policy.privacy') }}">
                                    <div class="mega-icon"><i class="fa-solid fa-shield-halved"></i></div>
                                    <div class="mega-content">
                                        <strong>গোপনীয়তা</strong>
                                        <span>Privacy Policy</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <form class="header-search" id="topSearch" action="{{ route('products') }}" method="GET" data-suggestions-url="{{ route('products.suggestions') }}">
                <input class="search-input" name="search" placeholder="পণ্য খুঁজুন..." type="search" aria-label="পণ্য খুঁজুন" autocomplete="off" maxlength="160">
                <button type="submit" aria-label="সার্চ করুন">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                </button>
                <div class="search-dropdown"></div>
            </form>

            <div class="header-actions">
                <a class="header-cart-btn" href="{{ route('cart.index') }}" aria-label="কার্ট">
                    <i class="fa-solid fa-cart-shopping"></i>
                </a>

                @guest
                    <a class="header-btn btn-login" href="{{ route('user.login') }}">লগইন</a>
                    <a class="header-btn btn-register" href="{{ route('user.register') }}">রেজিস্টার</a>
                @else
                    <a class="header-btn btn-login" href="#">
                        <i class="fa-solid fa-user"></i>
                        অ্যাকাউন্ট
                    </a>
                @endguest
            </div>
        </div>
    </div>
</header>