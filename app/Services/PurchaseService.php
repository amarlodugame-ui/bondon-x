<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\GeneralSetting;
use App\Models\Installment;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\Product;
use App\Models\ProductInstallmentPlan;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    public static function cents(string|int|float|null $amount): int
    {
        $value = (string) ($amount ?? 0);
        $parts = explode('.', ltrim($value, '-+'));
        $cents = ((int) $parts[0] * 100) + (int) str_pad(substr($parts[1] ?? '', 0, 2), 2, '0');

        return str_starts_with($value, '-') ? -$cents : $cents;
    }

    public static function decimal(int $cents): string
    {
        return ($cents < 0 ? '-' : '').intdiv(abs($cents), 100).'.'.str_pad((string) (abs($cents) % 100), 2, '0', STR_PAD_LEFT);
    }

    public static function money(int $cents): string
    {
        return '৳ '.number_format($cents / 100, $cents % 100 ? 2 : 0);
    }

    public function activeUser(User $user): void
    {
        abort_unless($user->status === 0 && ! $user->is_deleted, 403, 'এই অ্যাকাউন্ট থেকে কেনাকাটা করা যাবে না।');
    }

    public function product(int $id, bool $lock = false): Product
    {
        return Product::query()->when($lock, fn ($q) => $q->lockForUpdate())
            ->with(['brand', 'category', 'variants' => fn ($q) => $q->orderBy('id')->when($lock, fn ($q) => $q->lockForUpdate())->with('attributeValues.attribute'),
                'installmentPlans' => fn ($q) => $q->orderBy('id')->when($lock, fn ($q) => $q->lockForUpdate())->with(['installmentPlan' => fn ($q) => $q->when($lock, fn ($q) => $q->lockForUpdate())]),
                'flashSale' => fn ($q) => $q->where('status', 1)->where('starts_at', '<=', now())->where('ends_at', '>=', now())->orderBy('id')->when($lock, fn ($q) => $q->lockForUpdate()),
            ])->findOrFail($id);
    }

    public function images(Product $product): array
    {
        return collect([$product->image, ...($product->images ?? [])])->filter()->unique()
            ->filter(fn ($file) => is_string($file) && basename($file) === $file && is_file(public_path('assets/theme/images/products/'.$file)))
            ->map(fn ($file) => asset('assets/theme/images/products/'.$file))->values()->all();
    }

    public function plans(Product $product, ?int $variantId): Collection
    {
        return $product->installmentPlans->filter(fn ($plan) => $product->installment_enabled && $plan->status && $plan->installmentPlan?->status
            && ($plan->product_variant_id === null || (int) $plan->product_variant_id === $variantId)
            && $this->validPlan($plan))->values();
    }

    private function validPlan(ProductInstallmentPlan $plan): bool
    {
        $terms = $plan->installmentPlan;
        $count = (int) $terms?->installment_count;
        $remaining = self::cents($plan->installment_total) - self::cents($plan->down_payment);

        return $count > 0 && $count <= 120 && $terms->interval_value > 0 && $terms->interval_value <= 365
            && in_array($terms->interval_unit, ['day', 'week', 'month', 'year'], true)
            && self::cents($plan->down_payment) >= 0 && $remaining > 0
            && self::cents($plan->installment_amount) > 0
            && abs($remaining - self::cents($plan->installment_amount) * $count) <= $count;
    }

    public function describe(Product $product, ?int $variantId = null): array
    {
        $variant = $variantId ? $product->variants->firstWhere('id', $variantId) : null;
        $price = self::cents($variant?->price ?? $product->price);
        if ($product->flashSale) {
            $price = max(0, $price - self::cents($product->price) + self::cents($product->flashSale->sale_price));
        }
        $old = self::cents($product->flashSale ? ($variant?->price ?? $product->price) : ($variant?->old_price ?? $product->old_price));

        return [
            'id' => $product->id, 'name' => $product->name, 'url' => route('product.show', $product->slug),
            'image' => $this->images($product)[0] ?? null,
            'sku' => $variant?->sku ?? $product->sku,
            'meta' => $variant ? $variant->attributeValues->pluck('value')->implode(' · ') : ($product->brand?->name ?? $product->category?->name ?? ''),
            'unit_price' => $price, 'old_price' => $old,
            'stock' => $variant ? min($product->stock, $variant->stock) : $product->stock,
            'available' => $product->status && (! $variantId || ($variant && $variant->status)),
            'plans' => $this->plans($product, $variantId)->map(fn ($plan) => [
                'id' => $plan->id, 'name' => $plan->installmentPlan->name,
                'total' => self::cents($plan->installment_total), 'down' => self::cents($plan->down_payment),
                'per' => self::cents($plan->installment_amount), 'count' => $plan->installmentPlan->installment_count,
                'interval_unit' => $plan->installmentPlan->interval_unit, 'interval_value' => $plan->installmentPlan->interval_value,
                'grace_days' => $plan->grace_days, 'late_fee_type' => $plan->late_fee_type, 'late_fee_value' => self::cents($plan->late_fee_value),
            ])->all(),
        ];
    }

    public function selection(Product $product, array $input): array
    {
        $variantId = empty($input['product_variant_id']) ? null : (int) $input['product_variant_id'];
        $data = $this->describe($product, $variantId);
        if ($data['unit_price'] < 0) {
            $this->fail('product', 'এই পণ্যের মূল্য সঠিকভাবে নির্ধারণ করা হয়নি।');
        }
        if (! $data['available'] || ($product->has_variant && ! $variantId) || (! $product->has_variant && $variantId)) {
            $this->fail('product_variant_id', 'সঠিক ও উপলব্ধ ভ্যারিয়েন্ট নির্বাচন করুন।');
        }
        $quantity = (int) ($input['quantity'] ?? 1);
        if ($quantity < 1 || $quantity > 99 || $quantity > $data['stock']) {
            $this->fail('quantity', 'চাওয়া পরিমাণ পণ্য স্টকে নেই।');
        }
        $mode = $input['purchase_mode'] ?? 'cash';
        $planId = $input['product_installment_plan_id'] ?? null;
        $plan = $mode === 'installment' ? collect($data['plans'])->firstWhere('id', (int) $planId) : null;
        if (! in_array($mode, ['cash', 'installment'], true) || ($mode === 'installment' && ! $plan)) {
            $this->fail('product_installment_plan_id', 'এই পণ্যের জন্য উপলব্ধ কিস্তির প্ল্যান নির্বাচন করুন।');
        }

        return [...$data, 'product_id' => $product->id, 'product_variant_id' => $variantId, 'quantity' => $quantity,
            'purchase_mode' => $mode, 'product_installment_plan_id' => $plan['id'] ?? null, 'plan' => $plan,
            'initial' => ($plan ? $plan['down'] : $data['unit_price']) * $quantity,
            'total' => ($plan ? $plan['total'] : $data['unit_price']) * $quantity,
        ];
    }

    public function cart(User $user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    public function add(User $user, int $productId, array $input): void
    {
        DB::transaction(function () use ($user, $productId, $input) {
            $user = User::lockForUpdate()->findOrFail($user->id);
            $this->activeUser($user);
            $cart = $this->cart($user);
            $line = $this->selection($this->product($productId, true), $input);
            $existing = $cart->items()->where('product_id', $productId)->where('product_variant_id', $line['product_variant_id'])
                ->where('purchase_mode', $line['purchase_mode'])->where('product_installment_plan_id', $line['product_installment_plan_id'])->first();
            if ($existing) {
                $existing->update(['quantity' => $existing->quantity + $line['quantity']]);
            } else {
                if ($cart->items()->count() >= 50) {
                    $this->fail('cart', 'একবারে সর্বোচ্চ ৫০টি ভিন্ন পণ্য রাখা যাবে।');
                }
                $cart->items()->create(collect($line)->only(['product_id', 'product_variant_id', 'quantity', 'purchase_mode', 'product_installment_plan_id'])->all());
            }
            $this->lines($cart, true);
        }, 3);
    }

    public function lines(Cart $cart, bool $lock = false, bool $strict = true): array
    {
        $items = $cart->items()->orderBy('product_id')->orderBy('id')->when($lock, fn ($q) => $q->lockForUpdate())->get();
        $products = [];
        $lines = [];
        foreach ($items as $item) {
            $product = $products[$item->product_id] ??= $this->product($item->product_id, $lock);
            try {
                $line = $this->selection($product, $item->toArray());
            } catch (ValidationException $exception) {
                if ($strict) {
                    throw $exception;
                }
                $line = [...$this->describe($product, $item->product_variant_id),
                    ...$item->only(['product_id', 'product_variant_id', 'quantity', 'purchase_mode', 'product_installment_plan_id']),
                    'initial' => 0, 'total' => 0, 'plan' => null, 'error' => collect($exception->errors())->flatten()->first()];
            }
            $lines[] = [...$line, 'id' => $item->id];
        }
        foreach (collect($lines)->groupBy('product_id') as $id => $group) {
            if ($group->sum('quantity') > $products[$id]->stock) {
                if ($strict) {
                    $this->fail('quantity', $products[$id]->name.' পর্যাপ্ত স্টকে নেই।');
                }
                foreach ($lines as &$line) {
                    if ($line['product_id'] === $id) {
                        $line['error'] = 'কার্টের মোট পরিমাণ স্টকের চেয়ে বেশি। পরিমাণ কমান বা পণ্য সরান।';
                    }
                }
                unset($line);
            }
            foreach ($group->whereNotNull('product_variant_id')->groupBy('product_variant_id') as $variantId => $variants) {
                if ($variants->sum('quantity') > $products[$id]->variants->firstWhere('id', $variantId)->stock) {
                    if ($strict) {
                        $this->fail('quantity', 'নির্বাচিত ভ্যারিয়েন্ট পর্যাপ্ত স্টকে নেই।');
                    }
                    foreach ($lines as &$line) {
                        if ($line['product_variant_id'] === $variantId) {
                            $line['error'] = 'নির্বাচিত ভ্যারিয়েন্টের মোট পরিমাণ স্টকের চেয়ে বেশি।';
                        }
                    }
                    unset($line);
                }
            }
        }

        return $lines;
    }

    public function deliveryOptions(?UserAddress $address): array
    {
        $district = mb_strtolower(trim($address?->district ?? ''));
        $district = ['ঢাকা' => 'dhaka', 'চট্টগ্রাম' => 'chattogram', 'রাজশাহী' => 'rajshahi', 'খুলনা' => 'khulna', 'সিলেট' => 'sylhet', 'বরিশাল' => 'barishal', 'কুমিল্লা' => 'cumilla'][$district] ?? $district;
        $zones = ShippingZone::where('status', 1)->orderBy('sort_order')->get();
        $zone = $zones->first(fn ($zone) => collect($zone->districts ?? [])->contains(fn ($name) => mb_strtolower(trim($name)) === $district))
            ?? $zones->first(fn ($zone) => empty($zone->districts));

        return ShippingMethod::where('status', 1)->with('zoneRates')->orderBy('sort_order')->get()->map(function ($method) use ($zone, $address) {
            $pickup = in_array($method->code, ['store-pickup', 'pickup'], true);
            $rate = $method->zoneRates->firstWhere('shipping_zone_id', $zone?->id);
            $charge = self::cents($pickup ? $method->charge : ($rate?->charge ?? $method->charge));
            $allowed = $charge >= 0 && ($pickup || ($address && ($method->zoneRates->isEmpty() || ($rate && $rate->status))));

            return ['id' => $method->id, 'name' => $method->name, 'code' => $method->code,
                'charge' => $charge, 'available' => (bool) $allowed];
        })->all();
    }

    public function quote(User $user, array $options, bool $lock = false): array
    {
        $this->activeUser($user);
        $cart = $this->cart($user);
        $lines = $this->lines($cart, $lock, $lock);
        $address = ! empty($options['address_id']) ? $user->addresses()->when($lock, fn ($q) => $q->lockForUpdate())->findOrFail($options['address_id']) : $user->addresses()->orderByDesc('is_default')->orderBy('id')->first();
        $deliveryOptions = $this->deliveryOptions($address);
        $shipping = ! empty($options['shipping_method_id']) ? collect($deliveryOptions)->firstWhere('id', (int) $options['shipping_method_id']) : collect($deliveryOptions)->firstWhere('available', true);
        if ($shipping && ! $shipping['available']) {
            $this->fail('shipping_method_id', 'এই ঠিকানার জন্য ডেলিভারি পদ্ধতিটি প্রযোজ্য নয়।');
        }
        if (! $shipping && ! empty($options['shipping_method_id'])) {
            $this->fail('shipping_method_id', 'উপলব্ধ ডেলিভারি পদ্ধতি নির্বাচন করুন।');
        }
        $initial = array_sum(array_column($lines, 'initial'));
        $subtotal = array_sum(array_column($lines, 'total'));
        $discount = 0;
        $coupon = null;
        if (! empty($options['coupon'])) {
            $coupon = Coupon::where('code', mb_strtoupper(trim($options['coupon'])))->when($lock, fn ($q) => $q->lockForUpdate())->first();
            if (! $coupon || ! $coupon->status || ($coupon->starts_at && $coupon->starts_at->isFuture()) || ($coupon->expires_at && $coupon->expires_at->isPast())
                || ($coupon->usage_limit !== null && $coupon->usages()->count() >= $coupon->usage_limit)
                || $coupon->usages()->where('user_id', $user->id)->count() >= $coupon->per_user_limit) {
                $this->fail('coupon', 'কুপনটি মেয়াদোত্তীর্ণ, ব্যবহারসীমা পূর্ণ অথবা সঠিক নয়।');
            }
            if ($subtotal < self::cents($coupon->minimum_order)) {
                $this->fail('coupon', 'কুপনের জন্য ন্যূনতম অর্ডার '.self::money(self::cents($coupon->minimum_order)).' প্রয়োজন।');
            }
            $discount = match ($coupon->discount_type) {
                'fixed' => self::cents($coupon->discount_value),
                'percentage' => intdiv($subtotal * self::cents($coupon->discount_value) + 5000, 10000),
                default => 0,
            };
            if ($coupon->maximum_discount !== null) {
                $discount = min($discount, self::cents($coupon->maximum_discount));
            }
            // The discount reduces today's product payment, never future installments or delivery.
            $discount = max(0, min($discount, $initial));
        }
        $shippingCharge = $shipping['charge'] ?? 0;
        $due = $initial - $discount + $shippingCharge;
        $balance = self::cents($user->balance);
        $payload = ['lines' => $lines, 'address' => $address?->only(['id', 'label', 'name', 'mobile', 'address', 'area', 'district', 'postal_code', 'is_default']),
            'shipping' => $shipping, 'coupon' => $coupon?->code, 'coupon_id' => $coupon?->id,
            'subtotal' => $subtotal, 'initial' => $initial, 'discount' => $discount, 'shipping_charge' => $shippingCharge,
            'due_today' => $due, 'grand_total' => $subtotal - $discount + $shippingCharge, 'remaining' => $subtotal - $initial,
        ];
        $fingerprint = $payload;
        $fingerprint['lines'] = collect($lines)->map(fn ($line) => collect($line)->except(['stock', 'image', 'available', 'plans', 'url'])->all())->all();

        return [...$payload, 'hash' => hash('sha256', json_encode($fingerprint)), 'balance' => $balance, 'balance_after' => $balance - $due,
            'delivery_options' => $deliveryOptions, 'can_order' => count($lines) > 0 && ! collect($lines)->contains(fn ($line) => ! empty($line['error'])) && $balance >= $due && $shipping && $address];
    }

    public function place(User $user, array $input): Order
    {
        return DB::transaction(function () use ($user, $input) {
            $user = User::lockForUpdate()->findOrFail($user->id);
            $this->activeUser($user);
            $previous = Order::where('checkout_token', $input['checkout_token'])->first();
            if ($previous) {
                abort_unless($previous->user_id === $user->id, 404);

                return $previous;
            }
            $quote = $this->quote($user, $input, true);
            if (! hash_equals($quote['hash'], $input['quote_hash'])) {
                $this->fail('quote_hash', 'পণ্যের তথ্য বা মূল্য বদলেছে। নতুন হিসাব দেখে আবার নিশ্চিত করুন।');
            }
            if (! $quote['can_order']) {
                $this->fail('order', 'ঠিকানা, ডেলিভারি, কার্ট ও পর্যাপ্ত Main Balance নিশ্চিত করুন।');
            }
            $lines = collect($quote['lines']);
            $installmentLines = $lines->where('purchase_mode', 'installment');
            $cashTotal = $lines->where('purchase_mode', 'cash')->sum('total');
            $settings = GeneralSetting::first();
            $needsApproval = $installmentLines->isNotEmpty() ? ($settings?->installment_admin_approval ?? true) : ! ($settings?->cash_order_auto_approve ?? false);
            $address = $quote['address'];
            $order = Order::create([
                'checkout_token' => $input['checkout_token'], 'order_no' => ($settings?->order_prefix ?: 'ORD').'-'.Str::ulid(), 'user_id' => $user->id,
                'payment_mode' => $installmentLines->isEmpty() ? 'cash' : ($cashTotal > 0 ? 'mixed' : 'installment'),
                'subtotal' => self::decimal($quote['subtotal']), 'cash_items_total' => self::decimal($cashTotal),
                'installment_items_total' => self::decimal($installmentLines->sum('total')),
                'initial_payable_amount' => self::decimal($quote['initial']), 'discount_amount' => self::decimal($quote['discount']),
                'shipping_charge' => self::decimal($quote['shipping_charge']), 'grand_total' => self::decimal($quote['grand_total']),
                'installment_total' => $installmentLines->isNotEmpty() ? self::decimal($installmentLines->sum('total')) : null,
                'down_payment' => self::decimal($installmentLines->sum('initial')),
                'balance_used' => self::decimal($quote['due_today']), 'paid_amount' => self::decimal($quote['due_today']),
                'remaining_amount' => self::decimal($quote['remaining']), 'payment_status' => $quote['remaining'] > 0 ? 'initial_paid' : 'paid',
                'order_status' => $needsApproval ? 'pending_approval' : 'approved', 'approved_at' => $needsApproval ? null : now(),
                'shipping_status' => 'not_ready', 'shipping_method_id' => $quote['shipping']['id'],
                'shipping_name' => $address['name'], 'shipping_mobile' => $address['mobile'], 'shipping_address' => $address['address'],
                'shipping_area' => $address['area'], 'shipping_district' => $address['district'], 'customer_note' => $input['customer_note'] ?? null,
            ]);
            OrderAddress::create(['order_id' => $order->id, ...collect($address)->only(['name', 'mobile', 'address', 'area', 'district', 'postal_code'])->all()]);
            foreach ($lines as $line) {
                $plan = $line['plan'];
                $item = $order->items()->create([
                    'product_id' => $line['product_id'], 'product_variant_id' => $line['product_variant_id'], 'product_name' => $line['name'], 'sku' => $line['sku'],
                    'quantity' => $line['quantity'], 'unit_price' => self::decimal($line['unit_price']), 'total_price' => self::decimal($line['total']),
                    'purchase_mode' => $line['purchase_mode'], 'product_installment_plan_id' => $plan['id'] ?? null,
                    'installment_plan_name' => $plan['name'] ?? null, 'installment_total' => $plan ? self::decimal($line['total']) : null,
                    'down_payment' => self::decimal($plan ? $line['initial'] : 0), 'initial_payable' => self::decimal($line['initial']),
                    'installment_amount' => self::decimal(($plan['per'] ?? 0) * $line['quantity']), 'installment_count' => $plan['count'] ?? null,
                    'interval_unit' => $plan['interval_unit'] ?? null, 'interval_value' => $plan['interval_value'] ?? null,
                    'grace_days' => $plan['grace_days'] ?? 0, 'late_fee_type' => $plan['late_fee_type'] ?? null,
                    'late_fee_value' => self::decimal($plan['late_fee_value'] ?? 0),
                ]);
                Product::whereKey($line['product_id'])->decrement('stock', $line['quantity']);
                if ($line['product_variant_id']) {
                    ProductVariant::whereKey($line['product_variant_id'])->decrement('stock', $line['quantity']);
                }
                if ($plan) {
                    $remaining = $line['total'] - $line['initial'];
                    for ($index = 1; $index <= $plan['count']; $index++) {
                        $amount = $index === $plan['count'] ? $remaining : $plan['per'] * $line['quantity'];
                        $remaining -= $amount;
                        $interval = $index * $plan['interval_value'];
                        $dueDate = match ($plan['interval_unit']) {
                            'day' => now()->addDays($interval), 'week' => now()->addWeeks($interval),
                            'month' => now()->addMonthsNoOverflow($interval), 'year' => now()->addYearsNoOverflow($interval),
                        };
                        Installment::create(['order_id' => $order->id, 'order_item_id' => $item->id, 'installment_no' => $index,
                            'due_date' => $dueDate->toDateString(), 'amount' => self::decimal($amount), 'status' => 'pending']);
                    }
                }
            }
            $user->update(['balance' => self::decimal($quote['balance_after'])]);
            Transaction::create(['user_id' => $user->id, 'amount' => self::decimal($quote['due_today']), 'charge' => '0.00',
                'post_balance' => $user->balance, 'trx_type' => '-', 'trx' => 'PUR-'.Str::ulid(), 'wallet_type' => 'balance',
                'remark' => 'order_payment', 'reference_type' => 'order', 'reference_id' => $order->id, 'details' => 'Order '.$order->order_no.' initial payment']);
            if ($quote['coupon_id']) {
                CouponUsage::create(['coupon_id' => $quote['coupon_id'], 'user_id' => $user->id, 'order_id' => $order->id, 'discount_amount' => self::decimal($quote['discount'])]);
            }
            $this->cart($user)->items()->delete();

            return $order;
        }, 3);
    }

    private function fail(string $key, string $message): never
    {
        throw ValidationException::withMessages([$key => $message]);
    }
}
