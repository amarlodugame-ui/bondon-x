document.addEventListener('DOMContentLoaded',function(){
    const hero=document.getElementById('offerHero');
    let heroIndex=0,heroTimer=null;

    function getSlides(){return hero?[...hero.querySelectorAll('[data-offer-slide]')]:[]}
    function getDots(){return hero?[...hero.querySelectorAll('[data-hero-dot]')]:[]}
    function showSlide(index){
        const slides=getSlides();
        if(!slides.length)return;
        heroIndex=(index+slides.length)%slides.length;
        slides.forEach((slide,i)=>slide.classList.toggle('active',i===heroIndex));
        getDots().forEach((dot,i)=>dot.classList.toggle('active',i===heroIndex));
    }
    function startHero(){
        clearInterval(heroTimer);
        if(getSlides().length>1)heroTimer=setInterval(()=>showSlide(heroIndex+1),6500);
    }
    function syncHero(){
        const slides=getSlides();
        const dots=getDots();
        dots.forEach((dot,i)=>{if(i>=slides.length)dot.remove();else dot.dataset.heroDot=i});
        if(!slides.length){clearInterval(heroTimer);return}
        if(heroIndex>=slides.length)heroIndex=0;
        showSlide(heroIndex);
        startHero();
    }

    hero?.querySelector('[data-hero-prev]')?.addEventListener('click',()=>{showSlide(heroIndex-1);startHero()});
    hero?.querySelector('[data-hero-next]')?.addEventListener('click',()=>{showSlide(heroIndex+1);startHero()});
    getDots().forEach(dot=>dot.addEventListener('click',()=>{showSlide(Number(dot.dataset.heroDot));startHero()}));
    hero?.addEventListener('mouseenter',()=>clearInterval(heroTimer));
    hero?.addEventListener('mouseleave',startHero);
    startHero();

    function setTime(root,key,value){const el=root.querySelector(`[data-${key}]`);if(el)el.textContent=String(value).padStart(2,'0')}
    function expireElement(el){
        const wasHero=el.matches('[data-offer-slide]');
        el.remove();
        if(wasHero)syncHero();
        const count=document.querySelectorAll('#flashSaleSection [data-live-sale]').length;
        const countEl=document.querySelector('[data-flash-count]');
        if(countEl)countEl.textContent=`${count} টি অফার`;
        if(!count){
            const grid=document.querySelector('#flashSaleSection .offer-products-grid');
            if(grid&&!grid.querySelector('.offer-empty'))grid.innerHTML='<div class="offer-empty">এই মুহূর্তে কোনো Flash Sale চলছে না।</div>';
        }
    }
    function tick(){
        const now=Date.now();
        document.querySelectorAll('[data-end]').forEach(el=>{
            const end=new Date(el.dataset.end).getTime();
            if(!Number.isFinite(end))return;
            let diff=end-now;
            if(diff<=0){expireElement(el);return}
            const days=Math.floor(diff/86400000);diff%=86400000;
            const hours=Math.floor(diff/3600000);diff%=3600000;
            const minutes=Math.floor(diff/60000);diff%=60000;
            const seconds=Math.floor(diff/1000);
            setTime(el,'days',days);setTime(el,'hours',hours);setTime(el,'minutes',minutes);setTime(el,'seconds',seconds);
        });
    }
    tick();setInterval(tick,1000);

    document.querySelectorAll('[data-copy-coupon]').forEach(button=>button.addEventListener('click',async function(){
        const code=this.dataset.copyCoupon||'';
        try{await navigator.clipboard.writeText(code)}catch(e){const input=document.createElement('input');input.value=code;document.body.appendChild(input);input.select();document.execCommand('copy');input.remove()}
        const label=this.querySelector('span');if(!label)return;const old=label.textContent;label.textContent='কপি হয়েছে';setTimeout(()=>label.textContent=old,1300);
    }));
});
