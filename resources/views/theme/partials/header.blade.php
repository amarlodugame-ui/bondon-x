    <header class="site-header">
        <div class="container-site">
            <div class="header-inner">
                <button class="menu-toggle" type="button" data-bs-target="#mobileMenu" data-bs-toggle="offcanvas" aria-controls="mobileMenu" aria-label="মেনু খুলুন">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <a class="brand" href="{{ route('home') }}">
                    <img class="brand-badge" src="{{ asset('assets/logo/logo.png') }}" alt="Brand Logo" />
                </a>
                <nav class="desktop-nav">
                    <a class="nav-link-item" href="{{ route('home') }}">হোম</a>
                    <div class="nav-item">
                        <a class="nav-link-item mega-trigger" href="#">
                            ক্যাটাগরি
                            <i class="fa-solid fa-chevron-down"></i>
                        </a>
                        <div class="mega-menu row g-3">
                            <div class="col-3">
                                <div class="mega-col-title">মোবাইল</div>
                                <div class="mega-list">
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
                                        <div class="mega-content">
                                            <strong>Smartphone</strong>
                                            <span>Samsung, iPhone ও Android</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-watch-smart"></i></div>
                                        <div class="mega-content">
                                            <strong>Smart Watch</strong>
                                            <span>Wearable collection</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-headphones"></i></div>
                                        <div class="mega-content">
                                            <strong>Headphone</strong>
                                            <span>Audio accessories</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mega-col-title">কম্পিউটার</div>
                                <div class="mega-list">
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-laptop"></i></div>
                                        <div class="mega-content">
                                            <strong>Laptop</strong>
                                            <span>HP, Dell ও আরও</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-desktop"></i></div>
                                        <div class="mega-content">
                                            <strong>Desktop</strong>
                                            <span>Office ও Gaming PC</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-keyboard"></i></div>
                                        <div class="mega-content">
                                            <strong>Accessories</strong>
                                            <span>Mouse, keyboard, etc.</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mega-col-title">হোম অ্যাপ্লায়েন্স</div>
                                <div class="mega-list">
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-tv"></i></div>
                                        <div class="mega-content">
                                            <strong>Television</strong>
                                            <span>Smart TV collection</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-snowflake"></i></div>
                                        <div class="mega-content">
                                            <strong>Refrigerator</strong>
                                            <span>New fridge models</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-fan"></i></div>
                                        <div class="mega-content">
                                            <strong>Home Appliance</strong>
                                            <span>AC, fan ও electronics</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="mega-promo col-3">
                                <img alt="Offer" src="assets/eid-offer-banner.png" loading="lazy" decoding="async" />
                                <div class="mega-promo-body">
                                    <strong>বিশেষ অফার</strong>
                                    <p>নির্বাচিত পণ্যে বিশেষ ছাড় ও সহজ কিস্তি সুবিধা।</p>
                                    <a href="#">অফার দেখুন <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link-item mega-trigger" href="#">
                            কিস্তিতে কেনাকাটা
                            <i class="fa-solid fa-chevron-down"></i>
                        </a>
                        <div class="mega-menu row g-3">
                            <div class="col-3">
                                <div class="mega-col-title">কিস্তির পণ্য</div>
                                <div class="mega-list">
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-mobile-screen"></i></div>
                                        <div class="mega-content">
                                            <strong>Mobile EMI</strong>
                                            <span>সহজ কিস্তিতে স্মার্টফোন</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-laptop"></i></div>
                                        <div class="mega-content">
                                            <strong>Laptop EMI</strong>
                                            <span>Student ও office laptop</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-tv"></i></div>
                                        <div class="mega-content">
                                            <strong>TV EMI</strong>
                                            <span>Smart TV installment</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mega-col-title">জনপ্রিয় EMI</div>
                                <div class="mega-list">
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
                                        <div class="mega-content">
                                            <strong>iPhone 15</strong>
                                            <span>৳ 5,708 / মাস</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-laptop"></i></div>
                                        <div class="mega-content">
                                            <strong>Dell Inspiron 3530</strong>
                                            <span>৳ 3,083 / মাস</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-snowflake"></i></div>
                                        <div class="mega-content">
                                            <strong>Samsung Refrigerator</strong>
                                            <span>৳ 2,583 / মাস</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mega-col-title">সুবিধা</div>
                                <div class="mega-list">
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-file-pen"></i></div>
                                        <div class="mega-content">
                                            <strong>সহজ আবেদন</strong>
                                            <span>অল্প তথ্যেই শুরু</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-shield-halved"></i></div>
                                        <div class="mega-content">
                                            <strong>নিরাপদ প্রসেস</strong>
                                            <span>Trusted service</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <a class="mega-link" href="#">
                                        <div class="mega-icon"><i class="fa-solid fa-headset"></i></div>
                                        <div class="mega-content">
                                            <strong>সহায়তা</strong>
                                            <span>Support team ready</span>
                                        </div>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="mega-promo col-3">
                                <img alt="Installment" src="assets/shopping-installment-banner.webp" loading="lazy" decoding="async" />
                                <div class="mega-promo-body">
                                    <strong>সহজ কিস্তি</strong>
                                    <p>আপনার পছন্দের পণ্য এখন মাসিক কিস্তিতে।</p>
                                    <a href="#">পণ্য দেখুন <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a class="nav-link-item" href="#">অফার</a>
                    <a class="nav-link-item" href="#">সদস্যতা</a>
                    <a class="nav-link-item" href="#">সহায়তা</a>
                    <a class="nav-link-item" href="#">যোগাযোগ</a>
                </nav>
                <div class="header-search" id="topSearch">
                    <input class="search-input" placeholder="পণ্য খুঁজুন (যেমন: মোবাইল, ল্যাপটপ...)" type="search" aria-label="পণ্য খুঁজুন" autocomplete="off" />
                    <button type="button" aria-label="সার্চ করুন"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button>
                    <div class="search-dropdown">
                        <div class="search-item"><i class="fa-solid fa-magnifying-glass"></i> Samsung Galaxy A55</div>
                        <div class="search-item"><i class="fa-solid fa-magnifying-glass"></i> iPhone 15</div>
                        <div class="search-item"><i class="fa-solid fa-magnifying-glass"></i> HP Core i5 Laptop</div>
                        <div class="search-item"><i class="fa-solid fa-magnifying-glass"></i> Walton 43" Smart TV</div>
                    </div>
                </div>
                <div class="header-actions">
                    <a class="header-btn btn-login" href="{{ route('user.login') }}">লগইন</a>
                    <a class="header-btn btn-register" href="{{ route('user.register') }}">রেজিস্টার</a>
                </div>
            </div>
        </div>
    </header>