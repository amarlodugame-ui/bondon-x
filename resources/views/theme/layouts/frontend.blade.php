@extends('theme.layouts.app')
@section('panel')
    <!-- HEADER -->
    @include('theme.partials.header')
    <!-- HEADER END -->

    <main>
        <div class="container site-container">
            @yield('content')
        </div>
    </main>

    <!-- FOOTER -->
    @include('theme.partials.footer')

    <!-- MOBILE MENU -->
    @include('theme.partials.mobile-menu')

    <!-- MOBILE BOTTOM NAV -->
    @include('theme.partials.mobile-bottom-nav')
@endsection

@push('style')
<link rel="stylesheet" href="{{ asset('assets/theme/css/style.css') }}">
@endpush
@push('script')
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
@endpush