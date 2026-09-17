<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Installment;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function home(): View
    {
        $user = Auth::user();

        $data['pageTitle'] = 'Dashboard';
        $data['categories'] = Category::query()
            ->where('status', 1)
            ->orderBy('sort_order')
            ->select(['id', 'name', 'slug', 'image'])
            ->get();

        $data['user'] = $user;
        $data['referralCount'] = $user->referrals()
            ->where('is_deleted', 0)
            ->count();
        $data['referralIncome'] = (float) $user->commissionsReceived()->sum('amount');
        $data['referralLink'] = route('user.register', ['ref' => $user->referral_code]);

        $data['recentOrders'] = $user->orders()
            ->with([
                'items' => fn ($query) => $query
                    ->orderBy('id')
                    ->with('product:id,name,slug,image'),
            ])
            ->latest('id')
            ->limit(3)
            ->get();

        $data['recentTransactions'] = $user->transactions()
            ->latest('id')
            ->limit(5)
            ->get();

        $data['activeInstallment'] = Installment::query()
            ->where('status', 'pending')
            ->whereHas('order', fn ($query) => $query->where('user_id', $user->id))
            ->with([
                'order:id,user_id,order_no',
                'orderItem:id,order_id,product_id,product_name,sku',
                'orderItem.product:id,name,slug,image',
            ])
            ->orderBy('due_date')
            ->orderBy('id')
            ->first();

        $data['popularProducts'] = Product::query()
            ->select(['id', 'name', 'slug', 'image', 'price', 'old_price', 'stock', 'status'])
            ->withSum('orderItems', 'quantity')
            ->where('status', 1)
            ->where('stock', '>', 0)
            ->orderByDesc('order_items_sum_quantity')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        $data['dashboardBanners'] = [
            ['image' => 'assets/theme/images/banner/s1.png', 'alt' => 'বন্ধন গ্রুপ অফার ১'],
            ['image' => 'assets/theme/images/banner/s2.png', 'alt' => 'বন্ধন গ্রুপ অফার ২'],
            ['image' => 'assets/theme/images/banner/s3.png', 'alt' => 'বন্ধন গ্রুপ অফার ৩'],
        ];

        return view('theme.user.dashboard', $data);
    }
}
