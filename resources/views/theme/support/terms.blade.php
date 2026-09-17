@extends('theme.layouts.frontend')

@push('style')
<link rel="stylesheet" href="{{ asset('assets/theme/css/support-pages.css') }}">
@endpush

@section('content')
<div class="support-page">
    <section class="support-mini-hero"><div class="support-wrap"><div><span class="support-kicker"><i class="fa-regular fa-file-lines"></i> Terms & Conditions</span><h1>ব্যবহারের শর্তাবলী</h1><p>Website, account, wallet, purchase, installment এবং membership ব্যবহার করার মৌলিক নিয়মগুলো এখানে দেওয়া হলো।</p></div></div></section>
    <div class="support-wrap support-layout">
        @include('theme.support.partials.sidebar')
        <main class="support-main legal-content">
            <div class="legal-intro"><i class="fa-solid fa-scale-balanced"></i><div><strong>সেবাটি ব্যবহার করার আগে</strong><p>Website ব্যবহার, account তৈরি বা order করার মাধ্যমে প্রযোজ্য service rules ও এই terms মেনে চলতে সম্মত হচ্ছেন।</p></div></div>
            <section><span>01</span><div><h2>অ্যাকাউন্ট ও তথ্য</h2><p>Registration, order, delivery এবং verification-এর জন্য দেওয়া তথ্য সঠিক রাখা ব্যবহারকারীর দায়িত্ব। Account access, password এবং verification information নিরাপদ রাখতে হবে।</p></div></section>
            <section><span>02</span><div><h2>মূল্য ও পণ্যের তথ্য</h2><p>Product price, installment availability, stock, discount এবং offer সময়ের সাথে পরিবর্তিত হতে পারে। Order confirm হওয়ার সময় system-এ প্রদর্শিত applicable amount বিবেচিত হবে।</p></div></section>
            <section><span>03</span><div><h2>Wallet ও Payment</h2><p>Shopping, installment down payment, installment payment এবং membership fee Main Balance থেকে নেওয়া হবে। External payment method Balance Deposit-এর জন্য ব্যবহার হবে। Main Balance ও external payment একসাথে ব্যবহার করে direct purchase করা যাবে না।</p></div></section>
            <section><span>04</span><div><h2>Installment Purchase</h2><p>শুধু installment enabled product-এ available plan নির্বাচন করা যাবে। Required down payment এবং প্রতিটি installment full amount হিসেবে পরিশোধ করতে হবে। Due date, grace period বা late fee applicable plan অনুযায়ী কার্যকর হতে পারে।</p></div></section>
            <section><span>05</span><div><h2>Order ও Delivery</h2><p>Order payment status, approval, processing, courier এবং delivery status অনুযায়ী অগ্রসর হবে। Customer-provided address বা contact information ভুল হলে delivery delay বা failure হতে পারে।</p></div></section>
            <section><span>06</span><div><h2>Cancellation, Return ও Refund</h2><p>Order stage ও applicable rules অনুযায়ী cancellation বা return request available হতে পারে। Approved refund Main Balance-এ credit হবে এবং transaction history-তে record থাকবে।</p></div></section>
            <section><span>07</span><div><h2>Membership ও KYC</h2><p>Membership application-এর জন্য প্রয়োজনীয় KYC information এবং one-time fee লাগতে পারে। Verification approve না হওয়া পর্যন্ত membership final নয়। Incorrect বা unverifiable information-এর কারণে application reject হতে পারে।</p></div></section>
            <section><span>08</span><div><h2>অপব্যবহার ও নিরাপত্তা</h2><p>Duplicate transaction information, fraudulent activity, unauthorized access বা system abuse শনাক্ত হলে transaction review, account restriction বা প্রয়োজনীয় security action নেওয়া হতে পারে।</p></div></section>
            <section><span>09</span><div><h2>নীতিমালার পরিবর্তন</h2><p>Service operation, আইনগত প্রয়োজন বা system update অনুযায়ী terms পরিবর্তিত হতে পারে। গুরুত্বপূর্ণ পরিবর্তন website-এ প্রকাশ করা হতে পারে।</p></div></section>
        </main>
    </div>
</div>
@endsection
