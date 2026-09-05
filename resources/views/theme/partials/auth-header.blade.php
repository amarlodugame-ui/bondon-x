<!-- HEADER -->
        <header class="topbar">
            <div class="topbar-left">
                <div class="brand">
                    <img src="{{ asset('assets/logo/logo.png') }}" alt="Brand Logo" class="brand-mark">
                </div>
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
            <div class="topbar-right">
                <div class="notification-wrap" id="notificationWrap">
                    <button class="notification-btn" id="notificationButton">
                        <i class="fa-solid fa-bell"></i>
                        <span class="notification-count">
                            2
                        </span>
                    </button>
                    <div class="notification-dropdown">
                        <div class="notification-header">
                            <div>
                                নোটিফিকেশন
                            </div>
                            <button class="mark-read-btn">
                                সব পড়া হয়েছে
                            </button>
                        </div>
                        <div class="notification-list">
                            <a href="#" class="notification-item unread">
                                <div class="notification-icon notification-green">
                                    <i class="fa-solid fa-wallet"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">
                                        জমা সফল হয়েছে
                                    </div>
                                    <div class="notification-text">
                                        আপনার অ্যাকাউন্টে ৳500.00 যোগ হয়েছে।
                                    </div>
                                    <div class="notification-time">
                                        ২ মিনিট আগে
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="notification-item unread">
                                <div class="notification-icon notification-purple">
                                    <i class="fa-solid fa-gift"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">
                                        নতুন কমিশন পেয়েছেন
                                    </div>
                                    <div class="notification-text">
                                        রেফারেল থেকে ৳150 কমিশন যোগ হয়েছে।
                                    </div>
                                    <div class="notification-time">
                                        ২৫ মিনিট আগে
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="notification-item">
                                <div class="notification-icon notification-blue">
                                    <i class="fa-solid fa-box"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">
                                        অর্ডার Approved
                                    </div>
                                    <div class="notification-text">
                                        আপনার #ORD-45896 অর্ডারটি অনুমোদিত হয়েছে।
                                    </div>
                                    <div class="notification-time">
                                        ১ ঘণ্টা আগে
                                    </div>
                                </div>
                            </a>
                        </div>
                        <a href="#" class="notification-footer">
                            সব নোটিফিকেশন দেখুন
                        </a>
                    </div>
                </div>
                <div class="profile-wrap" id="profileWrap">
                    <button class="profile-button" id="profileButton">
                        <img src="assets/user-profile-avatar.png" class="profile-avatar" alt="Profile">
                        <div class="profile-data">
                            <span class="profile-name">
                                ওমর ফারুক হাসান
                            </span>
                            <span class="profile-id">
                                ID: WEBIT123
                            </span>
                        </div>
                        <i class="fa-solid fa-chevron-down profile-arrow"></i>
                    </button>
                    <div class="profile-dropdown">
                        <a href="#">
                            <i class="fa-regular fa-user"></i>
                            আমার প্রোফাইল
                        </a>
                        <a href="#">
                            <i class="fa-solid fa-gear"></i>
                            সেটিংস
                        </a>
                        <a href="#">
                            <i class="fa-solid fa-shield-halved"></i>
                            সিকিউরিটি
                        </a>
                        <a href="#">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            লগআউট
                        </a>
                    </div>
                </div>
            </div>
        </header>
<!-- END HEADER -->