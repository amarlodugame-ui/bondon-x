<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\PurchaseService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function __construct(private PurchaseService $purchase) {}

    public function show(Request $request, Product $product): View
    {
        abort_unless($product->status, 404);
        $product = $this->purchase->product($product->id);
        $variant = $product->has_variant ? $product->variants->where('status', true)->sortByDesc('stock')->first() : null;
        $selected = $this->purchase->describe($product, $variant?->id);
        $variants = $product->variants->where('status', true)->map(fn ($variant) => [
            'variant_id' => $variant->id, ...$this->purchase->describe($product, $variant->id),
        ])->values()->all();
        $reviewData = $this->reviewData($product, $request->user());
        $related = Product::where('status', 1)->where('category_id', $product->category_id)->whereKeyNot($product->id)
            ->with(['brand', 'category', 'installmentPlans.installmentPlan',
                'flashSale' => fn ($query) => $query->where('status', 1)->where('starts_at', '<=', now())->where('ends_at', '>=', now())->orderBy('id'),
            ])->orderByDesc('id')->limit(5)->get()
            ->map(fn ($related) => $this->purchase->describe($related));

        return view('theme.product', ['pageTitle' => $product->name, 'product' => $product, 'selected' => $selected, 'variants' => $variants,
            'images' => $this->purchase->images($product), ...$reviewData, 'related' => $related,
            'shippingMethods' => ShippingMethod::where('status', 1)->orderBy('sort_order')->get(),
            'saved' => $request->user() && Wishlist::where('user_id', $request->user()->id)->where('product_id', $product->id)->exists(),
        ]);
    }

    public function reviewForm(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->status, 404);
        $this->purchase->activeUser($request->user());

        return redirect()->to(route('product.show', $product->slug).'#reviews');
    }

    public function review(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        abort_unless($product->status, 404);
        $this->purchase->activeUser($request->user());
        $input = $request->validateWithBag('review', [
            'rating' => ['required', 'integer', 'between:1,5'],
            'review' => ['required', 'string', 'max:2000'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=8000,max_height=8000'],
            'remove_images' => ['nullable', 'array', 'max:6'],
            'remove_images.*' => ['required', 'string', 'distinct', 'regex:/\A[A-Za-z0-9_-]+\.(?:jpg|jpeg|png|webp)\z/'],
        ], [
            'rating.required' => '১ থেকে ৫ স্টারের মধ্যে রেটিং দিন।',
            'rating.integer' => 'সঠিক স্টার রেটিং নির্বাচন করুন।',
            'rating.between' => 'রেটিং ১ থেকে ৫ স্টারের মধ্যে হতে হবে।',
            'review.required' => 'আপনার মন্তব্য লিখুন।',
            'review.max' => 'মন্তব্য সর্বোচ্চ ২০০০ অক্ষরের হতে পারবে।',
            'images.max' => 'প্রতি রিভিউতে সর্বোচ্চ ৬টি ছবি রাখা যাবে।',
            'images.*.image' => 'সঠিক ছবি নির্বাচন করুন।',
            'images.*.mimes' => 'ছবি JPG, PNG বা WebP হতে হবে।',
            'images.*.max' => 'প্রতিটি ছবি সর্বোচ্চ ২ MB হতে পারবে।',
            'images.*.dimensions' => 'ছবির দৈর্ঘ্য ও প্রস্থ সর্বোচ্চ ৮০০০ পিক্সেল হতে পারবে।',
        ]);
        if (collect($request->file('images', []))->sum(fn ($file) => $file->getSize()) > 6 * 1024 * 1024) {
            throw ValidationException::withMessages(['images' => 'একবারে আপলোড করা ছবির মোট আকার সর্বোচ্চ ৬ MB হতে পারবে।'])->errorBag('review');
        }
        $uploaded = [];
        $disk = Storage::disk('product_reviews');
        try {
            foreach ($request->file('images', []) as $file) {
                $uploaded[] = $file->store('', 'product_reviews');
            }
            $removed = DB::transaction(function () use ($request, $product, $input, $uploaded) {
                $user = User::lockForUpdate()->findOrFail($request->user()->id);
                $this->purchase->activeUser($user);
                abort_unless(Product::whereKey($product->id)->lockForUpdate()->firstOrFail()->status, 404);
                $review = ProductReview::where('user_id', $user->id)->where('product_id', $product->id)->orderBy('id')->first()
                    ?? new ProductReview(['user_id' => $user->id, 'product_id' => $product->id]);
                $current = $review->imageFiles();
                $removed = $input['remove_images'] ?? [];
                if (array_diff($removed, $current)) {
                    throw ValidationException::withMessages(['remove_images' => 'শুধু নিজের এই রিভিউয়ের ছবি সরাতে পারবেন।'])->errorBag('review');
                }
                $images = [...array_values(array_diff($current, $removed)), ...$uploaded];
                if (count($images) > 6) {
                    throw ValidationException::withMessages(['images' => 'পুরোনো ও নতুন মিলিয়ে সর্বোচ্চ ৬টি ছবি রাখা যাবে।'])->errorBag('review');
                }
                $review->fill(['rating' => $input['rating'], 'review' => $input['review'], 'images' => $images, 'status' => 'approved'])->save();

                return $removed;
            }, 3);
        } catch (\Throwable $exception) {
            $disk->delete($uploaded);
            throw $exception;
        }
        if ($removed) {
            try {
                $disk->delete($removed);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }
        $message = 'আপনার রিভিউ সেভ হয়েছে। ধন্যবাদ!';
        if (! $request->expectsJson()) {
            return redirect()->to(route('product.show', $product->slug).'#reviews')->with('review_success', $message);
        }
        $data = $this->reviewData($product, $request->user());

        return response()->json(['message' => $message, 'rating' => round($data['rating'], 1), 'count' => $data['reviewCount'],
            'html' => view('theme.partials.product-reviews', ['product' => $product, ...$data])->render()]);
    }

    private function reviewData(Product $product, ?User $user): array
    {
        $reviews = ProductReview::where('product_id', $product->id)->where('status', 'approved');
        $reviewStats = (clone $reviews)->selectRaw('rating, COUNT(*) AS total')->groupBy('rating')->pluck('total', 'rating');
        $reviewCount = (int) $reviewStats->sum();

        return ['reviewStats' => $reviewStats, 'reviewCount' => $reviewCount,
            'rating' => $reviewCount ? $reviewStats->map(fn ($total, $rating) => $total * $rating)->sum() / $reviewCount : 0,
            'reviews' => $reviews->with('user:id,name')->latest()->orderByDesc('id')->paginate(5, ['*'], 'reviews_page'),
            'ownReview' => $user ? ProductReview::where('product_id', $product->id)->where('user_id', $user->id)->orderBy('id')->first() : null,
        ];
    }

    public function add(Request $request, Product $product): JsonResponse
    {
        $input = $request->validate([
            'product_variant_id' => ['nullable', 'integer', 'min:1'], 'quantity' => ['required', 'integer', 'between:1,99'],
            'purchase_mode' => ['required', Rule::in(['cash', 'installment'])], 'product_installment_plan_id' => ['nullable', 'integer', 'min:1'],
        ]);
        if ($request->user()) {
            $this->purchase->add($request->user(), $product->id, $input);
        } else {
            $this->purchase->selection($this->purchase->product($product->id), $input);
            $pending = $request->session()->get('purchase.pending_cart', []);
            if (count($pending) >= 50) {
                throw ValidationException::withMessages(['cart' => 'কার্ট সেভ করতে লগইন করুন।']);
            }
            $pending[] = ['product_id' => $product->id, ...$input];
            $request->session()->put('purchase.pending_cart', $pending);
        }

        return response()->json(['message' => $request->user() ? 'কার্টে যোগ হয়েছে।' : 'পণ্য রাখা হয়েছে। অর্ডার করতে লগইন করুন।', 'checkout_url' => route('checkout')]);
    }

    public function wishlist(Request $request, Product $product): JsonResponse
    {
        $input = $request->validate(['saved' => ['required', 'boolean']]);
        abort_unless($product->status, 404);
        $saved = DB::transaction(function () use ($request, $product, $input) {
            $user = User::lockForUpdate()->findOrFail($request->user()->id);
            $this->purchase->activeUser($user);
            if ($input['saved']) {
                Wishlist::firstOrCreate(['user_id' => $user->id, 'product_id' => $product->id]);
            } else {
                Wishlist::where('user_id', $user->id)->where('product_id', $product->id)->delete();
            }

            return (bool) $input['saved'];
        });

        return response()->json(['saved' => $saved, 'message' => $saved ? 'পছন্দের তালিকায় সেভ হয়েছে।' : 'পছন্দের তালিকা থেকে সরানো হয়েছে।']);
    }
}
