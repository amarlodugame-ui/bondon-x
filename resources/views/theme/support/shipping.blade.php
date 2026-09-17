@extends('theme.layouts.frontend')

@push('style')
<link rel="stylesheet" href="{{ asset('assets/theme/css/support-pages.css') }}">
@endpush

@section('content')
<div class="support-page">
    <section class="support-mini-hero"><div class="support-wrap"><div><span class="support-kicker"><i class="fa-solid fa-truck-fast"></i> শিপিং ও ডেলিভারি</span><h1>অর্ডার থেকে ডেলিভারি পর্যন্ত প্রতিটি ধাপ ট্র্যাক করুন</h1><p>Shipping zone অনুযায়ী charge নির্ধারিত হবে। Courier assign হলে tracking information এবং delivery status account থেকে দেখা যাবে।</p></div></div></section>
    <div class="support-wrap support-layout">
        @include('theme.support.partials.sidebar')
        <main class="support-main">
            <div class="delivery-flow"><div class="delivery-step active"><span><i class="fa-solid fa-receipt"></i></span><strong>Order</strong></div><i class="fa-solid fa-chevron-right"></i><div class="delivery-step"><span><i class="fa-solid fa-box"></i></span><strong>Processing</strong></div><i class="fa-solid fa-chevron-right"></i><div class="delivery-step"><span><i class="fa-solid fa-truck"></i></span><strong>Shipped</strong></div><i class="fa-solid fa-chevron-right"></i><div class="delivery-step"><span><i class="fa-solid fa-location-dot"></i></span><strong>In Transit</strong></div><i class="fa-solid fa-chevron-right"></i><div class="delivery-step"><span><i class="fa-solid fa-circle-check"></i></span><strong>Delivered</strong></div></div>
            <div class="support-section-head"><div><span>ডেলিভারি তথ্য</span><h2>যা জানা প্রয়োজন</h2></div></div>
            <div class="support-card-grid two">
                <div class="support-card static"><span class="support-card-icon blue"><i class="fa-solid fa-map-location-dot"></i></span><strong>Shipping Zone</strong><p>Location অনুযায়ী shipping zone এবং delivery charge automatically নির্ধারিত হবে।</p></div>
                <div class="support-card static"><span class="support-card-icon green"><i class="fa-solid fa-barcode"></i></span><strong>Tracking Number</strong><p>Courier assign হওয়ার পর available tracking number দিয়ে delivery progress দেখা যাবে।</p></div>
                <div class="support-card static"><span class="support-card-icon orange"><i class="fa-solid fa-mobile-screen-button"></i></span><strong>Delivery OTP</strong><p>প্রয়োজন অনুযায়ী product handover-এর সময় OTP verification ব্যবহার করা হতে পারে।</p></div>
                <div class="support-card static"><span class="support-card-icon purple"><i class="fa-solid fa-triangle-exclamation"></i></span><strong>Failed Delivery</strong><p>Delivery সম্পন্ন না হলে order-এ failed delivery status এবং পরবর্তী update দেখানো হবে।</p></div>
            </div>
            <div class="info-panel"><div class="info-panel-icon"><i class="fa-solid fa-calendar-check"></i></div><div><strong>Installment order delivery</strong><p>Installment purchase-এর ক্ষেত্রে required down payment Main Balance থেকে সফলভাবে পরিশোধ হওয়ার পর approval ও delivery process শুরু হবে।</p></div></div>
            <div class="policy-block"><h2>ডেলিভারি গ্রহণের সময়</h2><ul class="check-list"><li><i class="fa-solid fa-check"></i><span>Order/recipient information সঠিক আছে কি না নিশ্চিত করুন।</span></li><li><i class="fa-solid fa-check"></i><span>Tracking number থাকলে courier status অনুসরণ করুন।</span></li><li><i class="fa-solid fa-check"></i><span>OTP verification চাইলে শুধু product handover-এর সময় প্রয়োজনীয় OTP দিন।</span></li><li><i class="fa-solid fa-check"></i><span>Product-এর দৃশ্যমান সমস্যা থাকলে return policy অনুযায়ী request করুন।</span></li></ul></div>
        </main>
    </div>
</div>
@endsection
