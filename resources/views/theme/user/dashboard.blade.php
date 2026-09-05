@extends('theme.layouts.master')
@section('content')
<div class="content-container">
    <section class="offer-banner">
        <img src="assets/investment-offer-banner.png" alt="Investment Offer">
    </section>
    <!--   BALANCE CARDS  -->
    <section class="balance-grid">
        <div class="balance-card balance-blue">
            <div>
                <div class="balance-label">
                    আমার ব্যালেন্স
                </div>
                <div class="balance-amount counter" data-target="5780">
                    ৳ 0
                </div>
                <div class="balance-subtitle">
                    মোট ব্যালেন্স
                </div>
            </div>
            <div class="balance-icon">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>
        <div class="balance-card balance-green">
            <div>
                <div class="balance-label">
                    সঞ্চয় ব্যালেন্স
                </div>
                <div class="balance-amount counter" data-target="12450">
                    ৳ 0
                </div>
                <div class="balance-subtitle">
                    মোট সঞ্চয়
                </div>
            </div>
            <div class="balance-icon">
                <i class="fa-solid fa-piggy-bank"></i>
            </div>
        </div>
        <div class="balance-card balance-purple">
            <div>
                <div class="balance-label">
                    রেফার কমিশন
                </div>
                <div class="balance-amount counter" data-target="3250">
                    ৳ 0
                </div>
                <div class="balance-subtitle">
                    মোট কমিশন
                </div>
            </div>
            <div class="balance-icon">
                <i class="fa-solid fa-user-group"></i>
            </div>
        </div>
    </section>
    <!--  QUICK ACTION  -->
    <section class="quick-panel">
        <div class="section-mini-title">
            দ্রুত কার্যক্রম
        </div>
        <div class="quick-grid">
            <div class="quick-item">
                <div class="quick-icon blue-bg">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <span>
                    জমা দিন
                </span>
            </div>
            <div class="quick-item">
                <div class="quick-icon green-bg">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
                <span>
                    উত্তোলন করুন
                </span>
            </div>
            <div class="quick-item">
                <div class="quick-icon purple-bg">
                    <i class="fa-solid fa-user-group"></i>
                </div>
                <span>
                    রেফার করুন
                </span>
            </div>
            <div class="quick-item">
                <div class="quick-icon indigo-bg">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <span>
                    হিস্ট্রি দেখুন
                </span>
            </div>
            <div class="quick-item">
                <div class="quick-icon orange-bg">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <span>
                    সাপোর্ট/যোগাযোগ
                </span>
            </div>
        </div>
    </section>
    <!-- MIDDLE GRID  -->
    <section class="middle-grid">
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-title">
                    সক্রিয় ক্রয় (বেচা)
                </div>
                <div class="view-all">
                    সব দেখুন
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </div>
            <div class="purchase-body">
                <div class="purchase-main">
                    <div class="purchase-product-image">
                        <img src="assets/hoco-eq8-earbuds.png" alt="Hoco Earbuds">
                    </div>
                    <div>
                        <div class="product-name">
                            Hoco EQ8 In-Ear Hook
                        </div>
                        <div class="product-small">
                            5.3V
                        </div>
                        <div class="product-small">
                            ID: INST123456
                        </div>
                        <span class="mini-status status-red">
                            সক্রিয়
                        </span>
                    </div>
                </div>
                <div class="purchase-stats">
                    <div class="purchase-stat">
                        <span class="purchase-stat-label">
                            বর্তমান পরিমাণ
                        </span>
                        <strong class="purchase-stat-value">
                            ৳ 166
                        </strong>
                    </div>
                    <div class="purchase-stat">
                        <span class="purchase-stat-label">
                            পরবর্তী বিক্রির তারিখ
                        </span>
                        <strong class="purchase-stat-value date">
                            01 জুন, 2025
                        </strong>
                    </div>
                </div>
                <button class="primary-btn">
                    এক্টিভ বিক্রি দিন
                </button>
            </div>
        </div>
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-title">
                    সাম্প্রতিক অর্ডার
                </div>
                <div class="view-all">
                    সব দেখুন
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </div>
            <div class="orders-body">
                <div class="order-row">
                    <div class="order-image">
                        <img src="assets/hoco-eq8-earbuds.png" alt="">
                    </div>
                    <div>
                        <div class="order-name">
                            Hoco EQ8 In-Ear Hook
                        </div>
                        <div class="order-id">
                            #ORD-45896
                        </div>
                        <div class="order-meta">
                            <span>
                                ৳899
                            </span>
                            <span>
                                12 Oct, 2025
                            </span>
                        </div>
                    </div>
                    <span class="order-status approved">
                        Approved
                    </span>
                </div>
                <div class="order-row">
                    <div class="order-image">
                        <img src="assets/a4tech-hs50-headphone.png" alt="">
                    </div>
                    <div>
                        <div class="order-name">
                            A4Tech Headphone HS-50
                        </div>
                        <div class="order-id">
                            #ORD-45895
                        </div>
                        <div class="order-meta">
                            <span>
                                ৳450
                            </span>
                            <span>
                                10 Oct, 2025
                            </span>
                        </div>
                    </div>
                    <span class="order-status pending">
                        Pending
                    </span>
                </div>
                <div class="order-row">
                    <div class="order-image">
                        <img src="assets/hoco-eq8-earbuds.png" alt="">
                    </div>
                    <div>
                        <div class="order-name">
                            Hoco EQ8 In-Ear Hook
                        </div>
                        <div class="order-id">
                            #ORD-45894
                        </div>
                        <div class="order-meta">
                            <span>
                                ৳890
                            </span>
                            <span>
                                08 Oct, 2025
                            </span>
                        </div>
                    </div>
                    <span class="order-status rejected">
                        Rejected
                    </span>
                </div>
            </div>
        </div>
        <div class="dashboard-card referral-section">
            <div class="card-header">
                <div class="card-title">
                    রেফার সফলতা
                </div>
                <div class="view-all">
                    সব দেখুন
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </div>
            <div class="referral-body">
                <div class="referral-top">

                    <!-- মোট রেফার -->
                    <div class="referral-summary referral-users">

                        <div class="referral-summary-content">

                            <span class="referral-label">
                                মোট রেফার
                            </span>

                            <strong class="referral-number">
                                288 <small>জন</small>
                            </strong>

                        </div>

                        <div class="referral-summary-icon users-icon">
                            <i class="fa-solid fa-user-group"></i>
                        </div>

                    </div>


                    <!-- মোট ইনকাম -->
                    <div class="referral-summary referral-income">

                        <div class="referral-summary-content">

                            <span class="referral-label">
                                মোট ইনকাম
                            </span>

                            <strong class="referral-number green">
                                ৳ 12,850.00
                            </strong>

                        </div>

                        <div class="referral-summary-icon income-icon">
                            <i class="fa-solid fa-wallet"></i>
                        </div>

                    </div>

                </div>
                <div class="referral-levels">
                    <div class="level-box level-green">
                        <span>
                            লেভেল ১
                        </span>
                        <strong>
                            20%
                        </strong>
                    </div>
                    <div class="level-box level-blue">
                        <span>
                            লেভেল ২
                        </span>
                        <strong>
                            10%
                        </strong>
                    </div>
                    <div class="level-box level-orange">
                        <span>
                            লেভেল ৩
                        </span>
                        <strong>
                            5%
                        </strong>
                    </div>
                </div>
                <button class="referral-btn">
                    <i class="fa-solid fa-share-nodes"></i>
                    রেফার করুন
                </button>
            </div>
        </div>
    </section>
    <!--   BOTTOM  -->
    <section class="bottom-grid">
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-title">
                    সাম্প্রতিক লেনদেন
                </div>
                <div class="view-all">
                    সব দেখুন
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </div>
            <div class="transactions">
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th>
                                ধরন
                            </th>
                            <th>
                                বিবরণ
                            </th>
                            <th>
                                তারিখ ও সময়
                            </th>
                            <th>
                                পরিমাণ
                            </th>
                            <th>
                                স্থিতি
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="transaction-type">
                                    <span class="transaction-dot green-bg">
                                        <i class="fa-solid fa-arrow-up"></i>
                                    </span>
                                    সঞ্চয় জমা
                                </div>
                            </td>
                            <td>
                                সঞ্চয় একাউন্ট
                            </td>
                            <td>
                                17 Oct, 2025
                            </td>
                            <td class="amount-positive">
                                +৳500.00
                            </td>
                            <td>
                                <span class="order-status approved">
                                    Approved
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="transaction-type">
                                    <span class="transaction-dot blue-bg">
                                        <i class="fa-solid fa-arrow-down"></i>
                                    </span>
                                    উত্তোলন
                                </div>
                            </td>
                            <td>
                                বিকাশ উত্তোলন
                            </td>
                            <td>
                                16 Oct, 2025
                            </td>
                            <td class="amount-negative">
                                -৳800.00
                            </td>
                            <td>
                                <span class="order-status approved">
                                    Approved
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="transaction-type">
                                    <span class="transaction-dot purple-bg">
                                        <i class="fa-solid fa-users"></i>
                                    </span>
                                    কমিশন
                                </div>
                            </td>
                            <td>
                                রেফারেল কমিশন
                            </td>
                            <td>
                                15 Oct, 2025
                            </td>
                            <td class="amount-positive">
                                +৳150.00
                            </td>
                            <td>
                                <span class="order-status approved">
                                    Approved
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="transaction-type">
                                    <span class="transaction-dot green-bg">
                                        <i class="fa-solid fa-arrow-up"></i>
                                    </span>
                                    সঞ্চয় জমা
                                </div>
                            </td>
                            <td>
                                সঞ্চয় একাউন্ট
                            </td>
                            <td>
                                14 Oct, 2025
                            </td>
                            <td class="amount-positive">
                                +৳300.00
                            </td>
                            <td>
                                <span class="order-status approved">
                                    Approved
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="transaction-type">
                                    <span class="transaction-dot blue-bg">
                                        <i class="fa-solid fa-arrow-down"></i>
                                    </span>
                                    উত্তোলন
                                </div>
                            </td>
                            <td>
                                ব্যাংক উত্তোলন
                            </td>
                            <td>
                                13 Oct, 2025
                            </td>
                            <td class="amount-negative">
                                -৳500.00
                            </td>
                            <td>
                                <span class="order-status approved">
                                    Approved
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-title">
                    জনপ্রিয় পণ্য
                </div>
                <div class="view-all">
                    সব দেখুন
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </div>
            <div class="product-grid">
                <div class="product-card">
                    <span class="discount-badge">
                        10% OFF
                    </span>
                    <div class="product-image">
                        <img src="assets/hoco-eq8-earbuds.png" alt="Hoco EQ8">
                    </div>
                    <div class="product-info">
                        <div class="product-title">
                            Hoco EQ8 In-Ear Hook 5.3V
                        </div>
                        <div class="product-price">
                            <span class="new-price">
                                ৳ 890
                            </span>
                            <span class="old-price">
                                ৳990
                            </span>
                        </div>
                        <button class="order-btn">
                            অর্ডার করুন
                        </button>
                    </div>
                </div>
                <div class="product-card">
                    <span class="discount-badge">
                        10% OFF
                    </span>
                    <div class="product-image">
                        <img src="assets/a4tech-hs50-headphone.png" alt="A4Tech HS-50">
                    </div>
                    <div class="product-info">
                        <div class="product-title">
                            A4Tech HS-50 Headphone
                        </div>
                        <div class="product-price">
                            <span class="new-price">
                                ৳ 450
                            </span>
                            <span class="old-price">
                                ৳500
                            </span>
                        </div>
                        <button class="order-btn">
                            অর্ডার করুন
                        </button>
                    </div>
                </div>
                <div class="product-card">
                    <span class="discount-badge">
                        15% OFF
                    </span>
                    <div class="product-image">
                        <img src="assets/baseus-10000mah-powerbank.png" alt="Baseus Power Bank">
                    </div>
                    <div class="product-info">
                        <div class="product-title">
                            Baseus 10000mAh Power Bank
                        </div>
                        <div class="product-price">
                            <span class="new-price">
                                ৳ 1,190
                            </span>
                            <span class="old-price">
                                ৳1,400
                            </span>
                        </div>
                        <button class="order-btn">
                            অর্ডার করুন
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection