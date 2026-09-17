@extends('theme.layouts.frontend')

@push('style')
<link rel="stylesheet" href="{{ asset('assets/theme/css/support-pages.css') }}">
@endpush

@section('content')
<div class="support-page">
    <section class="support-mini-hero"><div class="support-wrap"><div><span class="support-kicker"><i class="fa-regular fa-envelope"></i> যোগাযোগ</span><h1>আমাদের সাথে যোগাযোগ করুন</h1><p>অর্ডার, পেমেন্ট, কিস্তি, সদস্যতা বা অন্য কোনো বিষয়ে সাহায্য প্রয়োজন হলে বিস্তারিত লিখে জানান।</p></div></div></section>
    <div class="support-wrap support-layout">
        @include('theme.support.partials.sidebar')
        <main class="support-main">
            <div class="contact-grid">
                <section class="contact-form-card">
                    <div class="support-section-head"><div><span>বার্তা পাঠান</span><h2>আপনার প্রশ্ন লিখুন</h2></div></div>
                    @if(session('success'))<div class="form-alert success"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>@endif
                    @if(session('error'))<div class="form-alert error"><i class="fa-solid fa-circle-exclamation"></i>{{ session('error') }}</div>@endif
                    @if($errors->any())<div class="form-alert error"><i class="fa-solid fa-circle-exclamation"></i>তথ্যগুলো ঠিকভাবে পূরণ করে আবার চেষ্টা করুন।</div>@endif
                    <form class="support-form" action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="form-row"><div><label for="name">আপনার নাম</label><input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="পূর্ণ নাম" required></div><div><label for="email">ইমেইল</label><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="name@example.com" required></div></div>
                        <div class="form-row"><div><label for="mobile">মোবাইল নম্বর</label><input id="mobile" name="mobile" type="text" value="{{ old('mobile') }}" placeholder="01XXXXXXXXX"></div><div><label for="subject">বিষয়</label><input id="subject" name="subject" type="text" value="{{ old('subject') }}" placeholder="কী বিষয়ে সাহায্য দরকার?" required></div></div>
                        <div><label for="message">বিস্তারিত বার্তা</label><textarea id="message" name="message" rows="6" placeholder="আপনার সমস্যাটি বিস্তারিত লিখুন..." required>{{ old('message') }}</textarea></div>
                        <button class="support-btn primary submit" type="submit">বার্তা পাঠান <i class="fa-regular fa-paper-plane"></i></button>
                    </form>
                </section>
                <aside class="contact-info-card">
                    <span class="contact-badge">Contact Info</span><h3>সরাসরি যোগাযোগ</h3><p>General Settings-এ তথ্য সেট করলে এখানে automatically দেখাবে।</p>
                    <div class="contact-info-list">
                        @if(gs()->phone ?? null)<a href="tel:{{ gs()->phone }}"><span><i class="fa-solid fa-phone"></i></span><div><small>ফোন</small><strong>{{ gs()->phone }}</strong></div></a>@endif
                        @if(gs()->email ?? null)<a href="mailto:{{ gs()->email }}"><span><i class="fa-regular fa-envelope"></i></span><div><small>ইমেইল</small><strong>{{ gs()->email }}</strong></div></a>@endif
                        @if(gs()->address ?? null)<div class="contact-info-item"><span><i class="fa-solid fa-location-dot"></i></span><div><small>ঠিকানা</small><strong>{{ gs()->address }}</strong></div></div>@endif
                    </div>
                    @if(!(gs()->নিরাপদ ও পরিষ্কার প্রক্রিয়া ?? null) && !(gs()->email ?? null) && !(gs()->address ?? null))<div class="contact-empty"><i class="fa-solid fa-gear"></i><span>Admin থেকে Phone, Email ও Address সেট করলে contact details এখানে দেখা যাবে।</span></div>@endif
                    <div class="contact-help"><i class="fa-regular fa-circle-question"></i><div><strong>আগে FAQ দেখেছেন?</strong><p>অনেক সাধারণ প্রশ্নের উত্তর FAQ page-এ পাওয়া যাবে।</p><a href="{{ route('faq') }}">FAQ দেখুন <i class="fa-solid fa-arrow-right"></i></a></div></div>
                </aside>
            </div>
        </main>
    </div>
</div>
@endsection
