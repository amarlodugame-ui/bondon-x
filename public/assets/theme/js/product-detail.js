document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('product-page');
    if (!root) return;
    const config = window.productPurchase;
    const form = root.querySelector('#productPurchaseForm');
    const money = cents => '৳ ' + (cents / 100).toLocaleString('en-US', { minimumFractionDigits: cents % 100 ? 2 : 0, maximumFractionDigits: 2 });
    const escape = text => String(text ?? '').replace(/[&<>"']/g, value => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[value]));
    let selected = config.selected, variantId = config.variants.find(item => item.sku === selected.sku)?.variant_id ?? null;
    let quantity = 1, mode = 'cash', planId = selected.plans[0]?.id ?? null, busy = false, imageIndex = 0;
    const message = (text, error = false, icon = null) => window.notify({ type: error ? 'error' : 'success', message: text, icon });
    const failure = error => window.notify({ type: error.status === 401 ? 'info' : error.status === 429 ? 'warning' : 'error', message: error.message, icon: error.status === 401 ? 'lock' : null });
    function render() {
        const plan = selected.plans.find(item => item.id === planId);
        root.querySelector('#mainPrice').textContent = money(selected.unit_price);
        root.querySelector('#oldPrice').textContent = money(selected.old_price);
        root.querySelector('#oldPrice').hidden = selected.old_price <= selected.unit_price;
        root.querySelector('#productSku').textContent = selected.sku;
        root.querySelector('#productStock').textContent = selected.stock > 0 ? '● স্টকে আছে (' + selected.stock + ')' : 'স্টক নেই';
        root.querySelector('#qtyValue').textContent = quantity;
        root.querySelector('#qtyMinus').disabled = quantity <= 1 || busy;
        root.querySelector('#qtyPlus').disabled = quantity >= Math.min(99, selected.stock) || busy;
        root.querySelector('#cashTotal').textContent = 'এককালীন ' + money(selected.unit_price * quantity);
        root.querySelector('#cashContent').hidden = mode !== 'cash';
        root.querySelector('#installmentContent').hidden = mode !== 'installment';
        root.querySelectorAll('[data-mode]').forEach(button => { button.classList.toggle('active', button.dataset.mode === mode); button.setAttribute('aria-pressed', String(button.dataset.mode === mode)); button.disabled = busy || (button.dataset.mode === 'installment' && !selected.plans.length); });
        root.querySelectorAll('[data-variant]').forEach(button => { button.classList.toggle('active', Number(button.dataset.variant) === variantId); button.setAttribute('aria-pressed', String(Number(button.dataset.variant) === variantId)); button.disabled = busy || config.variants.find(item => item.variant_id === Number(button.dataset.variant)).stock < 1; });
        root.querySelector('#emiOptions').innerHTML = selected.plans.map(item => `<button type="button" class="emi-option ${item.id === planId ? 'active' : ''}" data-plan="${item.id}" aria-pressed="${item.id === planId}"><span class="emi-radio"></span><span class="emi-duration"><strong>${escape(item.name)}</strong><span>${item.count}টি কিস্তি</span></span><span class="emi-price"><strong>${money(item.per * quantity)}</strong><span>মোট ${money(item.total * quantity)}</span></span></button>`).join('');
        root.querySelector('#emiSummary').innerHTML = plan ? `<div class="summary-line"><span>আজ ডাউন পেমেন্ট</span><strong>${money(plan.down * quantity)}</strong></div><div class="summary-line"><span>প্রতি কিস্তি × ${plan.count}</span><strong>${money(plan.per * quantity)}</strong></div><div class="summary-line"><span>মোট পরিশোধ</span><strong>${money(plan.total * quantity)}</strong></div><p>প্রথম কিস্তি অর্ডারের ${plan.interval_value} ${escape({day:'দিন',week:'সপ্তাহ',month:'মাস',year:'বছর'}[plan.interval_unit])} পরে। শেষ কিস্তিতে পয়সার সমন্বয় হতে পারে।</p>` : '';
        root.querySelector('#mobilePriceLabel').textContent = mode === 'installment' ? 'আজ ডাউন পেমেন্ট' : 'নগদ মূল্য';
        root.querySelector('#mobilePriceValue').textContent = money((mode === 'installment' && plan ? plan.down : selected.unit_price) * quantity);
        ['buyNow','addToCart','mobileBuy'].forEach(id => root.querySelector('#'+id).disabled = busy || selected.stock < 1 || !selected.available || (config.hasVariant && !variantId));
    }
    async function request(url, body) {
        const multipart = body instanceof FormData;
        const response = await fetch(url, { method: 'POST', headers: {...(multipart ? {} : {'Content-Type':'application/json'}),Accept:'application/json','X-CSRF-TOKEN':form.querySelector('[name="_token"]').value}, body: multipart ? body : JSON.stringify(body) });
        const result = await response.json().catch(() => ({}));
        if (!response.ok) {
            const error = new Error(Object.values(result.errors || {}).flat()[0] || (response.status === 401 ? 'সেশন শেষ হয়েছে। আবার লগইন করুন।' : response.status === 413 ? 'ছবিগুলোর মোট আকার বেশি। কম বা ছোট ছবি দিয়ে আবার চেষ্টা করুন।' : response.status === 419 ? 'সেশন শেষ হয়েছে। পেজ রিফ্রেশ করুন।' : response.status === 429 ? 'কিছুক্ষণ পর আবার চেষ্টা করুন।' : result.message || 'কাজটি সম্পন্ন হয়নি। আবার চেষ্টা করুন।'));
            error.status = response.status;
            throw error;
        }
        return result;
    }
    async function add(checkout) {
        if (busy) return;
        busy = true; render();
        const pending = window.notify({ type: 'info', message: 'কার্টে যোগ করা হচ্ছে…', duration: 0 });
        try {
            const result = await request(form.action, { product_variant_id: variantId, quantity, purchase_mode: mode, product_installment_plan_id: mode === 'installment' ? planId : null });
            if (checkout) window.notify.redirect(result.checkout_url, { type: 'success', message: result.message, icon: 'cart' });
            else message(result.message, false, 'cart');
        } catch (error) { failure(error); }
        finally { pending?.dismiss(); busy = false; render(); }
    }
    form.addEventListener('submit', event => { event.preventDefault(); add(true); });
    root.querySelector('#addToCart').addEventListener('click', () => add(false));
    root.querySelector('#mobileBuy').addEventListener('click', () => add(true));
    root.querySelector('#qtyMinus').addEventListener('click', () => { quantity = Math.max(1, quantity - 1); render(); });
    root.querySelector('#qtyPlus').addEventListener('click', () => { quantity = Math.min(99, selected.stock, quantity + 1); render(); });
    function activateTab(id) {
        if (!root.querySelector(`[data-tab="${id}"]`)) return;
        root.querySelectorAll('[data-tab]').forEach(button => { const active = button.dataset.tab === id; button.classList.toggle('active', active); button.setAttribute('aria-selected', String(active)); });
        root.querySelectorAll('.tab-content-panel').forEach(panel => panel.classList.toggle('active', panel.id === id));
    }
    root.addEventListener('click', event => {
        const button = event.target.closest('button');
        if (button?.dataset.variant && !busy) { variantId = Number(button.dataset.variant); selected = config.variants.find(item => item.variant_id === variantId); quantity = Math.min(quantity, Math.max(1, selected.stock)); planId = selected.plans[0]?.id ?? null; if (!planId) mode = 'cash'; render(); }
        if (button?.dataset.mode && !busy) { mode = button.dataset.mode; render(); }
        if (button?.dataset.plan && !busy) { planId = Number(button.dataset.plan); render(); }
        if (button?.dataset.tab) activateTab(button.dataset.tab);
        const tabLink = event.target.closest('[data-tab-link]'); if (tabLink) activateTab(tabLink.dataset.tabLink);
        if (button?.hasAttribute('data-image-index')) { imageIndex = Number(button.dataset.imageIndex); root.querySelector('#mainProductImage').src = config.images[imageIndex]; root.querySelectorAll('[data-image-index]').forEach(item => item.classList.toggle('active', item === button)); }
    });
    root.querySelector('#saveProduct').addEventListener('click', async event => {
        if (!config.authenticated) { window.notify.redirect(config.loginUrl, { type: 'info', title: 'Please Login', message: 'পছন্দের তালিকায় রাখতে আগে লগইন করুন।', icon: 'lock' }); return; }
        const button = event.currentTarget; button.disabled = true;
        try { const result = await request(config.wishlistUrl, {saved:button.getAttribute('aria-pressed') !== 'true'}); button.setAttribute('aria-pressed', String(result.saved)); button.querySelector('i').className = (result.saved ? 'fa-solid' : 'fa-regular') + ' fa-heart me-1'; message(result.message); }
        catch (error) { failure(error); } finally { button.disabled = false; }
    });
    const gallery = root.querySelector('#productGallery');
    let galleryImages = [], galleryIndex = 0, galleryTrigger = null, swipeStart = null, previousOverflow = '';
    function showImage() {
        const selectedImage = galleryImages[galleryIndex];
        gallery.querySelector('img').src = selectedImage.src;
        gallery.querySelector('img').alt = selectedImage.alt;
        gallery.querySelector('#galleryCount').textContent = (galleryIndex + 1) + ' / ' + galleryImages.length;
        ['galleryPrev','galleryNext'].forEach(id => gallery.querySelector('#'+id).disabled = galleryImages.length < 2);
    }
    function openGallery(images, index, title, trigger) {
        if (!images.length) return;
        galleryImages = images; galleryIndex = index; galleryTrigger = trigger;
        gallery.querySelector('#galleryTitle').textContent = title;
        showImage();
        previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        gallery.showModal();
    }
    function stepImage(direction) {
        if (galleryImages.length < 2) return;
        galleryIndex = (galleryIndex + direction + galleryImages.length) % galleryImages.length;
        showImage();
    }
    ['imageZoom','galleryOpen'].forEach(id => root.querySelector('#'+id)?.addEventListener('click', event => {
        openGallery(config.images.map(src => ({src,alt:root.querySelector('#mainProductImage').alt})), imageIndex, 'পণ্যের ছবি', event.currentTarget);
    }));
    root.addEventListener('click', event => {
        const button = event.target.closest('[data-review-image]');
        if (!button) return;
        const photos = [...button.closest('.review-published-photos').querySelectorAll('[data-review-image]')];
        openGallery(photos.map(photo => ({src:photo.querySelector('img').src,alt:photo.querySelector('img').alt})), photos.indexOf(button), 'রিভিউয়ের ছবি', button);
    });
    gallery.querySelector('.gallery-close').addEventListener('click', () => gallery.close());
    gallery.querySelector('#galleryPrev').addEventListener('click', () => stepImage(-1));
    gallery.querySelector('#galleryNext').addEventListener('click', () => stepImage(1));
    gallery.addEventListener('keydown', event => {
        if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') { event.preventDefault(); stepImage(event.key === 'ArrowLeft' ? -1 : 1); }
    });
    gallery.addEventListener('click', event => {
        const bounds = gallery.getBoundingClientRect();
        if (event.target === gallery && (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom)) gallery.close();
    });
    gallery.addEventListener('close', () => {
        document.body.style.overflow = previousOverflow;
        swipeStart = null;
        galleryTrigger?.isConnected && galleryTrigger.focus({preventScroll:true});
        gallery.querySelector('img').removeAttribute('src');
    });
    const galleryImage = gallery.querySelector('img');
    galleryImage.addEventListener('pointerdown', event => { if (event.pointerType === 'touch') swipeStart = {x:event.clientX,y:event.clientY}; });
    galleryImage.addEventListener('pointerup', event => {
        if (!swipeStart) return;
        const dx = event.clientX - swipeStart.x, dy = event.clientY - swipeStart.y;
        if (Math.abs(dx) > 45 && Math.abs(dx) > Math.abs(dy) * 1.3) stepImage(dx < 0 ? 1 : -1);
        swipeStart = null;
    });
    galleryImage.addEventListener('pointercancel', () => swipeStart = null);
    let reviewFiles = [], reviewPreviewUrls = [];
    function clearReviewPreviews() { reviewPreviewUrls.forEach(url => URL.revokeObjectURL(url)); reviewPreviewUrls = []; }
    function renderReviewPreviews() {
        clearReviewPreviews();
        const input = root.querySelector('#reviewImages'), previews = root.querySelector('#reviewImagePreviews');
        if (!input || !previews) return;
        const transfer = new DataTransfer();
        reviewFiles.forEach(file => transfer.items.add(file));
        input.files = transfer.files;
        previews.replaceChildren();
        reviewFiles.forEach((file,index) => {
            const tile = document.createElement('div'), img = document.createElement('img'), remove = document.createElement('button');
            tile.className = 'review-new-photo';
            const url = URL.createObjectURL(file); reviewPreviewUrls.push(url);
            img.src = url; img.alt = file.name;
            remove.type = 'button'; remove.dataset.removeReviewFile = index; remove.textContent = 'বাদ দিন';
            remove.setAttribute('aria-label', file.name + ' বাদ দিন');
            tile.append(img, remove); previews.append(tile);
        });
    }
    root.addEventListener('change', event => {
        if (event.target.id !== 'reviewImages') return;
        const status = root.querySelector('#reviewForm .review-form-status');
        const candidates = [...reviewFiles];
        for (const file of event.target.files) {
            if (!candidates.some(old => old.name === file.name && old.size === file.size && old.lastModified === file.lastModified)) candidates.push(file);
        }
        const kept = root.querySelectorAll('#reviewForm [name="remove_images[]"]:not(:checked)').length;
        const invalid = candidates.find(file => file.size > 2 * 1024 * 1024 || !['image/jpeg','image/png','image/webp'].includes(file.type));
        const tooLarge = candidates.reduce((total,file) => total + file.size, 0) > 6 * 1024 * 1024;
        if (candidates.length + kept > 6 || invalid || tooLarge) {
            status.textContent = '';
            const validationMessage = invalid ? 'প্রতিটি ছবি JPG, PNG বা WebP এবং সর্বোচ্চ ২ MB হতে হবে।' : tooLarge ? 'একবারে আপলোড করা ছবির মোট আকার সর্বোচ্চ ৬ MB হতে পারবে।' : 'পুরোনো ও নতুন মিলিয়ে সর্বোচ্চ ৬টি ছবি রাখা যাবে। আগে কিছু ছবি সরান।';
            message(validationMessage, true);
        } else { reviewFiles = candidates; status.textContent = ''; status.classList.remove('error'); }
        renderReviewPreviews();
    });
    root.addEventListener('click', event => {
        const button = event.target.closest('[data-remove-review-file]');
        if (!button || button.disabled) return;
        reviewFiles.splice(Number(button.dataset.removeReviewFile), 1); renderReviewPreviews();
    });
    window.addEventListener('pagehide', clearReviewPreviews);
    window.addEventListener('pageshow', event => { if (event.persisted && reviewFiles.length) renderReviewPreviews(); });
    root.addEventListener('submit', async event => {
        const reviewForm = event.target.closest('#reviewForm');
        if (!reviewForm) return;
        event.preventDefault();
        if (reviewForm.getAttribute('aria-busy') === 'true') return;
        const input = new FormData(reviewForm);
        const status = reviewForm.querySelector('.review-form-status');
        const controls = [...reviewForm.querySelectorAll('input,textarea,button')];
        reviewForm.setAttribute('aria-busy', 'true');
        controls.forEach(control => control.disabled = true);
        status.classList.remove('error');
        status.textContent = '';
        const pending = window.notify({ type: 'info', message: 'রিভিউ জমা হচ্ছে…', duration: 0 });
        try {
            const result = await request(reviewForm.action, input);
            clearReviewPreviews(); reviewFiles = [];
            root.querySelector('#reviews').innerHTML = result.html;
            root.querySelector('#reviewForm .review-form-status').textContent = '';
            message(result.message);
            root.querySelector('#tab-reviews').textContent = 'রিভিউ (' + result.count + ')';
            root.querySelector('.rating-row .rating-number').textContent = Number(result.rating).toFixed(1);
            root.querySelector('.rating-row .review-count').textContent = '(' + result.count + 'টি রিভিউ)';
            const stars = root.querySelector('.rating-row .rating-stars');
            stars.setAttribute('aria-label', 'রেটিং ' + result.rating);
            stars.querySelectorAll('i').forEach((icon,index) => icon.className = (index < Math.round(result.rating) ? 'fa-solid' : 'fa-regular') + ' fa-star');
            root.querySelector('#reviewForm button[type="submit"]').focus({preventScroll:true});
        } catch (error) {
            status.classList.add('error');
            status.textContent = '';
            failure(error);
            if (error.status === 401) {
                const link = document.createElement('a');
                link.href = reviewForm.action;
                link.textContent = ' লগইন করুন';
                status.append(link);
            }
        } finally {
            pending?.dismiss();
            reviewForm.setAttribute('aria-busy', 'false');
            controls.forEach(control => control.disabled = false);
        }
    });
    if (location.hash === '#reviews' || root.querySelector('#reviewForm')?.dataset.open === 'true') activateTab('reviews');
    render();
});
