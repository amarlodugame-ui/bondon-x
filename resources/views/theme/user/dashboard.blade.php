@extends('theme.layouts.master')

@section('content')
@php
    $orderStatusMap = [
        'approved' => ['Approved', 'approved'],
        'completed' => ['Completed', 'approved'],
        'pending_approval' => ['Pending', 'pending'],
        'payment_pending' => ['Pending', 'pending'],
        'rejected' => ['Rejected', 'rejected'],
        'cancelled' => ['Cancelled', 'rejected'],
    ];
@endphp

<div class="content-container">
    <section class="offer-banner">
        <div id="dashboardOfferSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4500" data-bs-touch="true">
            <div class="carousel-indicators">
                @foreach($dashboardBanners as $banner)
                    <button type="button" data-bs-target="#dashboardOfferSlider" data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" aria-label="Slide {{ $loop->iteration }}"></button>
                @endforeach
            </div>
            <div class="carousel-inner">
                @foreach($dashboardBanners as $banner)
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        <img src="{{ asset($banner['image']) }}" class="d-block w-100" alt="{{ $banner['alt'] }}" @if($loop->first) fetchpriority="high" @else loading="lazy" @endif decoding="async">
                    </div>
                @endforeach
            </div>
            @if(count($dashboardBanners) > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#dashboardOfferSlider" data-bs-slide="prev" aria-label="Previous banner">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#dashboardOfferSlider" data-bs-slide="next" aria-label="Next banner">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            @endif
        </div>
    </section>

    <section class="balance-grid">
        <div class="balance-card balance-blue">
            <div>
                <div class="balance-label">আমার ব্যালেন্স</div>
                <div class="balance-amount">৳ {{ number_format((float) $user->balance, 2) }}</div>
                <div class="balance-subtitle">মোট ব্যালেন্স</div>
            </div>
            <div class="balance-icon"><i class="fa-solid fa-wallet"></i></div>
        </div>
        <div class="balance-card balance-green">
            <div>
                <div class="balance-label">সঞ্চয় ব্যালেন্স</div>
                <div class="balance-amount">৳ {{ number_format((float) $user->saving_balance, 2) }}</div>
                <div class="balance-subtitle">মোট সঞ্চয়</div>
            </div>
            <div class="balance-icon"><i class="fa-solid fa-piggy-bank"></i></div>
        </div>
        <div class="balance-card balance-purple">
            <div>
                <div class="balance-label">রেফার কমিশন</div>
                <div class="balance-amount">৳ {{ number_format($referralIncome, 2) }}</div>
                <div class="balance-subtitle">মোট কমিশন</div>
            </div>
            <div class="balance-icon"><i class="fa-solid fa-user-group"></i></div>
        </div>
    </section>

    <section class="quick-panel">
        <div class="section-mini-title">দ্রুত কার্যক্রম</div>
        <div class="quick-grid">
            <a href="{{ route('checkout') }}" class="quick-item text-decoration-none">
                <div class="quick-icon blue-bg"><i class="fa-solid fa-wallet"></i></div>
                <span>জমা দিন</span>
            </a>
            <div class="quick-item" title="উত্তোলন সিস্টেম এখনও চালু হয়নি">
                <div class="quick-icon green-bg"><i class="fa-solid fa-money-bill-transfer"></i></div>
                <span>উত্তোলন করুন</span>
            </div>
            <button type="button" class="quick-item border-0 bg-transparent" id="copyReferralQuick" data-referral-link="{{ $referralLink }}">
                <div class="quick-icon purple-bg"><i class="fa-solid fa-user-group"></i></div>
                <span>রেফার করুন</span>
            </button>
            @if($recentOrders->isNotEmpty())
                <a href="{{ route('order.confirmation', $recentOrders->first()->order_no) }}" class="quick-item text-decoration-none">
                    <div class="quick-icon indigo-bg"><i class="fa-solid fa-clock"></i></div>
                    <span>হিস্ট্রি দেখুন</span>
                </a>
            @else
                <div class="quick-item">
                    <div class="quick-icon indigo-bg"><i class="fa-solid fa-clock"></i></div>
                    <span>হিস্ট্রি দেখুন</span>
                </div>
            @endif
            <a href="{{ route('support') }}" class="quick-item text-decoration-none">
                <div class="quick-icon orange-bg"><i class="fa-solid fa-headset"></i></div>
                <span>সাপোর্ট/যোগাযোগ</span>
            </a>
        </div>
    </section>

    <section class="middle-grid">
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-title">সক্রিয় কিস্তি</div>
                <div class="view-all">পরবর্তী কিস্তি <i class="fa-solid fa-chevron-right"></i></div>
            </div>
            @if($activeInstallment)
                @php
                    $installmentItem = $activeInstallment->orderItem;
                    $installmentProduct = $installmentItem?->product;
                    $dueAmount = max(0, (float) $activeInstallment->amount + (float) $activeInstallment->late_fee_amount - (float) $activeInstallment->paid_amount);
                @endphp
                <div class="purchase-body">
                    <div class="purchase-main">
                        <div class="purchase-product-image">
                            @if($installmentProduct?->image && basename($installmentProduct->image) === $installmentProduct->image)
                                <img src="{{ asset('assets/theme/images/products/' . $installmentProduct->image) }}" alt="{{ $installmentItem?->product_name ?? $installmentProduct->name }}">
                            @else
                                <i class="fa-solid fa-box-open"></i>
                            @endif
                        </div>
                        <div>
                            <div class="product-name">{{ $installmentItem?->product_name ?? $installmentProduct?->name ?? 'কিস্তির পণ্য' }}</div>
                            <div class="product-small">{{ $installmentItem?->sku ?: 'কিস্তি #' . $activeInstallment->installment_no }}</div>
                            <div class="product-small">ID: {{ $activeInstallment->order?->order_no }}</div>
                            <span class="mini-status status-red">সক্রিয়</span>
                        </div>
                    </div>
                    <div class="purchase-stats">
                        <div class="purchase-stat">
                            <span class="purchase-stat-label">বকেয়া পরিমাণ</span>
                            <strong class="purchase-stat-value">৳ {{ number_format($dueAmount, 2) }}</strong>
                        </div>
                        <div class="purchase-stat">
                            <span class="purchase-stat-label">পরবর্তী কিস্তির তারিখ</span>
                            <strong class="purchase-stat-value date">{{ $activeInstallment->due_date?->format('d M, Y') }}</strong>
                        </div>
                    </div>
                    <a href="{{ route('order.confirmation', $activeInstallment->order?->order_no) }}" class="primary-btn text-decoration-none">বিস্তারিত দেখুন</a>
                </div>
            @else
                <div class="purchase-body text-center py-4 text-muted">
                    <i class="fa-solid fa-circle-check mb-2"></i>
                    <div>কোনো সক্রিয় কিস্তি নেই</div>
                </div>
            @endif
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-title">সাম্প্রতিক অর্ডার</div>
                <div class="view-all">সর্বশেষ {{ $recentOrders->count() }}টি <i class="fa-solid fa-chevron-right"></i></div>
            </div>
            <div class="orders-body">
                @forelse($recentOrders as $order)
                    @php
                        $firstItem = $order->items->first();
                        $orderProduct = $firstItem?->product;
                        [$statusLabel, $statusClass] = $orderStatusMap[$order->order_status] ?? [ucwords(str_replace('_', ' ', $order->order_status)), 'pending'];
                    @endphp
                    <a href="{{ route('order.confirmation', $order->order_no) }}" class="order-row text-decoration-none">
                        <div class="order-image">
                            @if($orderProduct?->image && basename($orderProduct->image) === $orderProduct->image)
                                <img src="{{ asset('assets/theme/images/products/' . $orderProduct->image) }}" alt="{{ $firstItem?->product_name }}" loading="lazy">
                            @else
                                <i class="fa-solid fa-box"></i>
                            @endif
                        </div>
                        <div>
                            <div class="order-name">{{ $firstItem?->product_name ?? 'অর্ডার' }}</div>
                            <div class="order-id">#{{ $order->order_no }}</div>
                            <div class="order-meta">
                                <span>৳{{ number_format((float) $order->grand_total, 2) }}</span>
                                <span>{{ $order->created_at?->format('d M, Y') }}</span>
                            </div>
                        </div>
                        <span class="order-status {{ $statusClass }}">{{ $statusLabel }}</span>
                    </a>
                @empty
                    <div class="text-center py-4 text-muted">এখনও কোনো অর্ডার নেই</div>
                @endforelse
            </div>
        </div>

        <div class="dashboard-card referral-section">
            <div class="card-header">
                <div class="card-title">রেফার সফলতা</div>
                <div class="view-all">আপনার রেফার তথ্য <i class="fa-solid fa-chevron-right"></i></div>
            </div>
            <div class="referral-body">
                <div class="referral-top">
                    <div class="referral-summary referral-users">
                        <div class="referral-summary-content">
                            <span class="referral-label">মোট রেফার</span>
                            <strong class="referral-number">{{ number_format($referralCount) }} <small>জন</small></strong>
                        </div>
                        <div class="referral-summary-icon users-icon"><i class="fa-solid fa-user-group"></i></div>
                    </div>
                    <div class="referral-summary referral-income">
                        <div class="referral-summary-content">
                            <span class="referral-label">মোট ইনকাম</span>
                            <strong class="referral-number green">৳ {{ number_format($referralIncome, 2) }}</strong>
                        </div>
                        <div class="referral-summary-icon income-icon"><i class="fa-solid fa-wallet"></i></div>
                    </div>
                </div>
                <div class="referral-levels">
                    <div class="level-box level-green">
                        <span>রেফার কোড</span>
                        <strong>{{ $user->referral_code }}</strong>
                    </div>
                    <div class="level-box level-blue">
                        <span>মেম্বার নং</span>
                        <strong>{{ $user->member_no }}</strong>
                    </div>
                    <div class="level-box level-orange">
                        <span>স্ট্যাটাস</span>
                        <strong>{{ $user->is_member ? 'সদস্য' : 'সাধারণ' }}</strong>
                    </div>
                </div>
                <button type="button" class="referral-btn" id="copyReferralButton" data-referral-link="{{ $referralLink }}">
                    <i class="fa-solid fa-share-nodes"></i>
                    <span>রেফার লিংক কপি করুন</span>
                </button>
            </div>
        </div>
    </section>

    <section class="bottom-grid">
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-title">সাম্প্রতিক লেনদেন</div>
                <div class="view-all">সর্বশেষ {{ $recentTransactions->count() }}টি <i class="fa-solid fa-chevron-right"></i></div>
            </div>
            <div class="transactions">
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th>ধরন</th>
                            <th>বিবরণ</th>
                            <th>তারিখ ও সময়</th>
                            <th>পরিমাণ</th>
                            <th>স্থিতি</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $transaction)
                            @php
                                $isCredit = $transaction->trx_type === '+';
                                $walletLabel = $transaction->wallet_type === 'saving_balance' || $transaction->wallet_type === 'saving' ? 'সঞ্চয় একাউন্ট' : 'মূল ব্যালেন্স';
                            @endphp
                            <tr>
                                <td>
                                    <div class="transaction-type">
                                        <span class="transaction-dot {{ $isCredit ? 'green-bg' : 'blue-bg' }}">
                                            <i class="fa-solid {{ $isCredit ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                        </span>
                                        {{ $isCredit ? 'জমা' : 'খরচ' }}
                                    </div>
                                </td>
                                <td>{{ $transaction->details ?: ($transaction->remark ?: $walletLabel) }}</td>
                                <td>{{ $transaction->created_at?->format('d M, Y h:i A') }}</td>
                                <td class="{{ $isCredit ? 'amount-positive' : 'amount-negative' }}">
                                    {{ $isCredit ? '+' : '-' }}৳{{ number_format(abs((float) $transaction->amount), 2) }}
                                </td>
                                <td><span class="order-status approved">Completed</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">এখনও কোনো লেনদেন নেই</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-title">জনপ্রিয় পণ্য</div>
                <a href="{{ route('products') }}" class="view-all text-decoration-none">সব দেখুন <i class="fa-solid fa-chevron-right"></i></a>
            </div>
            <div class="product-grid">
                @forelse($popularProducts as $product)
                    @php
                        $price = (float) $product->price;
                        $oldPrice = (float) ($product->old_price ?? 0);
                        $discount = $oldPrice > $price && $oldPrice > 0 ? (int) round((($oldPrice - $price) / $oldPrice) * 100) : null;
                    @endphp
                    <div class="product-card">
                        @if($discount)
                            <span class="discount-badge">{{ $discount }}% OFF</span>
                        @endif
                        <div class="product-image">
                            @if($product->image && basename($product->image) === $product->image)
                                <img src="{{ asset('assets/theme/images/products/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <i class="fa-solid fa-box-open"></i>
                            @endif
                        </div>
                        <div class="product-info">
                            <div class="product-title">{{ $product->name }}</div>
                            <div class="product-price">
                                <span class="new-price">৳ {{ number_format($price, 2) }}</span>
                                @if($oldPrice > $price)
                                    <span class="old-price">৳{{ number_format($oldPrice, 2) }}</span>
                                @endif
                            </div>
                            <a href="{{ route('product.show', $product->slug) }}" class="order-btn text-decoration-none">অর্ডার করুন</a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted w-100">কোনো জনপ্রিয় পণ্য পাওয়া যায়নি</div>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function fallbackCopy(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.setAttribute('readonly', '');
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        textarea.remove();
    }

    async function copyReferral(button) {
        const link = button?.dataset.referralLink;
        if (!link) return;

        try {
            if (navigator.clipboard?.writeText) {
                await navigator.clipboard.writeText(link);
            } else {
                fallbackCopy(link);
            }
        } catch (error) {
            fallbackCopy(link);
        }

        const label = button.querySelector('span');
        if (!label) return;
        const oldText = label.textContent;
        label.textContent = 'কপি হয়েছে';
        window.setTimeout(function () {
            label.textContent = oldText;
        }, 1600);
    }

    document.getElementById('copyReferralButton')?.addEventListener('click', function () {
        copyReferral(this);
    });
    document.getElementById('copyReferralQuick')?.addEventListener('click', function () {
        copyReferral(this);
    });
});
</script>
@endpush
