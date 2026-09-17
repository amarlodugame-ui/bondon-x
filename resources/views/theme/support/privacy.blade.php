@extends('theme.layouts.frontend')

@push('style')
<link rel="stylesheet" href="{{ asset('assets/theme/css/support-pages.css') }}">
@endpush

@section('content')
<div class="support-page">
    <section class="support-mini-hero"><div class="support-wrap"><div><span class="support-kicker"><i class="fa-solid fa-shield-halved"></i> Privacy Policy</span><h1>আপনার তথ্য কীভাবে ব্যবহৃত হয়</h1><p>Account, order, payment, delivery এবং membership service পরিচালনার জন্য প্রয়োজনীয় তথ্য কীভাবে ব্যবহৃত হতে পারে তার সারসংক্ষেপ।</p></div></div></section>
    <div class="support-wrap support-layout">
        @include('theme.support.partials.sidebar')
        <main class="support-main legal-content">
            <div class="privacy-highlight"><span><i class="fa-solid fa-lock"></i></span><div><strong>প্রয়োজনীয় তথ্য, নির্দিষ্ট উদ্দেশ্যে</strong><p>Service চালানো, transaction trace করা, security বজায় রাখা এবং customer support দেওয়ার জন্য প্রয়োজনীয় তথ্য ব্যবহার করা হয়।</p></div></div>
            <section><span>01</span><div><h2>যে তথ্য সংগ্রহ হতে পারে</h2><p>নাম, মোবাইল, ইমেইল, delivery address, account information, order history, wallet transaction, deposit information, review এবং service interaction সংরক্ষিত হতে পারে।</p></div></section>
            <section><span>02</span><div><h2>Membership ও KYC তথ্য</h2><p>Membership verification-এর জন্য NID, address, profession, photo এবং প্রয়োজনীয় verification information নেওয়া হতে পারে। এসব তথ্য membership review এবং account verification-এর জন্য ব্যবহার করা হবে।</p></div></section>
            <section><span>03</span><div><h2>Payment ও Transaction Data</h2><p>Deposit transaction ID, payment method, amount, wallet movement, order/installment reference এবং transaction history financial tracking ও dispute resolution-এর জন্য সংরক্ষিত হতে পারে।</p></div></section>
            <section><span>04</span><div><h2>Login ও Security Information</h2><p>Login time, IP address, device বা browser information account security, suspicious activity detection এবং login history-এর জন্য রাখা হতে পারে।</p></div></section>
            <section><span>05</span><div><h2>তথ্য ব্যবহারের উদ্দেশ্য</h2><p>Order processing, delivery, payment verification, installment management, membership verification, notification, customer support, fraud prevention এবং service improvement-এর জন্য তথ্য ব্যবহার হতে পারে।</p></div></section>
            <section><span>06</span><div><h2>Service Provider-এর সাথে প্রয়োজনীয় তথ্য</h2><p>Order delivery বা service fulfilment-এর জন্য courier বা সংশ্লিষ্ট operational service provider-এর সাথে প্রয়োজনীয় সীমিত তথ্য শেয়ার করা হতে পারে।</p></div></section>
            <section><span>07</span><div><h2>তথ্য নিরাপত্তা</h2><p>Account এবং transaction data নিরাপদ রাখতে access control, validation, audit trail এবং অন্যান্য উপযুক্ত technical measure ব্যবহার করা যেতে পারে।</p></div></section>
            <section><span>08</span><div><h2>Policy Update</h2><p>Service বা legal requirement পরিবর্তিত হলে privacy policy update হতে পারে। Updated version website-এ প্রকাশ করা হবে।</p></div></section>
            <div class="support-callout"><div><span class="support-callout-icon"><i class="fa-regular fa-envelope"></i></span><div><strong>Privacy নিয়ে প্রশ্ন আছে?</strong><p>আপনার প্রশ্ন বা concern আমাদের contact page থেকে পাঠাতে পারেন।</p></div></div><a href="{{ route('contact') }}">যোগাযোগ করুন <i class="fa-solid fa-arrow-right"></i></a></div>
        </main>
    </div>
</div>
@endsection
