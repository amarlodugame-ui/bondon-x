<?php

use App\Models\Category;
use App\Models\CommissionLog;
use App\Models\GeneralSetting;
use App\Models\Installment;
use App\Models\Order;
use App\Models\Transaction;
use Database\Factories\PurchaseProductFactory;
use Database\Factories\PurchaseUserFactory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    expect(config('database.default'))->toBe('sqlite');
    expect(config('database.connections.sqlite.database'))->toBe(':memory:');

    DB::unprepared(file_get_contents(base_path('tests/Fixtures/purchase-schema.sql')));
    (require base_path('database/migrations/2026_09_07_033412_add_checkout_token_to_orders_table.php'))->up();

    Schema::create('commission_logs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('to_id');
        $table->unsignedBigInteger('from_id');
        $table->unsignedInteger('level')->default(1);
        $table->decimal('amount', 15, 2)->default(0);
        $table->string('type')->nullable();
        $table->string('details')->nullable();
        $table->timestamps();
    });

    $this->withoutVite();
});

test('dashboard renders real authenticated user data and never leaks another users ledger', function () {
    GeneralSetting::create([
        'site_name' => 'Dashboard Test',
        'cash_order_auto_approve' => true,
        'installment_admin_approval' => true,
    ]);

    $category = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
        'status' => 1,
    ]);

    $product = PurchaseProductFactory::new()->create([
        'category_id' => $category->id,
        'name' => 'Dashboard Earbuds',
        'slug' => 'dashboard-earbuds',
        'price' => '890.00',
        'old_price' => '990.00',
        'stock' => 10,
        'status' => 1,
    ]);

    $user = PurchaseUserFactory::new()->create([
        'balance' => '1234.50',
        'saving_balance' => '678.25',
        'referral_code' => '000001',
        'member_no' => '10000001',
    ]);

    $referral = PurchaseUserFactory::new()->create(['referred_by' => $user->id]);
    $otherUser = PurchaseUserFactory::new()->create();

    CommissionLog::create([
        'to_id' => $user->id,
        'from_id' => $referral->id,
        'level' => 1,
        'amount' => '150.00',
        'type' => 'referral',
        'details' => 'Referral commission',
    ]);

    $order = Order::create([
        'checkout_token' => 'dashboard-test-token',
        'order_no' => 'ORD-DASH-1',
        'user_id' => $user->id,
        'payment_mode' => 'installment',
        'subtotal' => '890.00',
        'cash_items_total' => '0.00',
        'installment_items_total' => '890.00',
        'initial_payable_amount' => '200.00',
        'discount_amount' => '0.00',
        'shipping_charge' => '0.00',
        'grand_total' => '890.00',
        'installment_total' => '890.00',
        'down_payment' => '200.00',
        'balance_used' => '200.00',
        'paid_amount' => '200.00',
        'remaining_amount' => '690.00',
        'payment_status' => 'initial_paid',
        'order_status' => 'approved',
        'shipping_status' => 'not_ready',
        'shipping_name' => $user->name,
        'shipping_mobile' => $user->mobile,
        'shipping_address' => 'Dashboard Road',
    ]);

    $item = $order->items()->create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 1,
        'unit_price' => '890.00',
        'total_price' => '890.00',
        'purchase_mode' => 'installment',
        'installment_total' => '890.00',
        'down_payment' => '200.00',
        'installment_amount' => '230.00',
        'installment_count' => 3,
        'interval_unit' => 'month',
        'interval_value' => 1,
        'initial_payable' => '200.00',
    ]);

    Installment::create([
        'order_id' => $order->id,
        'order_item_id' => $item->id,
        'installment_no' => 1,
        'due_date' => now()->addMonth()->toDateString(),
        'amount' => '230.00',
        'status' => 'pending',
    ]);

    Transaction::create([
        'user_id' => $user->id,
        'amount' => '200.00',
        'charge' => '0.00',
        'post_balance' => '1234.50',
        'trx_type' => '-',
        'trx' => 'DASH-USER-TRX',
        'details' => 'Dashboard user payment',
        'remark' => 'order_payment',
        'wallet_type' => 'balance',
        'reference_type' => 'order',
        'reference_id' => $order->id,
    ]);

    Transaction::create([
        'user_id' => $otherUser->id,
        'amount' => '999.00',
        'charge' => '0.00',
        'post_balance' => '0.00',
        'trx_type' => '+',
        'trx' => 'OTHER-USER-TRX',
        'details' => 'Other user secret transaction',
        'wallet_type' => 'balance',
    ]);

    $this->actingAs($user)
        ->get(route('user.dashboard'))
        ->assertOk()
        ->assertSee('৳ 1,234.50')
        ->assertSee('৳ 678.25')
        ->assertSee('৳ 150.00')
        ->assertSee('000001')
        ->assertSee('10000001')
        ->assertSee('Dashboard Earbuds')
        ->assertSee('ORD-DASH-1')
        ->assertSee('Dashboard user payment')
        ->assertSee('assets/theme/images/banner/s1.png', false)
        ->assertSee('assets/theme/images/banner/s2.png', false)
        ->assertSee('assets/theme/images/banner/s3.png', false)
        ->assertDontSee('Other user secret transaction');
});
