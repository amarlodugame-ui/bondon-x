@extends('theme.layouts.frontend')

@push('style')
<link rel="stylesheet" href="{{ asset('assets/theme/css/support-pages.css') }}">
@endpush

@section('content')
<div class="support-page">
    <section class="support-mini-hero"><div class="support-wrap"><div><span class="support-kicker"><i class="fa-solid fa-rotate-left"></i> রিটার্ন ও রিফান্ড</span><h1>Return request থেকে Refund পর্যন্ত পরিষ্কার প্রক্রিয়া</h1><p>Eligible delivered product-এর জন্য return request করা যাবে। Verification complete হলে approved amount Main Balance-এ refund হবে।</p></div></div></section>
    <div class="support-wrap support-layout">
        @include('theme.support.partials.sidebar')
        <main class="support-main">
            <div class="support-section-head"><div><span>Return Flow</span><h2>রিটার্নের ধাপসমূহ</h2></div></div>
            <div class="process-grid three">
                <div class="process-card"><span>01</span><i class="fa-regular fa-file-lines"></i><strong>Request দিন</strong><p>Delivered product-এর জন্য return reason এবং প্রয়োজন হলে ছবি যুক্ত করুন।</p></div>
                <div class="process-card"><span>02</span><i class="fa-solid fa-magnifying-glass"></i><strong>Verification</strong><p>Admin return request ও product condition যাচাই করে approve অথবা reject করবে।</p></div>
                <div class="process-card"><span>03</span><i class="fa-solid fa-wallet"></i><strong>Wallet Refund</strong><p>Approved refund amount আপনার Main Balance-এ credit হবে এবং transaction history তৈরি হবে।</p></div>
            </div>
            <div class="policy-block"><h2>Return request করার আগে</h2><ul class="check-list"><li><i class="fa-solid fa-check"></i><span>Order delivered হতে হবে এবং সংশ্লিষ্ট product/order return-এর জন্য eligible হতে হবে।</span></li><li><i class="fa-solid fa-check"></i><span>সঠিক return reason দিতে হবে; damage বা condition বোঝাতে ছবি চাওয়া হতে পারে।</span></li><li><i class="fa-solid fa-check"></i><span>Approval না হওয়া পর্যন্ত refund final হিসেবে গণ্য হবে না।</span></li><li><i class="fa-solid fa-check"></i><span>Approved refund-এর transaction ও refund history account-এ সংরক্ষিত থাকবে।</span></li></ul></div>
            <div class="policy-block"><h2>Cancelled Order</h2><p>Order cancellation approved হলে applicable paid amount system verification-এর পর Main Balance-এ ফেরত যেতে পারে। Order-এর বর্তমান stage এবং processing status অনুযায়ী cancellation availability নির্ধারিত হবে।</p></div>
        </main>
    </div>
</div>
@endsection
