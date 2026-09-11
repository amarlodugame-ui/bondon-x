document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('checkout-page'), config = window.purchaseCheckout;
    if (!root || !config) return;
    const el = id => root.querySelector('#'+id);
    const money = cents => (cents < 0 ? '−' : '') + '৳' + (Math.abs(cents) / 100).toLocaleString('en-US', {minimumFractionDigits:cents % 100 ? 2 : 0,maximumFractionDigits:2});
    const escape = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
    let quote = config.quote, addresses = config.addresses, busy = false, valid = true;
    let options = {address_id:quote.address?.id ?? null,shipping_method_id:quote.shipping?.id ?? null,coupon:quote.coupon ?? ''};
    function message(text, error = false) { if (text) window.notify({ type: error ? 'error' : 'success', message: text }); }
    function failure(error) { window.notify({ type: error.status === 401 ? 'info' : error.status === 429 ? 'warning' : 'error', message: error.message, icon: error.status === 401 ? 'lock' : null }); }
    function updateButtons() {
        root.querySelectorAll('button').forEach(button => { if (!button.closest('#depositForm')) button.disabled = busy || button.dataset.unavailable === 'true'; });
        root.querySelectorAll('select[data-action="plan"]').forEach(select => { select.disabled = busy || !quote.lines.find(line => line.id === Number(select.dataset.id))?.plan; });
        ['desktopConfirm','mobileConfirm'].forEach(id=>el(id).disabled = busy || !valid || !quote.can_order);
        root.setAttribute('aria-busy',String(busy));
    }
    async function request(url, body, method = 'POST') {
        const multipart=body instanceof FormData;
        const response=await fetch(url,{method,headers:{Accept:'application/json','X-CSRF-TOKEN':config.csrf,...(multipart?{}:{'Content-Type':'application/json'})},body:multipart?body:JSON.stringify(body)});
        const result=await response.json().catch(()=>({}));
        if(!response.ok){const error=new Error(Object.values(result.errors||{}).flat()[0] || (response.status===401?'লগইন সেশন শেষ হয়েছে। আবার লগইন করুন।':response.status===419?'সেশন শেষ হয়েছে। পেজ রিফ্রেশ করুন।':result.message||'সার্ভারের সঙ্গে যোগাযোগ হয়নি। আবার চেষ্টা করুন।'));error.fields=result.errors||{};error.status=response.status;throw error;}
        return result;
    }
    function renderProducts() {
        el('productList').innerHTML=quote.lines.map(line=>{
            const plan=line.plan, installment=!!plan;
            return `<article class="product-card"><div class="product-summary"><a class="product-image" href="${escape(line.url)}">${line.image?`<img src="${escape(line.image)}" alt="${escape(line.name)}">`:'<i class="bi bi-box-seam purchase-no-image"></i>'}</a><div class="product-info"><h3><a href="${escape(line.url)}">${escape(line.name)}</a></h3><div class="product-meta">${escape(line.meta)} • Unit ${money(line.unit_price)}</div><span class="mode-line ${installment?'installment':''}"><i class="bi ${installment?'bi-calendar2-week':'bi-cash-coin'}"></i> ${installment?'কিস্তিতে • '+escape(plan.name):'নগদে'}</span></div><div class="today-box"><span>${installment?'আজ Down Payment':'আজ Product Price'}</span><strong>${money(line.initial)}</strong></div></div>${line.error?`<p class="purchase-error">${escape(line.error)}</p>`:""}<div class="control-row"><div class="qty"><button type="button" data-action="minus" data-id="${line.id}" aria-label="পরিমাণ কমান" data-unavailable="${line.quantity<=1}">−</button><span>${line.quantity}</span><button type="button" data-action="plus" data-id="${line.id}" aria-label="পরিমাণ বাড়ান" data-unavailable="${line.quantity>=Math.min(99,line.stock)}">+</button></div><div class="mode-toggle"><button type="button" class="${!installment?'active cash':''}" data-action="mode" data-mode="cash" data-id="${line.id}">নগদে</button><button type="button" class="${installment?'active installment':''}" data-action="mode" data-mode="installment" data-id="${line.id}" data-unavailable="${!line.plans.length}">কিস্তিতে</button></div><select class="plan-select" aria-label="কিস্তির প্ল্যান" data-action="plan" data-id="${line.id}" ${!installment?'disabled':''}>${line.plans.length?line.plans.map(p=>`<option value="${p.id}" ${p.id===line.product_installment_plan_id?'selected':''}>${escape(p.name)}</option>`).join(''):'<option>কিস্তি নেই</option>'}</select><button type="button" class="remove-btn" data-action="remove" data-id="${line.id}" aria-label="কার্ট থেকে বাদ দিন"><i class="bi bi-trash3"></i></button></div>${installment?`<details class="installment-details"><summary class="detail-toggle"><i class="bi bi-info-circle"></i> কিস্তির বিস্তারিত <i class="bi bi-chevron-down"></i></summary><div class="detail-box open"><div class="detail-grid"><div class="detail-mini"><span>মোট কিস্তি মূল্য</span><strong>${money(plan.total*line.quantity)}</strong></div><div class="detail-mini"><span>Down Payment</span><strong>${money(plan.down*line.quantity)}</strong></div><div class="detail-mini"><span>প্রতি কিস্তি</span><strong>${money(plan.per*line.quantity)} × ${plan.count}</strong></div></div><p>প্রথম কিস্তি অর্ডারের ${plan.interval_value} ${escape({day:'দিন',week:'সপ্তাহ',month:'মাস',year:'বছর'}[plan.interval_unit])} পরে। শেষ কিস্তিতে পয়সার সমন্বয় হতে পারে।</p></div></details>`:!line.plans.length?'<div class="cash-note"><i class="bi bi-info-circle"></i> এই পণ্যে কিস্তি প্রযোজ্য নয়</div>':''}</article>`;
        }).join('');
        el('emptyCart').style.display=quote.lines.length?'none':'block';
        el('itemCount').textContent=quote.lines.reduce((sum,line)=>sum+line.quantity,0)+' পণ্য';
    }
    function renderAddresses() {
        el('addressPreviewName').textContent=quote.address?.name ?? 'কোনো ঠিকানা নির্বাচিত নেই';
        el('addressPreviewLine').textContent=quote.address ? quote.address.mobile+' • '+quote.address.address : 'নতুন ঠিকানা যোগ করুন';
        el('savedAddressList').innerHTML=addresses.map(address=>`<div class="saved-address-card ${options.address_id===address.id?'active':''}"><button type="button" class="saved-address-select" data-address-id="${address.id}"><span class="saved-address-radio"></span><span><strong>${escape(address.label||'ঠিকানা')} ${address.is_default?'· Default':''}</strong><span>${escape(address.name)} · ${escape(address.mobile)}</span><span>${escape(address.address)}</span></span></button><button type="button" class="saved-address-edit" data-edit-address="${address.id}" aria-label="ঠিকানা সম্পাদনা"><i class="bi bi-pencil-square"></i></button></div>`).join('');
        el('deliveryOptions').innerHTML=quote.delivery_options.map(option=>`<button type="button" class="delivery-option ${quote.shipping?.id===option.id?'active':''}" data-shipping="${option.id}" data-unavailable="${!option.available}"><span class="delivery-radio"></span><strong>${escape(option.name)} · ${money(option.charge)}</strong><span>${option.available?(option.code==='store-pickup'?'শাখা থেকে সংগ্রহ':'ঠিকানা অনুযায়ী চার্জ'):'এই ঠিকানায় প্রযোজ্য নয়'}</span></button>`).join('');
    }
    function render() {
        renderProducts();renderAddresses();
        for(const [id,value] of Object.entries({currentBalance:quote.balance,productsToday:quote.initial,deliveryAmount:quote.shipping_charge,discountAmount:-quote.discount,todayRequired:quote.due_today,mobileTotal:quote.due_today,remainingBalance:quote.balance_after}))el(id).textContent=money(value);
        const shortage=quote.balance_after<0;
        el('balanceStatus').classList.toggle('warn',shortage||!quote.address||!quote.shipping);
        el('balanceStatus').textContent=shortage?'আরও '+money(-quote.balance_after)+' প্রয়োজন':!quote.address?'ডেলিভারি ঠিকানা যোগ করুন':!quote.shipping?'ডেলিভারি পদ্ধতি নির্বাচন করুন':!quote.lines.length?'কার্টে পণ্য যোগ করুন':'✓ পর্যাপ্ত ব্যালেন্স আছে';
        el('depositButton').style.display=shortage?'block':'none';
        el('depositButton').textContent=money(Math.max(0,-quote.balance_after))+' Deposit করুন';
        el('couponInput').value=options.coupon||'';
        el('removeCoupon').hidden=!quote.coupon;
        updateButtons();
    }
    async function refreshQuote(clearInvalidCoupon=false) {
        try {quote=await request(config.urls.quote,options);valid=true;options={address_id:quote.address?.id??null,shipping_method_id:quote.shipping?.id??null,coupon:quote.coupon??''};render();}
        catch(error){if(clearInvalidCoupon&&error.fields?.coupon&&options.coupon){options.coupon='';quote=await request(config.urls.quote,options);valid=true;render();message(error.message+' কুপন সরানো হয়েছে।',true);}else{valid=false;throw error;}}
    }
    async function run(work, text = 'হিসাব আপডেট হচ্ছে…') {
        if(busy)return;busy=true;updateButtons();
        const pending = window.notify({ type: 'info', message: text, duration: 0 });
        try{await work();}
        catch(error){failure(error);}
        finally{pending?.dismiss();busy=false;updateButtons();}
    }
    async function mutate(data) {await request(config.urls.cart,data,'PATCH');await refreshQuote(true);}
    el('productList').addEventListener('click',event=>{
        const button=event.target.closest('[data-action]');if(!button||busy)return;
        const line=quote.lines.find(item=>item.id===Number(button.dataset.id));if(!line)return;
        if(button.dataset.action==='remove')run(()=>mutate({action:'remove',item_id:line.id}));
        if(['minus','plus'].includes(button.dataset.action))run(()=>mutate({action:'update',item_id:line.id,quantity:line.quantity+(button.dataset.action==='plus'?1:-1)}));
        if(button.dataset.action==='mode')run(()=>mutate({action:'update',item_id:line.id,purchase_mode:button.dataset.mode,product_installment_plan_id:button.dataset.mode==='installment'?(line.product_installment_plan_id||line.plans[0]?.id):null}));
    });
    el('productList').addEventListener('change',event=>{const select=event.target.closest('[data-action="plan"]');if(select)run(()=>mutate({action:'update',item_id:Number(select.dataset.id),product_installment_plan_id:Number(select.value)}));});
    el('allCash').addEventListener('click',()=>run(()=>mutate({action:'all_cash'})));
    el('allInstallment').addEventListener('click',()=>run(()=>mutate({action:'all_installment'})));
    el('deliveryOptions').addEventListener('click',event=>{const button=event.target.closest('[data-shipping]');if(button)run(async()=>{options.shipping_method_id=Number(button.dataset.shipping);await refreshQuote();});});
    el('couponForm').addEventListener('submit',event=>{event.preventDefault();run(async()=>{options.coupon=el('couponInput').value.trim();try{await refreshQuote();message(quote.coupon?money(quote.discount)+' ছাড় প্রয়োগ হয়েছে':'');}catch(error){options.coupon=quote.coupon||'';await refreshQuote();throw error;}});});
    el('removeCoupon').addEventListener('click',()=>run(async()=>{options.coupon='';await refreshQuote();message('কুপন সরানো হয়েছে।');}));
    el('editAddressButton').addEventListener('click',()=>el('addressManager').classList.toggle('open'));
    el('closeAddressManager').addEventListener('click',()=>el('addressManager').classList.remove('open'));
    function editAddress(address=null){el('addressForm').reset();for(const [id,key] of Object.entries({addressId:'id',addressLabel:'label',addressName:'name',addressMobile:'mobile',addressFull:'address',addressDistrict:'district',addressArea:'area',addressPostal:'postal_code'}))el(id).value=address?.[key]|| (key==='label'?'বাসা':'');el('addressDefault').checked=address?.is_default||!addresses.length;el('addressFormTitle').textContent=address?'ঠিকানা সম্পাদনা করুন':'নতুন ঠিকানা যোগ করুন';el('addressForm').classList.add('open');el('addressName').focus();}
    el('addNewAddressButton').addEventListener('click',()=>editAddress());
    el('cancelAddressEdit').addEventListener('click',()=>el('addressForm').classList.remove('open'));
    el('savedAddressList').addEventListener('click',event=>{const edit=event.target.closest('[data-edit-address]'),select=event.target.closest('[data-address-id]');if(edit)editAddress(addresses.find(address=>address.id===Number(edit.dataset.editAddress)));else if(select)run(async()=>{options.address_id=Number(select.dataset.addressId);options.shipping_method_id=null;await refreshQuote(true);});});
    el('addressForm').addEventListener('submit',event=>{event.preventDefault();run(async()=>{const data=Object.fromEntries(new FormData(el('addressForm')));data.is_default=el('addressDefault').checked;try{const result=await request(config.urls.address,data);addresses=result.addresses;options.address_id=result.address.id;options.shipping_method_id=null;await refreshQuote(true);el('addressForm').classList.remove('open');message(result.message);}catch(error){throw error;}});});
    async function confirm(){if(busy||!valid||!quote.can_order)return;await run(async()=>{try{const result=await request(config.urls.order,{...options,checkout_token:config.token,quote_hash:quote.hash,customer_note:el('customerNote').value});window.notify.redirect(result.redirect, {type:'success',title:'Order Placed!',message:'আপনার অর্ডার সফলভাবে গ্রহণ করা হয়েছে।',icon:'order'});}catch(error){if(error.fields?.quote_hash||error.fields?.order||error.fields?.quantity)await refreshQuote(true);throw error;}}, 'অর্ডার সংরক্ষণ হচ্ছে…');}
    el('desktopConfirm').addEventListener('click',confirm);el('mobileConfirm').addEventListener('click',confirm);
    el('depositButton').addEventListener('click',()=>{el('depositPanel').open=true;el('depositAmount')&&(el('depositAmount').value=Math.max(1,-quote.balance_after/100).toFixed(2));el('depositPanel').scrollIntoView({block:'start',behavior:'smooth'});});
    function depositInstructions(){const method=config.paymentMethods.find(item=>item.id===Number(el('depositMethod')?.value));if(method)el('depositInstructions').textContent=method.name+' · '+method.account_number+(method.account_name?' · '+method.account_name:'')+'\n'+(method.instructions||'')+'\nসর্বনিম্ন ৳'+method.minimum_amount+(method.maximum_amount?' · সর্বোচ্চ ৳'+method.maximum_amount:'');}
    el('depositMethod')?.addEventListener('change',depositInstructions);depositInstructions();
    el('depositForm')?.addEventListener('submit',async event=>{event.preventDefault();const form=event.currentTarget,button=form.querySelector('button[type="submit"]');if(button.disabled)return;button.disabled=true;const pending=window.notify({type:'info',message:'আবেদন জমা হচ্ছে…',duration:0});try{const result=await request(form.action,new FormData(form));message(result.message);const row=document.createElement('p');row.textContent=result.deposit.method+' · '+money(result.deposit.amount)+' · '+result.deposit.status;root.querySelector('.deposit-history').prepend(row);form.reset();depositInstructions();}catch(error){failure(error);}finally{pending?.dismiss();button.disabled=false;}});
    el('refreshBalance').addEventListener('click',()=>run(()=>refreshQuote(true)));
    render();if(quote.notice)message(quote.notice,true);if(!quote.address){el('addressManager').classList.add('open');editAddress();}
});
