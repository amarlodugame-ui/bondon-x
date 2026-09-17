<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    public function index()
    {
        $now = now();
        $columns = ['id','category_id','brand_id','name','slug','sku','image','price','old_price','stock','has_variant','installment_enabled','status'];
        $relations = [
            'brand:id,name',
            'category:id,name,slug',
            'variants' => fn ($q) => $q->where('status', 1)->with('attributeValues.attribute'),
        ];
        $approvedReviews = fn ($q) => $q->where('status', 'approved');

        $activeFlashSales = FlashSale::query()
            ->with(['product' => function ($q) use ($columns, $relations, $approvedReviews) {
                $q->select($columns)->with($relations)
                    ->withAvg(['reviews as approved_reviews_avg_rating' => $approvedReviews], 'rating')
                    ->withCount(['reviews as approved_reviews_count' => $approvedReviews])
                    ->withSum('orderItems', 'quantity');
            }])
            ->where('status', 1)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>', $now)
            ->whereHas('product', fn ($q) => $q->where('status', 1))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->filter(fn ($sale) => $sale->product)
            ->values();

        $activeFlashSales->each(function ($sale) {
            $sale->product->setRelation('flashSale', $sale);
        });

        $heroSales = $activeFlashSales->take(3)->values();
        $featuredSale = $activeFlashSales->sortByDesc(function ($sale) {
            $base = (float) $sale->product->price;
            return $base > (float) $sale->sale_price ? (($base - (float) $sale->sale_price) / $base) * 100 : 0;
        })->first();
        $flashProductIds = $activeFlashSales->pluck('product_id');

        $highestDiscountProducts = Product::query()
            ->select($columns)
            ->with($relations)
            ->withAvg(['reviews as approved_reviews_avg_rating' => $approvedReviews], 'rating')
            ->withCount(['reviews as approved_reviews_count' => $approvedReviews])
            ->where('status', 1)
            ->whereNotNull('old_price')
            ->where('old_price', '>', 0)
            ->whereColumn('old_price', '>', 'price')
            ->when($flashProductIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $flashProductIds))
            ->orderByRaw('((old_price - price) / old_price) DESC')
            ->limit(6)
            ->get();

        $planRows = DB::table('product_installment_plans as pip')
            ->join('installment_plans as ip', 'ip.id', '=', 'pip.installment_plan_id')
            ->where('pip.status', 1)
            ->where('ip.status', 1)
            ->orderBy('pip.down_payment')
            ->orderBy('pip.installment_amount')
            ->get(['pip.product_id','pip.down_payment','pip.installment_amount','pip.installment_total','ip.installment_count'])
            ->groupBy('product_id')
            ->map(fn ($rows) => $rows->first());

        $installmentProducts = Product::query()
            ->select($columns)
            ->with($relations)
            ->where('status', 1)
            ->where('installment_enabled', 1)
            ->whereIn('id', $planRows->keys())
            ->latest('id')
            ->limit(4)
            ->get()
            ->each(function ($product) use ($planRows) {
                $plan = $planRows->get($product->id);
                $product->setAttribute('offer_plan_down_payment', (float) ($plan->down_payment ?? 0));
                $product->setAttribute('offer_plan_amount', (float) ($plan->installment_amount ?? 0));
                $product->setAttribute('offer_plan_total', (float) ($plan->installment_total ?? 0));
                $product->setAttribute('offer_plan_count', (int) ($plan->installment_count ?? 0));
            });

        $coupons = Coupon::query()
            ->where('status', 1)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', $now))
            ->orderByDesc('discount_value')
            ->limit(3)
            ->get();

        return view('theme.offers', compact('heroSales','featuredSale','activeFlashSales','highestDiscountProducts','installmentProducts','coupons') + [
            'pageTitle' => 'অফার'
        ]);
    }
}
