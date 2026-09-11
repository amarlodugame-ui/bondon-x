<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PurchaseService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct(private PurchaseService $purchase) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        if (! $user) {
            $request->session()->put('url.intended', route('checkout'));

            return view('theme.checkout', ['pageTitle' => 'চেকআউট', 'quote' => null]);
        }
        $this->purchase->activeUser($user);
        $pending = $request->session()->get('purchase.pending_cart', []);
        $pendingNotice = null;
        if ($pending) {
            foreach ($pending as $item) {
                try {
                    $this->purchase->add($user, $item['product_id'], $item);
                } catch (ValidationException|ModelNotFoundException $exception) {
                    $pendingNotice = 'আগে রাখা কিছু পণ্য এখন উপলব্ধ নেই। সেগুলো আবার নির্বাচন করুন।';
                }
            }
            $request->session()->forget('purchase.pending_cart');
        }
        $token = $request->session()->get('purchase.checkout_token');
        if (! $token || Order::where('checkout_token', $token)->exists()) {
            $token = (string) Str::uuid();
            $request->session()->put('purchase.checkout_token', $token);
            $request->session()->forget('purchase.options');
        }
        $options = $request->session()->get('purchase.options', []);
        if (! empty($options['address_id']) && ! $user->addresses()->whereKey($options['address_id'])->exists()) {
            $options = [];
        }
        try {
            $quote = $this->purchase->quote($user, $options);
        } catch (ValidationException $exception) {
            $request->session()->forget('purchase.options');
            $quote = $this->purchase->quote($user, []);
            $quote['notice'] = collect($exception->errors())->flatten()->first();
        }
        if ($pendingNotice) {
            $quote['notice'] = $pendingNotice;
        }

        return view('theme.checkout', ['pageTitle' => 'চেকআউট', 'quote' => $quote, 'checkoutToken' => $token,
            'addresses' => $user->addresses()->orderByDesc('is_default')->orderByDesc('id')->get(),
            'paymentMethods' => PaymentMethod::where('status', 1)->orderBy('sort_order')->get(),
            'deposits' => $user->deposits()->with('paymentMethod:id,name')->latest()->limit(5)->get(),
        ]);
    }

    public function quote(Request $request): JsonResponse
    {
        $options = $request->validate(['address_id' => ['nullable', 'integer', 'min:1'], 'shipping_method_id' => ['nullable', 'integer', 'min:1'], 'coupon' => ['nullable', 'string', 'max:100']]);
        $quote = $this->purchase->quote($request->user(), $options);
        $request->session()->put('purchase.options', $options);

        return response()->json($quote);
    }

    public function updateCart(Request $request): JsonResponse
    {
        $data = $request->validate([
            'action' => ['required', Rule::in(['update', 'remove', 'all_cash', 'all_installment'])],
            'item_id' => ['required_if:action,update,remove', 'integer', 'min:1'], 'quantity' => ['sometimes', 'integer', 'between:1,99'],
            'purchase_mode' => ['sometimes', Rule::in(['cash', 'installment'])], 'product_installment_plan_id' => ['nullable', 'integer', 'min:1'],
        ]);
        DB::transaction(function () use ($request, $data) {
            $user = User::lockForUpdate()->findOrFail($request->user()->id);
            $this->purchase->activeUser($user);
            $cart = $this->purchase->cart($user);
            if ($data['action'] === 'remove') {
                $cart->items()->findOrFail($data['item_id'])->delete();

                return;
            }
            $items = $data['action'] === 'update' ? collect([$cart->items()->findOrFail($data['item_id'])]) : $cart->items()->orderBy('product_id')->get();
            foreach ($items as $item) {
                $product = $this->purchase->product($item->product_id, true);
                $input = [...$item->only(['product_variant_id', 'quantity', 'purchase_mode', 'product_installment_plan_id']), ...collect($data)->only(['quantity', 'purchase_mode', 'product_installment_plan_id'])->all()];
                if (in_array($data['action'], ['all_cash', 'all_installment'], true)) {
                    $input['purchase_mode'] = $data['action'] === 'all_cash' ? 'cash' : 'installment';
                }
                $plans = $this->purchase->plans($product, $item->product_variant_id);
                if ($data['action'] === 'all_installment' && $plans->isEmpty()) {
                    $input['purchase_mode'] = 'cash';
                }
                if ($input['purchase_mode'] === 'installment' && empty($input['product_installment_plan_id'])) {
                    $input['product_installment_plan_id'] = $plans->first()?->id;
                }
                $line = $this->purchase->selection($product, $input);
                $item->update(collect($line)->only(['quantity', 'purchase_mode', 'product_installment_plan_id'])->all());
            }
            $this->purchase->lines($cart, true);
        }, 3);

        return response()->json(['message' => 'কার্ট আপডেট হয়েছে।']);
    }

    public function address(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => ['nullable', 'integer', 'min:1'], 'label' => ['nullable', 'string', 'max:50'], 'name' => ['required', 'string', 'max:150'],
            'mobile' => ['required', 'regex:/^(?:\+?88)?01[3-9][0-9]{8}$/'], 'address' => ['required', 'string', 'max:500'],
            'area' => ['nullable', 'string', 'max:150'], 'district' => ['required', 'string', 'max:150'], 'postal_code' => ['nullable', 'string', 'max:20'],
            'is_default' => ['required', 'boolean'],
        ]);
        $address = DB::transaction(function () use ($request, $data) {
            $user = User::lockForUpdate()->findOrFail($request->user()->id);
            $this->purchase->activeUser($user);
            $address = ! empty($data['id']) ? $user->addresses()->findOrFail($data['id']) : $user->addresses()->make();
            if ($data['is_default'] || ! $user->addresses()->exists()) {
                $user->addresses()->update(['is_default' => false]);
                $data['is_default'] = true;
            } elseif ($address->exists && $address->is_default) {
                $data['is_default'] = true;
            }
            $address->fill(collect($data)->except('id')->all())->save();

            return $address;
        }, 3);

        return response()->json(['address' => $address->only(['id', 'label', 'name', 'mobile', 'address', 'area', 'district', 'postal_code', 'is_default']),
            'addresses' => $request->user()->addresses()->orderByDesc('is_default')->orderByDesc('id')->get(), 'message' => 'ঠিকানা সেভ হয়েছে।']);
    }

    public function store(CheckoutRequest $request): JsonResponse
    {
        $input = $request->validated();
        $previous = Order::where('checkout_token', $input['checkout_token'])->where('user_id', $request->user()->id)->first();
        if (! $previous && ! hash_equals((string) $request->session()->get('purchase.checkout_token', ''), $input['checkout_token'])) {
            throw ValidationException::withMessages(['checkout_token' => 'সেশন বদলেছে। চেকআউট পেজ রিফ্রেশ করুন।']);
        }
        $order = $this->purchase->place($request->user(), $input);

        return response()->json(['redirect' => route('order.confirmation', $order->order_no), 'order_no' => $order->order_no]);
    }

    public function confirmation(Request $request, string $orderNo): View
    {
        $this->purchase->activeUser($request->user());
        $order = $request->user()->orders()->where('order_no', $orderNo)->with(['items', 'address', 'shippingMethod', 'installments' => fn ($q) => $q->orderBy('due_date')->orderBy('installment_no')])->firstOrFail();

        return view('theme.order-confirmation', ['pageTitle' => 'অর্ডার নিশ্চিত', 'order' => $order,
            'transaction' => Transaction::where('user_id', $request->user()->id)->where('reference_type', 'order')->where('reference_id', $order->id)->where('trx_type', '-')->first(),
            'otherOrders' => $request->user()->orders()->latest()->paginate(10, ['id', 'order_no', 'grand_total', 'order_status', 'created_at']),
        ]);
    }

    public function deposit(Request $request): JsonResponse
    {
        $this->purchase->activeUser($request->user());
        $data = $request->validate([
            'payment_method_id' => ['required', 'integer'], 'amount' => ['required', 'numeric', 'decimal:0,2', 'min:1', 'max:9999999'],
            'payer_mobile' => ['required', 'regex:/^(?:\+?88)?01[3-9][0-9]{8}$/'], 'transaction_id' => ['required', 'string', 'max:191', 'regex:/^[A-Za-z0-9-]+$/'],
            'screenshot' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $method = PaymentMethod::where('status', 1)->findOrFail($data['payment_method_id']);
        $path = $request->file('screenshot')?->store('deposit-proofs', 'local');
        try {
            $deposit = DB::transaction(function () use ($request, $data, $path, $method) {
                $method = PaymentMethod::where('status', 1)->whereKey($method->id)->lockForUpdate()->firstOrFail();
                $amount = PurchaseService::cents((string) $data['amount']);
                if ($amount < PurchaseService::cents($method->minimum_amount) || ($method->maximum_amount !== null && $amount > PurchaseService::cents($method->maximum_amount))) {
                    throw ValidationException::withMessages(['amount' => 'এই পেমেন্ট পদ্ধতির সর্বনিম্ন/সর্বোচ্চ সীমার মধ্যে টাকা দিন।']);
                }
                if (Deposit::where('payment_method_id', $method->id)->where('transaction_id', mb_strtoupper($data['transaction_id']))->exists()) {
                    throw ValidationException::withMessages(['transaction_id' => 'এই ট্রানজ্যাকশন আইডি আগে জমা দেওয়া হয়েছে।']);
                }

                return Deposit::create([...collect($data)->except('screenshot')->all(), 'user_id' => $request->user()->id,
                    'transaction_id' => mb_strtoupper($data['transaction_id']), 'wallet_type' => 'balance', 'screenshot' => $path, 'status' => 0]);
            }, 3);
        } catch (\Throwable $exception) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }
            throw $exception;
        }

        return response()->json(['message' => 'ডিপোজিট আবেদন জমা হয়েছে। যাচাই ও অনুমোদনের পর Main Balance-এ যোগ হবে।',
            'deposit' => ['id' => $deposit->id, 'method' => $method->name, 'amount' => PurchaseService::cents($deposit->amount), 'status' => 'যাচাই চলছে']]);
    }
}
