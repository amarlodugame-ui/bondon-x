

@extends('theme.layouts.app')
@section('panel')
    <div class="app">
        <!--  HEADER -->
        @include('theme.partials.auth-header')
        <!-- SIDEBAR  -->
        @include('theme.partials.auth-sidebar')
        <!--   MAIN CONTENT  -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <script>
        /*  ELEMENTS  */
        const body = document.body;
        const sidebarToggle = document.getElementById("sidebarToggle");
        const sidebarOverlay = document.getElementById("sidebarOverlay");
        const orderMenu = document.getElementById("orderMenu");
        const profileWrap = document.getElementById("profileWrap");
        const profileButton = document.getElementById("profileButton");
        const notificationWrap = document.getElementById("notificationWrap");
        const notificationButton = document.getElementById("notificationButton");
        /*   SIDEBAR */
        sidebarToggle.addEventListener("click", () => {
            if (window.innerWidth <= 900) {
                body.classList.toggle("mobile-sidebar-open");
            }
            else {
                body.classList.toggle("sidebar-collapsed");
                localStorage.setItem("sidebarCollapsed",body.classList.contains("sidebar-collapsed"));
            }
        });
        if (localStorage.getItem("sidebarCollapsed") === "true" && window.innerWidth > 900) {
            body.classList.add("sidebar-collapsed");
        }
        /*    MOBILE SIDEBAR OVERLAY   */
        sidebarOverlay.addEventListener( "click",() => { body.classList.remove("mobile-sidebar-open"); });
        /*    SUB MENU  */
        orderMenu.addEventListener(
            "click",
            () => {
                if (body.classList.contains("sidebar-collapsed") && window.innerWidth > 900) {
                    body.classList.remove("sidebar-collapsed");
                }
                orderMenu.classList.toggle("open");
            }
        );
        /*  PROFILE DROPDOWN  */
        profileButton.addEventListener(
            "click",
            (e) => {
                e.stopPropagation();
                profileWrap.classList.toggle("open");
            }
        );
        document.addEventListener(
            "click",
            () => {
                profileWrap.classList.remove("open");
                notificationWrap.classList.remove("open");
            }
        );
        /*  NOTIFICATION DROPDOWN  */
        notificationButton.addEventListener(
            "click",
            (e) => {
                e.stopPropagation();
                profileWrap.classList.remove("open");
                notificationWrap.classList.toggle("open");
            }
        );
        notificationWrap.addEventListener("click",(e) => {e.stopPropagation();});
        /*  MENU ACTIVE STATE  */
        const menuItems = document.querySelectorAll(".sidebar-menu > a.menu-item");
        menuItems.forEach(
            item => {
                item.addEventListener(
                    "click",
                    () => {
                        menuItems.forEach(menu => { menu.classList.remove("active");});
                        item.classList.add("active");
                        if (window.innerWidth <= 900) {
                            body.classList.remove( "mobile-sidebar-open");
                        }
                    }
                );
            }
        );
        /*  BALANCE COUNT ANIMATION  */
        const counters = document.querySelectorAll(".counter");
        counters.forEach(counter => {
            const target = Number( counter.dataset.target);
            const duration = 800;
            const startTime = performance.now();
            function updateCounter(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration,1);
                const eased = 1 - Math.pow(1 - progress,3);
                const value = Math.floor(target * eased);
                counter.textContent ="৳ " + value.toLocaleString("en-US") +".00";
                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                }
            }
            requestAnimationFrame(
                updateCounter
            );
        });
        /*    RESIZE     */
        window.addEventListener(
            "resize",
            () => {
                if (window.innerWidth > 900) {
                    body.classList.remove("mobile-sidebar-open");
                }
            }
        );
    </script>
@endsection

@push('style')
<link rel="stylesheet" href="{{ asset('assets/theme/css/auth-style.css') }}">
@endpush
@push('script')
<script src="{{ asset('assets/theme/js/auth-script.js') }}"></script>
@endpush