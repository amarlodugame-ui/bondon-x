@extends('theme.layouts.frontend')

@push('style')
<link rel="stylesheet" href="{{ asset('assets/theme/css/support-pages.css') }}">
@endpush

@section('content')
<div class="support-page">
    <section class="support-mini-hero"><div class="support-wrap"><div><span class="support-kicker"><i class="fa-regular fa-id-card"></i> সদস্যতা</span><h1>Verified Member হওয়ার সহজ প্রক্রিয়া</h1><p>Membership এবং KYC একই verification flow-এর অংশ। যখন ভ্যালিড মেম্বার হবে তখন থেকে পন্য কিনতে পারবেন।</p></div></div></section>
    <div class="support-wrap support-layout">
        @include('theme.support.partials.sidebar')
        <main class="support-main">
            <div class="membership-summary">
                <div><span>One-time Membership Fee</span><strong>@if((float)(gs()->membership_fee ?? 0) > 0) ৳{{ number_format((float)gs()->membership_fee, 0) }} @else নির্ধারিত ফি @endif</strong></div>
                <a class="support-btn primary" href="{{ route('user.login') }}">লগইন করে শুরু করুন <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="support-section-head"><div><h2>সদস্যতা কীভাবে সম্পন্ন হবে</h2></div></div>
            <div class="process-grid">
                <div class="process-card"><span>01</span><i class="fa-regular fa-pen-to-square"></i><strong>আবেদন</strong><p>নাম, NID, ঠিকানা, পেশা, ছবি ও প্রয়োজনীয় KYC তথ্য দিন।</p></div>
                <div class="process-card"><span>02</span><i class="fa-solid fa-wallet"></i><strong>Fee Payment</strong><p>Membership fee শুধুমাত্র Main Balance থেকে পরিশোধ হবে।</p></div>
                <div class="process-card"><span>03</span><i class="fa-solid fa-user-shield"></i><strong>Verification</strong><p>Application ও payment সম্পন্ন হলে Admin তথ্য যাচাই করবে।</p></div>
                <div class="process-card"><span>04</span><i class="fa-solid fa-circle-check"></i><strong>Member Active</strong><p>Approve হলে account Verified Member হিসেবে active হবে।</p></div>
            </div>
            <div class="policy-block"><h2>Verification সম্পর্কে গুরুত্বপূর্ণ তথ্য</h2><ul class="check-list"><li><i class="fa-solid fa-check"></i><span>Application + fee complete হওয়ার পর status Pending Verification হবে।</span></li><li><i class="fa-solid fa-check"></i><span>Admin প্রয়োজনীয় KYC/NID information যাচাই করবে।</span></li><li><i class="fa-solid fa-check"></i><span>Application reject হলে rejection reason account-এ দেখানো হবে।</span></li><li><i class="fa-solid fa-check"></i><span>Membership application এবং approval/rejection history সংরক্ষিত থাকবে।</span></li></ul></div>
        </main>
    </div>
</div>
@endsection
