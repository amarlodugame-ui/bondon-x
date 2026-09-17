@extends('theme.layouts.frontend')

@push('style')
<link rel="stylesheet" href="{{ asset('assets/theme/css/support-pages.css') }}">
@endpush

@section('content')
<div class="support-page">
    <section class="support-mini-hero"><div class="support-wrap"><div><span class="support-kicker"><i class="fa-regular fa-circle-question"></i> FAQ</span><h1>সাধারণ প্রশ্ন ও উত্তর</h1><p>কেনাকাটা, Wallet, কিস্তি, সদস্যতা এবং রিফান্ডের গুরুত্বপূর্ণ প্রশ্নগুলোর সংক্ষিপ্ত উত্তর।</p></div></div></section>
    <div class="support-wrap support-layout">
        @include('theme.support.partials.sidebar')
        <main class="support-main">
            <div class="support-section-head"><div><span>প্রয়োজনীয় তথ্য</span><h2>প্রায়ই জিজ্ঞাসিত প্রশ্ন</h2></div></div>
            <div class="faq-list">
                <details open><summary>পণ্য কেনার টাকা কীভাবে পরিশোধ করব?<i class="fa-solid fa-plus"></i></summary><div>Cash purchase, installment down payment, পরবর্তী কিস্তি এবং membership fee Main Balance থেকে পরিশোধ হবে। Main Balance কম থাকলে আগে Deposit করতে হবে।</div></details>
                <details><summary>bKash/Nagad দিয়ে সরাসরি পণ্য কিনতে পারব?<i class="fa-solid fa-plus"></i></summary><div>না। External payment method শুধু Balance Deposit-এর জন্য। Deposit approve হয়ে Main Balance-এ যোগ হওয়ার পর সেই Balance দিয়ে কেনাকাটা করা যাবে।</div></details>
                <details><summary>Main Balance এবং bKash একসাথে দিয়ে payment করা যাবে?<i class="fa-solid fa-plus"></i></summary><div>না। Mixed funding payment থাকবে না। প্রয়োজনীয় পুরো amount আগে Main Balance-এ থাকতে হবে।</div></details>
                <details><summary>কিস্তির পণ্য কীভাবে কিনব?<i class="fa-solid fa-plus"></i></summary><div>Installment enabled পণ্য থেকে plan নির্বাচন করতে হবে। Down payment Main Balance থেকে সফলভাবে পরিশোধ হলে order approval ও delivery process শুরু হবে।</div></details>
                <details><summary>কিস্তির আংশিক টাকা দেওয়া যাবে?<i class="fa-solid fa-plus"></i></summary><div>না। নির্ধারিত installment amount সম্পূর্ণ পরিশোধ করতে হবে। Balance কম হলে আগে প্রয়োজনীয় টাকা Deposit করতে হবে।</div></details>
                <details><summary>Due date-এর আগে কিস্তি দেওয়া যাবে?<i class="fa-solid fa-plus"></i></summary><div>হ্যাঁ। নির্ধারিত due date-এর আগেও installment payment করা যাবে। Remaining installment একসাথে settlement করার সুবিধাও থাকতে পারে।</div></details>
                <details><summary>Return approve হলে refund কোথায় পাব?<i class="fa-solid fa-plus"></i></summary><div>Approved return বা cancelled order-এর refund Main Balance-এ যোগ হবে। সাধারণ refund সরাসরি bKash/Nagad-এ পাঠানো হবে না।</div></details>
                <details><summary>সদস্য হতে কী লাগবে?<i class="fa-solid fa-plus"></i></summary><div>Membership application-এ প্রয়োজনীয় পরিচয় ও KYC তথ্য দিতে হবে এবং one-time membership fee Main Balance থেকে পরিশোধ করতে হবে। এরপর Admin verification হবে।</div></details>
                <details><summary>Saving Balance দিয়ে shopping করা যাবে?<i class="fa-solid fa-plus"></i></summary><div>না। Saving Balance আলাদা থাকবে এবং shopping, installment বা membership payment-এর জন্য Main Balance ব্যবহার হবে।</div></details>
                <details><summary>অর্ডারের অবস্থা কীভাবে দেখব?<i class="fa-solid fa-plus"></i></summary><div>Login করার পর My Orders থেকে order status, payment information এবং available হলে courier tracking দেখা যাবে।</div></details>
            </div>
            <section class="support-callout"><div><span class="support-callout-icon"><i class="fa-solid fa-headset"></i></span><div><strong>আরও কিছু জানতে চান?</strong><p>FAQ-তে উত্তর না পেলে আমাদের সাথে যোগাযোগ করুন।</p></div></div><a href="{{ route('contact') }}">যোগাযোগ করুন <i class="fa-solid fa-arrow-right"></i></a></section>
        </main>
    </div>
</div>
@endsection
