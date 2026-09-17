<aside class="support-sidebar">
    <div class="support-sidebar-head">
        <span class="support-sidebar-icon"><i class="fa-solid fa-life-ring"></i></span>
        <div><strong>সহায়তা ও তথ্য</strong><small>প্রয়োজনীয় তথ্য এক জায়গায়</small></div>
    </div>
    <nav class="support-nav">
        <a class="{{ request()->routeIs('support') ? 'active' : '' }}" href="{{ route('support') }}"><i class="fa-solid fa-headset"></i><span>হেল্প সেন্টার</span><i class="fa-solid fa-chevron-right"></i></a>
        <a class="{{ request()->routeIs('faq') ? 'active' : '' }}" href="{{ route('faq') }}"><i class="fa-regular fa-circle-question"></i><span>সাধারণ প্রশ্ন</span><i class="fa-solid fa-chevron-right"></i></a>
        <a class="{{ request()->routeIs('membership') ? 'active' : '' }}" href="{{ route('membership') }}"><i class="fa-regular fa-id-card"></i><span>সদস্যতা</span><i class="fa-solid fa-chevron-right"></i></a>
        <a class="{{ request()->routeIs('policy.return_refund') ? 'active' : '' }}" href="{{ route('policy.return_refund') }}"><i class="fa-solid fa-rotate-left"></i><span>রিটার্ন ও রিফান্ড</span><i class="fa-solid fa-chevron-right"></i></a>
        <a class="{{ request()->routeIs('policy.shipping') ? 'active' : '' }}" href="{{ route('policy.shipping') }}"><i class="fa-solid fa-truck-fast"></i><span>ডেলিভারি নীতি</span><i class="fa-solid fa-chevron-right"></i></a>
        <a class="{{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}"><i class="fa-regular fa-envelope"></i><span>যোগাযোগ</span><i class="fa-solid fa-chevron-right"></i></a>
        <a class="{{ request()->routeIs('policy.terms') ? 'active' : '' }}" href="{{ route('policy.terms') }}"><i class="fa-regular fa-file-lines"></i><span>শর্তাবলী</span><i class="fa-solid fa-chevron-right"></i></a>
        <a class="{{ request()->routeIs('policy.privacy') ? 'active' : '' }}" href="{{ route('policy.privacy') }}"><i class="fa-solid fa-shield-halved"></i><span>গোপনীয়তা নীতি</span><i class="fa-solid fa-chevron-right"></i></a>
    </nav>
</aside>
