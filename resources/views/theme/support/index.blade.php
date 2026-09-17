@extends('theme.layouts.frontend')

@push('style')
<link rel="stylesheet" href="{{ asset('assets/theme/css/support-pages.css') }}">
@endpush

@section('content')
<div class="support-page">
    <section class="support-hero">
        <div class="support-wrap">
            <div class="support-hero-copy">
                <span class="support-kicker"><i class="fa-solid fa-headset"></i> সহায়তা কেন্দ্র</span>
                <h1>যে তথ্য দরকার, সহজেই খুঁজে নিন</h1>
                <p>কেনাকাটা, কিস্তি, সদস্যতা, ডেলিভারি, রিটার্ন ও অ্যাকাউন্ট সংক্রান্ত গুরুত্বপূর্ণ তথ্য এখানে সাজানো আছে।</p>
                <div class="support-hero-actions">
                    <a class="support-btn primary" href="{{ route('faq') }}">সাধারণ প্রশ্ন দেখুন <i class="fa-solid fa-arrow-right"></i></a>
                    <a class="support-btn light" href="{{ route('contact') }}">যোগাযোগ করুন</a>
                </div>
            </div>
        </div>
    </section>

    <div class="support-wrap support-layout">
        @include('theme.support.partials.sidebar')
        <main class="support-main">
            <div class="support-section-head"><div><span>দ্রুত সহায়তা</span><h2>কী বিষয়ে জানতে চান?</h2></div><p>সবচেয়ে বেশি প্রয়োজন হয় এমন বিষয়গুলো বেছে নিন।</p></div>
            <div class="support-card-grid">
                <a class="support-card" href="{{ route('faq') }}"><span class="support-card-icon blue"><i class="fa-regular fa-circle-question"></i></span><strong>সাধারণ প্রশ্ন</strong><p>Wallet, payment, order ও installment নিয়ে দ্রুত উত্তর।</p><span class="support-card-link">উত্তর দেখুন <i class="fa-solid fa-arrow-right"></i></span></a>
                <a class="support-card" href="{{ route('membership') }}"><span class="support-card-icon green"><i class="fa-regular fa-id-card"></i></span><strong>সদস্যতা</strong><p>Membership fee, KYC এবং verification process সম্পর্কে জানুন।</p><span class="support-card-link">বিস্তারিত দেখুন <i class="fa-solid fa-arrow-right"></i></span></a>
                <a class="support-card" href="{{ route('policy.shipping') }}"><span class="support-card-icon orange"><i class="fa-solid fa-truck-fast"></i></span><strong>ডেলিভারি</strong><p>Shipping charge, courier, tracking এবং delivery status জানুন।</p><span class="support-card-link">ডেলিভারি তথ্য <i class="fa-solid fa-arrow-right"></i></span></a>
                <a class="support-card" href="{{ route('policy.return_refund') }}"><span class="support-card-icon purple"><i class="fa-solid fa-rotate-left"></i></span><strong>রিটার্ন ও রিফান্ড</strong><p>Return request, verification ও wallet refund process দেখুন।</p><span class="support-card-link">নীতিমালা দেখুন <i class="fa-solid fa-arrow-right"></i></span></a>
            </div>

            <section class="support-callout">
                <div><span class="support-callout-icon"><i class="fa-regular fa-message"></i></span><div><strong>উত্তর খুঁজে পাচ্ছেন না?</strong><p>আপনার প্রশ্ন বিস্তারিত লিখে পাঠান। সাপোর্ট টিম প্রয়োজন অনুযায়ী সহায়তা করবে।</p></div></div>
                <a href="{{ route('contact') }}">যোগাযোগ করুন <i class="fa-solid fa-arrow-right"></i></a>
            </section>
        </main>
    </div>
</div>
@endsection
