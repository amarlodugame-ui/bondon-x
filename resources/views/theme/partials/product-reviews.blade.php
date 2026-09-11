<div class="review-summary">
    <div class="rating-big">
        <strong>{{ number_format($rating, 1) }}</strong>
        <div class="rating-stars" aria-label="রেটিং {{ number_format($rating, 1) }}">
            @for($star = 1; $star <= 5; $star++)<i class="{{ $star <= round($rating) ? 'fa-solid' : 'fa-regular' }} fa-star" aria-hidden="true"></i>@endfor
        </div>
        <span>{{ $reviewCount }}টি রিভিউ</span>
    </div>
    <div class="rating-bars">
        @foreach([5,4,3,2,1] as $star)
            @php($percent = $reviewCount ? round(($reviewStats[$star] ?? 0) / $reviewCount * 100) : 0)
            <div class="rating-bar"><span>{{ $star }}★</span><div class="bar-track"><div class="bar-fill" style="width:{{ $percent }}%"></div></div><span>{{ $percent }}%</span></div>
        @endforeach
    </div>
</div>
<section class="review-editor" aria-labelledby="reviewEditorTitle">
    <h3 id="reviewEditorTitle">{{ $ownReview ? 'আপনার রিভিউ পরিবর্তন করুন' : 'আপনার রিভিউ দিন' }}</h3>
    @auth
        <p>এই পণ্য সম্পর্কে আপনার মতামত লিখুন। প্রতি পণ্যে নিজের রিভিউ আপডেট করতে পারবেন।</p>
        <form id="reviewForm" action="{{ route('product.review', $product->slug) }}" method="post" enctype="multipart/form-data" data-open="{{ $errors->review->any() || session('review_success') ? 'true' : 'false' }}">
            @csrf
            <fieldset class="review-rating-input">
                <legend>আপনার রেটিং</legend>
                @foreach([1,2,3,4,5] as $star)
                    <label class="review-star-option"><input type="radio" name="rating" value="{{ $star }}" required @checked((int) old('rating', $ownReview?->rating) === $star)><span>{{ $star }} <i class="fa-solid fa-star" aria-hidden="true"></i></span></label>
                @endforeach
            </fieldset>
            <label for="reviewComment" class="review-comment-label">আপনার মন্তব্য</label>
            <textarea id="reviewComment" name="review" required maxlength="2000" rows="4" placeholder="পণ্য সম্পর্কে আপনার মতামত লিখুন…">{{ old('review', $ownReview?->review) }}</textarea>
            <div class="review-upload">
                @if($ownReview?->imageFiles())
                    <p class="review-comment-label">আগের ছবি — সরাতে চাইলে ছবির নিচে টিক দিন</p>
                    <div class="review-photo-grid">
                        @foreach($ownReview->imageFiles() as $filename)
                            <label class="review-existing-photo"><img src="{{ asset('assets/theme/images/product_reviews/'.$filename) }}" alt="আপনার রিভিউয়ের ছবি {{ $loop->iteration }}" loading="lazy"><span><input type="checkbox" name="remove_images[]" value="{{ $filename }}" @checked(in_array($filename, old('remove_images', [])))> ছবিটি সরান</span></label>
                        @endforeach
                    </div>
                @endif
                <label for="reviewImages" class="review-comment-label">ছবি যোগ করুন (ঐচ্ছিক)</label>
                <input id="reviewImages" type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple aria-describedby="reviewImageHelp">
                <p id="reviewImageHelp">পুরোনো ও নতুন মিলিয়ে সর্বোচ্চ ৬টি ছবি। JPG, PNG বা WebP; প্রতিটি সর্বোচ্চ ২ MB, একবারে মোট ৬ MB। ছবি সরানো বা যোগ করা কার্যকর হবে রিভিউ সেভ করলে।</p>
                <div id="reviewImagePreviews" class="review-photo-grid" aria-live="polite"></div>
            </div>
            <div class="review-form-footer"><span>সর্বোচ্চ ২০০০ অক্ষর</span><button type="submit" class="review-submit">{{ $ownReview ? 'রিভিউ আপডেট করুন' : 'রিভিউ জমা দিন' }}</button></div>
            <p class="review-form-status {{ $errors->review->any() ? 'error' : '' }}" role="status" aria-live="polite">{{ $errors->review->first() ?: session('review_success') }}</p>
        </form>
    @else
        <p>রিভিউ দিতে আপনার অ্যাকাউন্টে লগইন করুন। লগইনের পর এখানেই ফিরে আসবেন।</p>
        <a class="review-login-link" href="{{ route('product.review.form', $product->slug) }}"><i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i> লগইন করে রিভিউ দিন</a>
    @endauth
</section>
<div class="purchase-reviews">
    @forelse($reviews as $review)
        <article><strong>{{ $review->user?->name ?? 'ব্যবহারকারী' }}</strong><span>{{ $review->rating }} ★ · {{ $review->updated_at?->format('d M Y') }}</span><p>{{ $review->review }}</p>
            @if($review->imageFiles())<div class="review-photo-grid review-published-photos">@foreach($review->imageFiles() as $filename)<button type="button" data-review-image aria-label="রিভিউয়ের ছবি {{ $loop->iteration }} বড় করে দেখুন" aria-haspopup="dialog" aria-controls="productGallery"><img src="{{ asset('assets/theme/images/product_reviews/'.$filename) }}" alt="রিভিউয়ের ছবি {{ $loop->iteration }}" loading="lazy" width="120" height="120"></button>@endforeach</div>@endif
        </article>
    @empty
        <p>এখনও কোনো রিভিউ নেই। প্রথম রিভিউটি আপনি দিন!</p>
    @endforelse
</div>
@if($reviews->hasPages())
    <div class="purchase-review-pages">
        @if($reviews->previousPageUrl())<a href="{{ $reviews->previousPageUrl() }}#reviews">আগের রিভিউ</a>@endif
        @if($reviews->nextPageUrl())<a href="{{ $reviews->nextPageUrl() }}#reviews">পরের রিভিউ</a>@endif
    </div>
@endif
