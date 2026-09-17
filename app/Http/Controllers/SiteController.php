<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiteController extends Controller
{
    public function index()
    {
        $data['pageTitle'] = 'Home';

        $data['categories'] = Category::query()
            ->where('status', 1)
            ->orderBy('sort_order')
            ->select(['id', 'name', 'slug', 'image'])
            ->get();

        $data['randomCategories'] = Category::query()
            ->where('status', 1)
            ->inRandomOrder()
            ->select(['id', 'name', 'slug'])
            ->limit(5)
            ->get();

        $cardRelations = ['brand:id,name', 'category:id,name',
            'flashSale' => fn ($query) => $query->where('status', 1)->where('starts_at', '<=', now())->where('ends_at', '>=', now()),
            'variants' => fn ($query) => $query->where('status', 1)->with('attributeValues.attribute'),
        ];
        $approvedReviews = fn ($query) => $query->where('status', 'approved');
        $cardColumns = ['id', 'category_id', 'brand_id', 'name', 'slug', 'sku', 'image', 'price', 'old_price', 'stock', 'installment_enabled'];
        $cardQuery = Product::query()->select($cardColumns)->with($cardRelations)
            ->withAvg(['reviews as approved_reviews_avg_rating' => $approvedReviews], 'rating')
            ->withCount(['reviews as approved_reviews_count' => $approvedReviews]);

        $data['popularProducts'] = (clone $cardQuery)
            ->withSum('orderItems', 'quantity')
            ->where('status', 1)
            ->where('stock', '>', 0)
            ->orderByDesc('order_items_sum_quantity')
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        $data['installmentProducts'] = (clone $cardQuery)
            ->withSum('orderItems', 'quantity')
            ->where('status', 1)
            ->where('stock', '>', 0)
            ->where('installment_enabled', 1)
            ->whereHas('installmentPlans', function ($query) {
                $query->where('status', 1);
            })
            ->orderByDesc('order_items_sum_quantity')
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        $data['flashSales'] = FlashSale::query()
            ->with(['product' => function ($query) use ($cardColumns, $cardRelations, $approvedReviews) {
                $query->select($cardColumns)->with($cardRelations)
                    ->withAvg(['reviews as approved_reviews_avg_rating' => $approvedReviews], 'rating')
                    ->withCount(['reviews as approved_reviews_count' => $approvedReviews])
                    ->where('status', 1)
                    ->where('stock', '>', 0);
            }])
            ->where('status', 1)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->whereHas('product', function ($query) {
                $query->where('status', 1)->where('stock', '>', 0);
            })
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        $data['flashSaleEnd'] = $data['flashSales']->first()?->ends_at;

        return view('theme.home', $data);
    }
    public function productSuggestions(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:160'],
        ]);

        $search = trim($validated['search'] ?? '');

        if (mb_strlen($search) < 2) {
            return response()->json([
                'products' => [],
            ]);
        }

        $products = Product::query()
            ->where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->orderByRaw(
                'CASE
                    WHEN name LIKE ? THEN 0
                    WHEN sku LIKE ? THEN 1
                    ELSE 2
                END',
                [
                    $search . '%',
                    $search . '%',
                ]
            )
            ->orderByDesc('id')
            ->limit(6)
            ->get([
                'id',
                'name',
                'slug',
                'sku',
            ])
            ->map(function ($product) {
                return [
                    'id'   => $product->id,
                    'name' => $product->name,
                    'sku'  => $product->sku,

                    'url' => route('product.show', [
                        'product' => $product->slug,
                    ]),
                ];
            })
            ->values();

        return response()->json([
            'products' => $products,
        ]);
    }
    public function products(Request $request)
    {
        $filters = $request->validate([
            'type' => ['nullable', Rule::in(['popular', 'installment', 'flash', 'offer'])],
            'category' => ['nullable', 'string', 'max:160'],
            'brand' => ['nullable', 'array', 'max:50'],
            'brand.*' => ['string', 'max:160'],
            'search' => ['nullable', 'string', 'max:160'],
            'min_price' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'max:999999999', ...($request->filled('min_price') ? ['gte:min_price'] : [])],
            'sort' => ['nullable', Rule::in(['popular', 'newest', 'price_low', 'price_high', 'rating'])],
            'installment' => ['nullable', 'boolean'],
            'in_stock' => ['nullable', 'boolean'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'attributes' => ['nullable', 'array', 'max:30'],
            'attributes.*' => ['array', 'max:50'],
            'attributes.*.*' => ['integer', 'min:1'],
            'per_page' => ['nullable', Rule::in([12, 24, 48])],
            'page' => ['nullable', 'integer', 'min:1', 'max:100000'],
        ]);

        $type = $filters['type'] ?? null;
        $brandSlugs = $filters['brand'] ?? [];
        $search = trim($filters['search'] ?? '');
        $sort = $filters['sort'] ?? 'popular';
        $activeSale = fn ($query) => $query->where('status', 1)
            ->where('starts_at', '<=', now())->where('ends_at', '>=', now());
        // Price filters and sorting must use the same active sale price shown on the card.
        $priceSql = 'COALESCE((SELECT sale_price FROM flash_sales WHERE product_id = products.id AND status = 1 AND starts_at <= ? AND ends_at >= ? ORDER BY id LIMIT 1), products.price)';
        $priceBindings = [now(), now()];

        $query = Product::query()
            ->select(['id', 'category_id', 'brand_id', 'name', 'slug', 'sku', 'image', 'short_description', 'price', 'old_price', 'stock', 'installment_enabled', 'status'])
            ->with(['brand:id,name,slug', 'category:id,name,slug', 'flashSale' => $activeSale,
                'variants' => fn ($query) => $query->where('status', 1)->with('attributeValues.attribute'),
            ])
            ->withAvg(['reviews as approved_reviews_avg_rating' => fn ($query) => $query->where('status', 'approved')], 'rating')
            ->withCount(['reviews as approved_reviews_count' => fn ($query) => $query->where('status', 'approved')])
            ->where('status', 1);

        $pageTitle = 'সকল পণ্য';
        if ($type === 'popular') {
            $pageTitle = 'জনপ্রিয় পণ্য';
        } elseif ($type === 'installment') {
            $query->where('installment_enabled', 1)
                ->whereHas('installmentPlans', fn ($query) => $query->where('status', 1));
            $pageTitle = 'কিস্তিতে জনপ্রিয় পণ্য';
        } elseif ($type === 'flash') {
            $query->whereHas('flashSale', $activeSale);
            $pageTitle = 'ফ্ল্যাশ সেল';
        } elseif ($type === 'offer') {
            $query->where(fn ($query) => $query->whereColumn('old_price', '>', 'price')
                ->orWhereHas('flashSale', fn ($query) => $activeSale($query)->whereColumn('sale_price', '<', 'products.price')));
            $pageTitle = 'বিশেষ অফার';
        }

        $selectedCategory = null;
        if (! empty($filters['category'])) {
            $selectedCategory = Category::where('status', 1)->where('slug', $filters['category'])->first();
            $query->where('category_id', $selectedCategory?->id ?? 0);
            $pageTitle = $selectedCategory?->name ?? 'পণ্য পাওয়া যায়নি';
        }
        if ($brandSlugs) {
            $query->whereHas('brand', fn ($query) => $query->where('status', 1)->whereIn('slug', $brandSlugs));
        }
        if ($search !== '') {
            $query->where(fn ($query) => $query->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"));
        }
        foreach (['min_price' => '>=', 'max_price' => '<='] as $key => $operator) {
            if (isset($filters[$key])) {
                $query->whereRaw("{$priceSql} {$operator} ?", [...$priceBindings, $filters[$key]]);
            }
        }
        if ($request->boolean('installment')) {
            $query->where('installment_enabled', 1);
        }
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }
        if (! empty($filters['rating'])) {
            $query->whereRaw('(SELECT AVG(rating) FROM product_reviews WHERE product_id = products.id AND status = ?) >= ?', ['approved', $filters['rating']]);
        }
        $attributeFilters = array_filter($filters['attributes'] ?? []);
        if ($attributeFilters) {
            // All selected attribute groups must match a single available variant.
            $query->whereHas('variants', function ($query) use ($attributeFilters) {
                $query->where('status', 1)->where('stock', '>', 0);
                foreach ($attributeFilters as $attributeId => $values) {
                    $query->whereHas('attributeValues', fn ($query) => $query
                        ->where('attribute_id', $attributeId)->where('status', 1)
                        ->whereHas('attribute', fn ($query) => $query->where('status', 1))
                        ->whereIn('attribute_values.id', $values));
                }
            });
        }

        if (in_array($sort, ['price_low', 'price_high'])) {
            $query->orderByRaw($priceSql.($sort === 'price_low' ? ' ASC' : ' DESC'), $priceBindings);
        } elseif ($sort === 'popular') {
            $query->withSum('orderItems', 'quantity')->orderByDesc('order_items_sum_quantity');
        } elseif ($sort === 'rating') {
            $query->orderByDesc('approved_reviews_avg_rating');
        }
        $products = $query->orderByDesc('id')->paginate((int) ($filters['per_page'] ?? 12))
            ->withPath(route('products'))->appends($request->except('page'));

        $countProducts = function ($query) {
            $query->where('status', 1);
        };
        $data = [
            'pageTitle' => $pageTitle,
            'products' => $products,
            'categories' => Category::where('status', 1)->select(['id', 'name', 'slug'])->withCount(['products' => fn ($query) => $query->where('status', 1)])->orderBy('sort_order')->get(),
            'brands' => Brand::where('status', 1)->select(['id', 'name', 'slug'])
                ->withCount(['products' => $countProducts])->orderBy('sort_order')->get(),
            'attributes' => Attribute::where('status', 1)->with(['values' => fn ($query) => $query
                ->where('status', 1)->whereHas('variants', fn ($query) => $query->where('status', 1)
                ->whereHas('product', $countProducts))->orderBy('sort_order')])->get(),
            'priceCeiling' => max(200000, (int) ceil((float) Product::where('status', 1)->max('price') / 1000) * 1000, (int) ($filters['max_price'] ?? 0)),
            'currentType' => $type,
            'selectedCategory' => $selectedCategory,
            'selectedBrands' => $brandSlugs,
            'currentSort' => $sort,
        ];

        if ($request->expectsJson() || $request->routeIs('products.results')) {
            return response()->json([
                'html' => view('theme.products', $data)->fragment('catalog-results'),
                'total' => $products->total(),
                'title' => $pageTitle,
            ]);
        }

        return view('theme.products', $data);
    }

    public function categories()
    {
        $data['pageTitle'] = 'শ্রেণি বিভাগ';
        $data['categories'] = Category::query()
            ->where('status', 1)
            ->withCount(['products' => function ($query) {
                $query->where('status', 1)->where('stock', '>', 0);
            }])
            ->orderBy('sort_order')
            ->select(['id', 'name', 'slug', 'image'])
            ->get();

        return view('theme.categories', $data);
    }
}
