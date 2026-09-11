<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Deposit;
use App\Models\FlashSale;
use App\Models\GeneralSetting;
use App\Models\Installment;
use App\Models\InstallmentPlan;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductInstallmentPlan;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use App\Models\ShippingZoneRate;
use App\Services\PurchaseService;
use Carbon\Carbon;
use Database\Factories\PurchaseProductFactory;
use Database\Factories\PurchaseUserFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

beforeEach(function () {
    expect(config('database.default'))->toBe('sqlite');
    expect(config('database.connections.sqlite.database'))->toBe(':memory:');
    DB::unprepared(file_get_contents(base_path('tests/Fixtures/purchase-schema.sql')));
    (require base_path('database/migrations/2026_09_07_033412_add_checkout_token_to_orders_table.php'))->up();
    (require base_path('database/migrations/2026_09_07_185929_add_images_to_product_reviews_table.php'))->up();
    $this->withoutVite();
});

function purchaseFixture(): array
{
    GeneralSetting::create(['site_name' => 'Purchase Test', 'cash_order_auto_approve' => true, 'installment_admin_approval' => true]);
    $category = Category::create(['name' => 'Electronics', 'slug' => 'electronics', 'status' => 1]);
    $brand = Brand::create(['name' => 'QA Brand', 'slug' => 'qa-brand', 'status' => 1]);
    $product = PurchaseProductFactory::new()->create(['category_id' => $category->id, 'brand_id' => $brand->id]);
    $user = PurchaseUserFactory::new()->create();
    $address = $user->addresses()->create(['label' => 'Home', 'name' => $user->name, 'mobile' => '01712345678', 'address' => 'Test Road 1', 'district' => 'Dhaka', 'is_default' => true]);
    $shipping = ShippingMethod::create(['name' => 'Inside Dhaka', 'code' => 'inside-dhaka', 'charge' => '60.00', 'status' => true]);

    return compact('user', 'product', 'address', 'shipping', 'category', 'brand');
}

function purchasePlan(Product $product, array $overrides = []): ProductInstallmentPlan
{
    $product->update(['installment_enabled' => true]);
    $terms = InstallmentPlan::create(['name' => 'Monthly x 3', 'interval_unit' => 'month', 'interval_value' => 1, 'installment_count' => 3, 'status' => true]);

    return ProductInstallmentPlan::create(['product_id' => $product->id, 'installment_plan_id' => $terms->id, 'installment_total' => '1200.00', 'down_payment' => '200.00', 'installment_amount' => '333.33', 'status' => true, ...$overrides]);
}

function purchaseQuote(TestCase $test, array $fixture, array $options = []): array
{
    $test->actingAs($fixture['user']);
    $token = (string) Str::uuid();
    $test->withSession(['purchase.checkout_token' => $token]);
    $input = ['address_id' => $fixture['address']->id, 'shipping_method_id' => $fixture['shipping']->id, ...$options];
    $quote = $test->postJson(route('checkout.quote'), $input)->assertOk()->json();

    return [...$input, 'checkout_token' => $token, 'quote_hash' => $quote['hash']];
}

test('product cards link to the real product page and render escaped database content', function () {
    $f = purchaseFixture();
    $f['product']->update(['name' => '<script>alert(1)</script> Product']);
    $this->get(route('products'))->assertSee(route('product.show', $f['product']->slug), false)->assertDontSee('catalog-product-dialog', false);
    $this->get(route('product.show', $f['product']->slug))->assertSee($f['product']->name)->assertDontSee('<script>alert(1)</script>', false);
});

test('inactive products return 404 and cannot be added to the cart', function () {
    $f = purchaseFixture();
    $f['product']->update(['status' => false]);
    $this->get(route('product.show', $f['product']->slug))->assertNotFound();
    $this->actingAs($f['user'])->postJson(route('product.cart', $f['product']->slug), ['quantity' => 1, 'purchase_mode' => 'cash'])->assertUnprocessable();
    $this->assertDatabaseCount('cart_items', 0);
});

test('guest selections survive login and merge into the authenticated database cart', function () {
    $f = purchaseFixture();
    $this->postJson(route('product.cart', $f['product']->slug), ['quantity' => 2, 'purchase_mode' => 'cash'])->assertOk()->assertSessionHas('purchase.pending_cart');
    $this->get(route('checkout'))->assertSee('অর্ডার করতে লগইন করুন');
    $this->post(route('user.login'), ['identity' => $f['user']->email, 'password' => 'purchase-test-password'])
        ->assertRedirect(route('checkout'))
        ->assertSessionHas('success', 'Login successful');
    $this->get(route('checkout'))->assertSee('কার্ট সারাংশ');
    $this->assertDatabaseHas('cart_items', ['product_id' => $f['product']->id, 'quantity' => 2]);
    $this->get(route('checkout'))->assertOk();
    $this->assertDatabaseCount('cart_items', 1);
});

test('invalid credentials never authenticate the default user', function () {
    $f = purchaseFixture();
    $this->post(route('user.login'), ['identity' => $f['user']->email, 'password' => 'wrong-password'])->assertSessionHasErrors('identity');
    $this->assertGuest();
});

test('logout shows the success notification once on the login page', function () {
    $f = purchaseFixture();

    $this->actingAs($f['user'])->get(route('user.logout'))
        ->assertRedirect(route('user.login'))
        ->assertSessionHas('success', 'You have been logged out');

    $this->assertGuest();
    $this->get(route('user.login'))
        ->assertSee('You have been logged out')
        ->assertSee('data-notify-type="success"', false);
    $this->get(route('user.login'))->assertDontSee('You have been logged out');
});

test('blocked or deleted users cannot debit a balance', function (array $state) {
    $f = purchaseFixture();
    $f['user']->update($state);
    $this->actingAs($f['user'])->postJson(route('checkout.store'), [])->assertStatus($state['is_deleted'] ?? false ? 302 : 403);
    $this->assertDatabaseCount('orders', 0);
    expect($f['user']->fresh()->balance)->toBe('10000.00');
})->with(['blocked' => [['status' => 1]], 'deleted' => [['is_deleted' => true]]]);

test('cash orders persist the discounted debit inventory address snapshot and transaction', function () {
    $f = purchaseFixture();
    app(PurchaseService::class)->add($f['user'], $f['product']->id, ['quantity' => 2, 'purchase_mode' => 'cash']);
    Coupon::create(['code' => 'WELCOME100', 'discount_type' => 'fixed', 'discount_value' => '100.00', 'minimum_order' => '1000.00', 'per_user_limit' => 1, 'status' => true]);
    $input = purchaseQuote($this, $f, ['coupon' => 'WELCOME100']);

    $response = $this->postJson(route('checkout.store'), [...$input, 'unit_price' => 1, 'balance_used' => 1, 'customer_note' => '<b>Call first</b>'])->assertOk();

    $order = Order::firstOrFail();
    expect($order->payment_mode)->toBe('cash');
    expect($order->grand_total)->toBe('1960.00');
    expect($order->paid_amount)->toBe('1960.00');
    expect($order->remaining_amount)->toBe('0.00');
    expect($order->order_status)->toBe('approved');
    expect($f['user']->fresh()->balance)->toBe('8040.00');
    expect($f['user']->fresh()->saving_balance)->toBe('5000.00');
    expect($f['product']->fresh()->stock)->toBe(8);
    $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'quantity' => 2, 'unit_price' => 1000]);
    $this->assertDatabaseHas('order_addresses', ['order_id' => $order->id, 'address' => 'Test Road 1']);
    $this->assertDatabaseHas('transactions', ['reference_id' => $order->id, 'amount' => 1960, 'post_balance' => 8040, 'trx_type' => '-', 'wallet_type' => 'balance']);
    $this->assertDatabaseCount('coupon_usages', 1);
    $this->assertDatabaseCount('cart_items', 0);
    $this->get($response->json('redirect'))->assertSee($order->order_no)->assertSee('<b>Call first</b>')->assertDontSee('<b>Call first</b>', false);
});

test('mixed orders debit only cash and down payments and reconcile the final installment cent', function () {
    $this->travelTo(Carbon::parse('2026-01-31 12:00:00'));
    $f = purchaseFixture();
    $plan = purchasePlan($f['product']);
    $service = app(PurchaseService::class);
    $service->add($f['user'], $f['product']->id, ['quantity' => 1, 'purchase_mode' => 'cash']);
    $service->add($f['user'], $f['product']->id, ['quantity' => 2, 'purchase_mode' => 'installment', 'product_installment_plan_id' => $plan->id]);

    $this->postJson(route('checkout.store'), purchaseQuote($this, $f))->assertOk();

    $order = Order::firstOrFail();
    expect($order->payment_mode)->toBe('mixed');
    expect($order->grand_total)->toBe('3460.00');
    expect($order->paid_amount)->toBe('1460.00');
    expect($order->remaining_amount)->toBe('2000.00');
    expect($order->payment_status)->toBe('initial_paid');
    expect($order->order_status)->toBe('pending_approval');
    expect($f['user']->fresh()->balance)->toBe('8540.00');
    expect($f['product']->fresh()->stock)->toBe(7);
    expect(Installment::orderBy('installment_no')->pluck('amount')->all())->toBe(['666.66', '666.66', '666.68']);
    expect(Installment::orderBy('installment_no')->get()->map(fn ($installment) => $installment->due_date->toDateString())->all())->toBe(['2026-02-28', '2026-03-31', '2026-04-30']);
});

test('repeated checkout tokens return one order and debit inventory and balance once', function () {
    $f = purchaseFixture();
    app(PurchaseService::class)->add($f['user'], $f['product']->id, ['quantity' => 1, 'purchase_mode' => 'cash']);
    $input = purchaseQuote($this, $f);
    $first = $this->postJson(route('checkout.store'), $input)->assertOk()->json('order_no');

    $this->postJson(route('checkout.store'), $input)->assertOk()->assertJsonPath('order_no', $first);

    $this->assertDatabaseCount('orders', 1);
    $this->assertDatabaseCount('transactions', 1);
    expect($f['user']->fresh()->balance)->toBe('8940.00');
    expect($f['product']->fresh()->stock)->toBe(9);
});

test('insufficient main balance cannot spend saving balance or partially persist an order', function () {
    $f = purchaseFixture();
    $f['user']->update(['balance' => '500.00', 'saving_balance' => '100000.00']);
    app(PurchaseService::class)->add($f['user'], $f['product']->id, ['quantity' => 1, 'purchase_mode' => 'cash']);

    $this->postJson(route('checkout.store'), purchaseQuote($this, $f))->assertUnprocessable()->assertJsonValidationErrors('order');

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('transactions', 0);
    $this->assertDatabaseCount('cart_items', 1);
    expect($f['product']->fresh()->stock)->toBe(10);
    expect($f['user']->fresh()->balance)->toBe('500.00');
});

test('a changed product price requires a new quote before payment', function () {
    $f = purchaseFixture();
    app(PurchaseService::class)->add($f['user'], $f['product']->id, ['quantity' => 1, 'purchase_mode' => 'cash']);
    $input = purchaseQuote($this, $f);
    $f['product']->update(['price' => '1100.00']);

    $this->postJson(route('checkout.store'), $input)->assertUnprocessable()->assertJsonValidationErrors('quote_hash');

    $this->assertDatabaseCount('orders', 0);
    expect($f['user']->fresh()->balance)->toBe('10000.00');
});

test('stock changes leave an unavailable cart removable without a debit', function () {
    $f = purchaseFixture();
    app(PurchaseService::class)->add($f['user'], $f['product']->id, ['quantity' => 2, 'purchase_mode' => 'cash']);
    $input = purchaseQuote($this, $f);
    $f['product']->update(['stock' => 1]);
    $this->postJson(route('checkout.store'), $input)->assertUnprocessable()->assertJsonValidationErrors('quantity');
    $this->postJson(route('checkout.quote'), [])->assertOk()->assertJsonPath('can_order', false);
    $this->get(route('checkout'))->assertSee('কার্ট সারাংশ');
    $this->patchJson(route('checkout.cart'), ['action' => 'remove', 'item_id' => $f['user']->cart->items->first()->id])->assertOk();
    $this->assertDatabaseCount('cart_items', 0);
    $this->assertDatabaseCount('transactions', 0);
});

test('variants must belong to the product and both variant and aggregate stock are deducted', function () {
    $f = purchaseFixture();
    $f['product']->update(['has_variant' => true]);
    $variant = ProductVariant::create(['product_id' => $f['product']->id, 'sku' => 'BLUE', 'price' => '1100.00', 'stock' => 3, 'status' => true]);
    $this->actingAs($f['user'])->postJson(route('product.cart', $f['product']->slug), ['quantity' => 1, 'purchase_mode' => 'cash'])->assertUnprocessable();
    $this->postJson(route('product.cart', $f['product']->slug), ['quantity' => 1, 'purchase_mode' => 'cash', 'product_variant_id' => 999999])->assertUnprocessable();
    $this->postJson(route('product.cart', $f['product']->slug), ['quantity' => 2, 'purchase_mode' => 'cash', 'product_variant_id' => $variant->id])->assertOk();

    $this->postJson(route('checkout.store'), purchaseQuote($this, $f))->assertOk();

    expect($variant->fresh()->stock)->toBe(1);
    expect($f['product']->fresh()->stock)->toBe(8);
    expect(Order::first()->grand_total)->toBe('2260.00');
});

test('active flash sales apply consistently to variant and checkout prices', function () {
    $this->freezeTime();
    $f = purchaseFixture();
    $f['product']->update(['has_variant' => true]);
    $variant = ProductVariant::create(['product_id' => $f['product']->id, 'sku' => 'BLUE', 'price' => '1100.00', 'stock' => 3, 'status' => true]);
    FlashSale::create(['product_id' => $f['product']->id, 'sale_price' => '800.00', 'starts_at' => now()->subDay(), 'ends_at' => now()->addDay(), 'status' => 1]);
    app(PurchaseService::class)->add($f['user'], $f['product']->id, ['quantity' => 1, 'purchase_mode' => 'cash', 'product_variant_id' => $variant->id]);

    $this->postJson(route('checkout.store'), purchaseQuote($this, $f))->assertOk();

    expect(Order::first()->grand_total)->toBe('960.00');
});

test('foreign and invalid installment plans cannot be selected', function () {
    $f = purchaseFixture();
    $other = PurchaseProductFactory::new()->create(['category_id' => $f['category']->id]);
    $plan = purchasePlan($other);
    $this->actingAs($f['user'])->postJson(route('product.cart', $f['product']->slug), ['quantity' => 1, 'purchase_mode' => 'installment', 'product_installment_plan_id' => $plan->id])->assertUnprocessable();
    $invalid = purchasePlan($f['product'], ['installment_total' => '9000.00']);
    $this->postJson(route('product.cart', $f['product']->slug), ['quantity' => 1, 'purchase_mode' => 'installment', 'product_installment_plan_id' => $invalid->id])->assertUnprocessable();
    $this->assertDatabaseCount('cart_items', 0);
});

test('address and cart updates never access another user records', function () {
    $f = purchaseFixture();
    app(PurchaseService::class)->add($f['user'], $f['product']->id, ['quantity' => 1, 'purchase_mode' => 'cash']);
    $item = $f['user']->cart->items->first();
    $other = PurchaseUserFactory::new()->create();
    $this->actingAs($other)->postJson(route('checkout.quote'), ['address_id' => $f['address']->id])->assertNotFound();
    $this->patchJson(route('checkout.cart'), ['action' => 'remove', 'item_id' => $item->id])->assertNotFound();
    $this->postJson(route('checkout.address'), [...$f['address']->only(['name', 'mobile', 'address', 'district']), 'id' => $f['address']->id, 'is_default' => true])->assertNotFound();
    $this->assertDatabaseCount('cart_items', 1);
});

test('saved addresses persist and exactly one remains default', function () {
    $f = purchaseFixture();
    $this->actingAs($f['user'])->postJson(route('checkout.address'), ['name' => 'New Recipient', 'mobile' => '01712345678', 'address' => 'New Road', 'district' => 'Dhaka', 'is_default' => true])->assertOk();
    expect($f['address']->fresh()->is_default)->toBeFalse();
    expect($f['user']->addresses()->where('is_default', true)->count())->toBe(1);
    $this->assertDatabaseHas('user_addresses', ['user_id' => $f['user']->id, 'address' => 'New Road']);
});

test('shipping uses the saved address zone rate and rejects an unavailable method', function () {
    $f = purchaseFixture();
    $zone = ShippingZone::create(['name' => 'Other Bangladesh', 'districts' => [], 'status' => true]);
    $outside = ShippingMethod::create(['name' => 'Outside', 'code' => 'outside', 'charge' => 120, 'status' => true]);
    ShippingZoneRate::create(['shipping_zone_id' => $zone->id, 'shipping_method_id' => $outside->id, 'charge' => 150, 'status' => true]);
    $dhaka = ShippingZone::create(['name' => 'Dhaka', 'districts' => ['Dhaka'], 'status' => true]);
    ShippingZoneRate::create(['shipping_zone_id' => $dhaka->id, 'shipping_method_id' => $f['shipping']->id, 'charge' => 60, 'status' => true]);
    $f['address']->update(['district' => 'Rangpur']);
    $this->actingAs($f['user'])->postJson(route('checkout.quote'), ['address_id' => $f['address']->id, 'shipping_method_id' => $outside->id])->assertOk()->assertJsonPath('shipping_charge', 15000);
    $this->postJson(route('checkout.quote'), ['address_id' => $f['address']->id, 'shipping_method_id' => $f['shipping']->id])->assertUnprocessable()->assertJsonValidationErrors('shipping_method_id');
});

test('coupons enforce expiry usage limits minimum order and percentage caps', function () {
    $this->freezeTime();
    $f = purchaseFixture();
    app(PurchaseService::class)->add($f['user'], $f['product']->id, ['quantity' => 2, 'purchase_mode' => 'cash']);
    $coupon = Coupon::create(['code' => 'SAVE10', 'discount_type' => 'percentage', 'discount_value' => 10, 'maximum_discount' => 150, 'minimum_order' => 2000, 'per_user_limit' => 1, 'status' => true]);
    $this->actingAs($f['user'])->postJson(route('checkout.quote'), ['coupon' => 'SAVE10'])->assertOk()->assertJsonPath('discount', 15000);
    $coupon->update(['expires_at' => now()->subMinute()]);
    $this->postJson(route('checkout.quote'), ['coupon' => 'SAVE10'])->assertUnprocessable()->assertJsonValidationErrors('coupon');
    $coupon->update(['expires_at' => null, 'minimum_order' => 3000]);
    $this->postJson(route('checkout.quote'), ['coupon' => 'SAVE10'])->assertUnprocessable()->assertJsonValidationErrors('coupon');
    $coupon->update(['minimum_order' => 1000, 'usage_limit' => 0]);
    $this->postJson(route('checkout.quote'), ['coupon' => 'SAVE10'])->assertUnprocessable()->assertJsonValidationErrors('coupon');
});

test('coupon discount cannot reduce delivery or future installment payments', function () {
    $f = purchaseFixture();
    $plan = purchasePlan($f['product']);
    app(PurchaseService::class)->add($f['user'], $f['product']->id, ['quantity' => 1, 'purchase_mode' => 'installment', 'product_installment_plan_id' => $plan->id]);
    Coupon::create(['code' => 'LARGE', 'discount_type' => 'fixed', 'discount_value' => 1000, 'per_user_limit' => 1, 'status' => true]);
    $this->actingAs($f['user'])->postJson(route('checkout.quote'), ['coupon' => 'LARGE'])->assertOk()->assertJsonPath('discount', 20000)->assertJsonPath('due_today', 6000)->assertJsonPath('remaining', 100000);
});

test('another user cannot view confirmation or reuse an order token', function () {
    $f = purchaseFixture();
    app(PurchaseService::class)->add($f['user'], $f['product']->id, ['quantity' => 1, 'purchase_mode' => 'cash']);
    $input = purchaseQuote($this, $f);
    $url = $this->postJson(route('checkout.store'), $input)->assertOk()->json('redirect');
    $this->actingAs(PurchaseUserFactory::new()->create())->get($url)->assertNotFound();
    $this->postJson(route('checkout.store'), $input)->assertNotFound();
    $this->assertDatabaseCount('transactions', 1);
});

test('wishlist changes persist only for the authenticated user', function () {
    $f = purchaseFixture();
    $url = route('product.wishlist', $f['product']->slug);
    $this->postJson($url, ['saved' => true])->assertUnauthorized();
    $this->actingAs($f['user'])->postJson($url, ['saved' => true])->assertOk()->assertJsonPath('saved', true);
    $this->postJson($url, ['saved' => true])->assertOk();
    $this->assertDatabaseCount('wishlists', 1);
    $this->postJson($url, ['saved' => false])->assertOk();
    $this->assertDatabaseCount('wishlists', 0);
});

test('deposit submissions stay pending and duplicate references cannot credit a wallet', function () {
    $f = purchaseFixture();
    $method = PaymentMethod::create(['name' => 'Test Payment', 'code' => 'test', 'account_number' => '01712345678', 'minimum_amount' => 100, 'maximum_amount' => 10000, 'status' => 1]);
    $data = ['payment_method_id' => $method->id, 'amount' => 500, 'payer_mobile' => '01712345678', 'transaction_id' => 'TEST123'];
    $this->actingAs($f['user'])->postJson(route('checkout.deposit'), $data)->assertOk();
    $this->postJson(route('checkout.deposit'), $data)->assertUnprocessable()->assertJsonValidationErrors('transaction_id');
    $this->assertDatabaseCount('deposits', 1);
    $this->assertDatabaseHas('deposits', ['status' => 0, 'wallet_type' => 'balance', 'amount' => 500]);
    $this->assertDatabaseCount('transactions', 0);
    expect($f['user']->fresh()->balance)->toBe('10000.00');
});

test('unsafe receipt uploads and out of range deposits are rejected', function () {
    Storage::fake('local');
    $f = purchaseFixture();
    $method = PaymentMethod::create(['name' => 'Test Payment', 'code' => 'test', 'account_number' => '01712345678', 'minimum_amount' => 100, 'maximum_amount' => 1000, 'status' => 1]);
    $data = ['payment_method_id' => $method->id, 'amount' => 50, 'payer_mobile' => '01712345678', 'transaction_id' => 'TEST456'];
    $this->actingAs($f['user'])->postJson(route('checkout.deposit'), $data)->assertUnprocessable()->assertJsonValidationErrors('amount');
    $this->postJson(route('checkout.deposit'), [...$data, 'amount' => 500, 'screenshot' => UploadedFile::fake()->create('proof.php', 1, 'application/x-php')])->assertUnprocessable()->assertJsonValidationErrors('screenshot');
    $this->assertDatabaseCount('deposits', 0);
    expect(Storage::disk('local')->allFiles())->toBe([]);
});

test('deposit receipts stay private and a rejected duplicate leaves no orphaned upload', function () {
    Storage::fake('local');
    $f = purchaseFixture();
    $method = PaymentMethod::create(['name' => 'Test Payment', 'code' => 'test', 'account_number' => '01712345678', 'minimum_amount' => 100, 'status' => 1]);
    $data = ['payment_method_id' => $method->id, 'amount' => 500, 'payer_mobile' => '01712345678', 'transaction_id' => 'WITH-PROOF'];
    $this->actingAs($f['user'])->postJson(route('checkout.deposit'), [...$data, 'screenshot' => UploadedFile::fake()->image('proof.png')])->assertOk()->assertJsonPath('deposit.status', 'যাচাই চলছে')->assertJsonPath('deposit.amount', 50000);
    $deposit = Deposit::firstOrFail();
    Storage::disk('local')->assertExists($deposit->screenshot);
    $this->postJson(route('checkout.deposit'), [...$data, 'screenshot' => UploadedFile::fake()->image('duplicate.png')])->assertUnprocessable();
    expect(Storage::disk('local')->allFiles())->toBe([$deposit->screenshot]);
    expect($f['user']->fresh()->balance)->toBe('10000.00');
});

test('bulk cart modes retain cash only products and update installment totals', function () {
    $f = purchaseFixture();
    purchasePlan($f['product']);
    $cashProduct = PurchaseProductFactory::new()->create(['category_id' => $f['category']->id]);
    $service = app(PurchaseService::class);
    foreach ([$f['product'], $cashProduct] as $product) {
        $service->add($f['user'], $product->id, ['quantity' => 1, 'purchase_mode' => 'cash']);
    }
    $this->actingAs($f['user'])->patchJson(route('checkout.cart'), ['action' => 'all_installment'])->assertOk();
    $this->postJson(route('checkout.quote'), [])->assertOk()->assertJsonPath('due_today', 126000)->assertJsonPath('remaining', 100000);
    $this->assertDatabaseHas('cart_items', ['product_id' => $cashProduct->id, 'purchase_mode' => 'cash']);
    $this->patchJson(route('checkout.cart'), ['action' => 'all_cash'])->assertOk();
    $this->postJson(route('checkout.quote'), [])->assertOk()->assertJsonPath('due_today', 206000)->assertJsonPath('remaining', 0);
});

test('invalid negative catalogue prices and delivery charges cannot credit a buyer', function () {
    $f = purchaseFixture();
    $f['product']->update(['price' => '-0.50']);
    $this->actingAs($f['user'])->postJson(route('product.cart', $f['product']->slug), ['quantity' => 1, 'purchase_mode' => 'cash'])->assertUnprocessable()->assertJsonValidationErrors('product');
    $f['product']->update(['price' => 1000]);
    app(PurchaseService::class)->add($f['user'], $f['product']->id, ['quantity' => 1, 'purchase_mode' => 'cash']);
    $f['shipping']->update(['charge' => '-0.50']);
    $this->postJson(route('checkout.quote'), ['shipping_method_id' => $f['shipping']->id])->assertUnprocessable()->assertJsonValidationErrors('shipping_method_id');
    expect($f['user']->fresh()->balance)->toBe('10000.00');
    $this->assertDatabaseCount('transactions', 0);
});

test('invalid quantities and client-selected payment modes are rejected', function (array $input, string $field) {
    $f = purchaseFixture();
    $this->actingAs($f['user'])->postJson(route('product.cart', $f['product']->slug), ['quantity' => 1, 'purchase_mode' => 'cash', ...$input])->assertUnprocessable()->assertJsonValidationErrors($field);
    $this->assertDatabaseCount('cart_items', 0);
})->with(['zero' => [['quantity' => 0], 'quantity'], 'negative' => [['quantity' => -1], 'quantity'], 'fraction' => [['quantity' => 1.5], 'quantity'], 'too many' => [['quantity' => 100], 'quantity'], 'invalid mode' => [['purchase_mode' => 'free'], 'purchase_mode']]);

test('review submission requires login and login returns to the selected product', function () {
    $f = purchaseFixture();
    $url = route('product.review', $f['product']->slug);
    $this->postJson($url, ['rating' => 5, 'review' => 'Good product'])->assertUnauthorized();
    $this->assertDatabaseCount('product_reviews', 0);
    $this->get(route('product.show', $f['product']->slug))->assertSee('লগইন করে রিভিউ দিন')->assertDontSee('id="reviewForm"', false);
    $this->get(route('product.review.form', $f['product']->slug))->assertRedirect(route('user.login'))->assertSessionHas('url.intended', $url);
    $this->get(route('user.login'))->assertSee('রিভিউ দিতে লগইন করুন');
    $this->post(route('user.login'), ['identity' => $f['user']->email, 'password' => 'purchase-test-password'])->assertRedirect($url);
    $this->get($url)->assertRedirect(route('product.show', $f['product']->slug).'#reviews');
    $this->get(route('product.show', $f['product']->slug))->assertSee('id="reviewForm"', false);
});

test('logged in users can publish a review without an order and receive updated review data', function () {
    $f = purchaseFixture();
    $response = $this->actingAs($f['user'])->postJson(route('product.review', $f['product']->slug), ['rating' => 4, 'review' => 'পণ্যটি ভালো লেগেছে।'])->assertOk()->assertJsonPath('count', 1)->assertJsonPath('rating', 4);
    expect($response->json('html'))->toContain('পণ্যটি ভালো লেগেছে।', 'রিভিউ আপডেট করুন');
    $this->assertDatabaseHas('product_reviews', ['user_id' => $f['user']->id, 'product_id' => $f['product']->id, 'order_id' => null, 'rating' => 4, 'status' => 'approved']);
    $this->assertDatabaseCount('orders', 0);
    $this->get(route('product.show', $f['product']->slug))->assertSee('পণ্যটি ভালো লেগেছে।')->assertViewHas('reviewCount', 1)->assertViewHas('rating', 4);
});

test('review updates only change the authenticated users review and ignore forged ownership', function () {
    $f = purchaseFixture();
    $other = PurchaseUserFactory::new()->create();
    $otherReview = ProductReview::create(['product_id' => $f['product']->id, 'user_id' => $other->id, 'rating' => 2, 'review' => 'Another user review', 'status' => 'approved']);
    $url = route('product.review', $f['product']->slug);
    $this->actingAs($f['user'])->postJson($url, ['rating' => 5, 'review' => 'My first review'])->assertOk();
    $this->postJson($url, ['id' => $otherReview->id, 'user_id' => $other->id, 'product_id' => 99999, 'order_id' => 99999, 'status' => 'pending', 'rating' => 4, 'review' => 'My updated review'])->assertOk()->assertJsonPath('count', 2)->assertJsonPath('rating', 3);
    $this->assertDatabaseCount('product_reviews', 2);
    expect($otherReview->fresh()->review)->toBe('Another user review');
    $this->assertDatabaseHas('product_reviews', ['user_id' => $f['user']->id, 'product_id' => $f['product']->id, 'order_id' => null, 'review' => 'My updated review', 'status' => 'approved']);
});

test('review validation rejects invalid stars and missing or oversized comments', function (array $input, string $field) {
    $f = purchaseFixture();
    $this->actingAs($f['user'])->postJson(route('product.review', $f['product']->slug), ['rating' => 5, 'review' => 'Valid review', ...$input])->assertUnprocessable()->assertJsonValidationErrors($field);
    $this->assertDatabaseCount('product_reviews', 0);
})->with([
    'no stars' => [['rating' => null], 'rating'], 'zero stars' => [['rating' => 0], 'rating'],
    'six stars' => [['rating' => 6], 'rating'], 'fractional stars' => [['rating' => 3.5], 'rating'],
    'empty comment' => [['review' => '   '], 'review'], 'oversized comment' => [['review' => str_repeat('x', 2001)], 'review'],
]);

test('review lists escape user content and keep unpublished reviews out of public statistics', function () {
    $f = purchaseFixture();
    ProductReview::create(['product_id' => $f['product']->id, 'user_id' => $f['user']->id, 'rating' => 1, 'review' => 'Hidden pending review', 'status' => 'pending']);
    $other = PurchaseUserFactory::new()->create(['name' => '<img src=x onerror=alert(1)>']);
    $comment = '<script>alert(1)</script> & </textarea> review';
    $response = $this->actingAs($other)->postJson(route('product.review', $f['product']->slug), ['rating' => 5, 'review' => $comment])->assertOk()->assertJsonPath('count', 1)->assertJsonPath('rating', 5);
    expect($response->json('html'))->toContain(e($comment))->not->toContain('<script>alert(1)</script>', '<img src=x onerror=alert(1)>', 'Hidden pending review');
    $this->get(route('product.show', $f['product']->slug))->assertSee($comment)->assertDontSee('Hidden pending review')->assertViewHas('reviewCount', 1);
});

test('inactive products and blocked accounts cannot receive or submit reviews', function () {
    $f = purchaseFixture();
    $url = route('product.review', $f['product']->slug);
    $data = ['rating' => 5, 'review' => 'A review'];
    $f['product']->update(['status' => false]);
    $this->actingAs($f['user'])->postJson($url, $data)->assertNotFound();
    $f['product']->update(['status' => true]);
    $f['user']->update(['status' => 1]);
    $this->postJson($url, $data)->assertForbidden();
    $this->assertDatabaseCount('product_reviews', 0);
});

test('review forms also submit successfully without JavaScript', function () {
    $f = purchaseFixture();
    $this->actingAs($f['user'])->post(route('product.review', $f['product']->slug), ['rating' => 3, 'review' => 'Submitted without JavaScript'])->assertRedirect(route('product.show', $f['product']->slug).'#reviews')->assertSessionHas('review_success');
    $this->get(route('product.show', $f['product']->slug))->assertSee('Submitted without JavaScript')->assertSee('data-open="true"', false);
});

test('review images upload together with generated filenames and render in the public review', function () {
    Storage::fake('product_reviews');
    $f = purchaseFixture();
    $response = $this->actingAs($f['user'])->post(route('product.review', $f['product']->slug), [
        'rating' => 5, 'review' => 'Review with two photos',
        'images' => [UploadedFile::fake()->image('camera.jpg'), UploadedFile::fake()->image('camera.png')],
    ], ['Accept' => 'application/json'])->assertOk();
    $review = ProductReview::firstOrFail();
    expect($review->images)->toHaveCount(2);
    foreach ($review->images as $file) {
        expect($file)->not->toBeIn(['camera.jpg', 'camera.png']);
        Storage::disk('product_reviews')->assertExists($file);
        expect($response->json('html'))->toContain(asset('assets/theme/images/product_reviews/'.$file));
    }
    $this->get(route('product.show', $f['product']->slug))->assertSee('enctype="multipart/form-data"', false)->assertSee('review-published-photos');
});

test('review image edits preserve old photos append new ones and remove only selected photos', function () {
    Storage::fake('product_reviews');
    $f = purchaseFixture();
    $url = route('product.review', $f['product']->slug);
    $input = ['rating' => 5, 'review' => 'Photo review'];
    $this->actingAs($f['user'])->postJson($url, [...$input, 'images' => [UploadedFile::fake()->image('first.jpg')]])->assertOk();
    $review = ProductReview::firstOrFail();
    $first = $review->images[0];
    $this->postJson($url, [...$input, 'review' => 'Text edit preserves photos'])->assertOk();
    expect($review->fresh()->images)->toBe([$first]);
    $this->postJson($url, [...$input, 'images' => [UploadedFile::fake()->image('second.png')]])->assertOk();
    $second = $review->fresh()->images[1];
    $this->postJson($url, [...$input, 'remove_images' => [$first], 'images' => [UploadedFile::fake()->image('replacement.jpg')]])->assertOk();
    expect($review->fresh()->images)->toHaveCount(2)->toContain($second)->not->toContain($first);
    Storage::disk('product_reviews')->assertMissing($first);
    Storage::disk('product_reviews')->assertExists($second);
    $this->postJson($url, [...$input, 'remove_images' => $review->fresh()->images])->assertOk();
    expect($review->fresh()->images)->toBe([]);
    expect(Storage::disk('product_reviews')->allFiles())->toBe([]);
});

test('review image deletion rejects foreign photos and traversal paths and cleans rejected uploads', function () {
    Storage::fake('product_reviews');
    $f = purchaseFixture();
    $other = PurchaseUserFactory::new()->create();
    $filename = UploadedFile::fake()->image('other.jpg')->store('', 'product_reviews');
    ProductReview::create(['product_id' => $f['product']->id, 'user_id' => $other->id, 'rating' => 5, 'review' => 'Other review', 'images' => [$filename], 'status' => 'approved']);
    $url = route('product.review', $f['product']->slug);
    $input = ['rating' => 5, 'review' => 'My review'];
    $this->actingAs($f['user'])->postJson($url, [...$input, 'remove_images' => [$filename], 'images' => [UploadedFile::fake()->image('new.jpg')]])->assertUnprocessable()->assertJsonValidationErrors('remove_images');
    $this->postJson($url, [...$input, 'remove_images' => ['../other.jpg']])->assertUnprocessable()->assertJsonValidationErrors('remove_images.0');
    expect(Storage::disk('product_reviews')->allFiles())->toBe([$filename]);
    $this->assertDatabaseCount('product_reviews', 1);
});

test('review image limits include existing photos and rejected additions leave no files', function () {
    Storage::fake('product_reviews');
    $f = purchaseFixture();
    $input = ['rating' => 5, 'review' => 'Six photos'];
    $images = collect(range(1, 6))->map(fn ($i) => UploadedFile::fake()->image($i.'.jpg'))->all();
    $url = route('product.review', $f['product']->slug);
    $this->actingAs($f['user'])->postJson($url, [...$input, 'images' => $images])->assertOk();
    $review = ProductReview::firstOrFail();
    $this->postJson($url, [...$input, 'images' => [UploadedFile::fake()->image('seventh.jpg')]])->assertUnprocessable()->assertJsonValidationErrors('images');
    expect($review->fresh()->images)->toBe($review->images);
    expect(Storage::disk('product_reviews')->allFiles())->toHaveCount(6);
});

test('review uploads reject scripts svg and oversized images before storing anything', function () {
    Storage::fake('product_reviews');
    $f = purchaseFixture();
    $this->actingAs($f['user']);
    foreach ([UploadedFile::fake()->create('payload.php', 1, 'application/x-php'), UploadedFile::fake()->createWithContent('script.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'), UploadedFile::fake()->image('large.jpg')->size(2049)] as $file) {
        $this->postJson(route('product.review', $f['product']->slug), ['rating' => 5, 'review' => 'Invalid image', 'images' => [$file]])->assertUnprocessable()->assertJsonValidationErrors('images.0');
    }
    $this->assertDatabaseCount('product_reviews', 0);
    expect(Storage::disk('product_reviews')->allFiles())->toBe([]);
});

test('failed review database updates retain old images and clean newly uploaded files', function () {
    Storage::fake('product_reviews');
    $f = purchaseFixture();
    $oldFile = UploadedFile::fake()->image('old.jpg')->store('', 'product_reviews');
    $review = ProductReview::create(['product_id' => $f['product']->id, 'user_id' => $f['user']->id, 'rating' => 5, 'review' => 'Original review', 'images' => [$oldFile], 'status' => 'approved']);
    ProductReview::saving(fn () => throw new RuntimeException('Simulated review database failure'));
    try {
        $this->actingAs($f['user'])->postJson(route('product.review', $f['product']->slug), ['rating' => 4, 'review' => 'Changed review', 'remove_images' => [$oldFile], 'images' => [UploadedFile::fake()->image('new.png')]])->assertServerError();
    } finally {
        ProductReview::flushEventListeners();
    }
    expect($review->fresh()->review)->toBe('Original review');
    expect($review->fresh()->images)->toBe([$oldFile]);
    expect(Storage::disk('product_reviews')->allFiles())->toBe([$oldFile]);
});

test('review uploads enforce the combined request size limit', function () {
    Storage::fake('product_reviews');
    $f = purchaseFixture();
    $files = collect(range(1, 4))->map(fn ($i) => UploadedFile::fake()->image($i.'.jpg')->size(2048))->all();
    $this->actingAs($f['user'])->postJson(route('product.review', $f['product']->slug), ['rating' => 5, 'review' => 'Too many bytes', 'images' => $files])->assertUnprocessable()->assertJsonValidationErrors('images');
    expect(Storage::disk('product_reviews')->allFiles())->toBe([]);
    $this->assertDatabaseCount('product_reviews', 0);
});
