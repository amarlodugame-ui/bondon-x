@props(['type' => 'info', 'title' => null, 'message' => '', 'icon' => null, 'template' => false])

@php
    $variants = [
        'success' => ['green', 'Success!', 'check'],
        'error' => ['red', 'Oops!', 'error'],
        'warning' => ['amber', 'Warning!', 'warning'],
        'info' => ['blue', 'Information', 'info'],
        'special' => ['purple', 'Special Offer!', 'gift'],
    ];
    $type = isset($variants[$type]) ? $type : 'info';
    [$color, $defaultTitle, $defaultIcon] = $variants[$type];
    $title ??= __($defaultTitle);
    $icon = in_array($icon, ['cart', 'check', 'lock', 'warning', 'gift', 'x', 'error', 'info', 'order', 'handbag'], true) ? $icon : $defaultIcon;
    $illustrated = in_array($icon, ['order', 'gift', 'handbag'], true);
@endphp

@unless($template)
    @include('partials.notify-assets')
@endunless

@if($template)
    <template data-notify-template="{{ $type }}-{{ $icon }}">
@endif

<div {{ $attributes->class(['notify-toast', 'notify-'.$color, 'notify-product' => $icon === 'handbag']) }}
    @unless($template) data-notify data-notify-type="{{ $type }}" @endunless
    role="{{ $type === 'error' ? 'alert' : 'status' }}" aria-atomic="true">
    @unless($illustrated)
        <span class="notify-shape notify-a" aria-hidden="true"></span>
    @endunless
    @unless($icon === 'handbag')
        <span class="notify-shape notify-b" aria-hidden="true"></span>
    @endunless
    <span class="notify-shape notify-c" aria-hidden="true"></span>
    @if($color === 'green')
        <span class="notify-shape notify-d" aria-hidden="true"></span>
    @endif
    <span class="notify-shine" aria-hidden="true"></span><span class="notify-grain" aria-hidden="true"></span>

    @if($icon === 'order')
        <div class="notify-illustration-zone" aria-hidden="true">
            <div class="notify-confetti"><i class="notify-c1"></i><i class="notify-c2"></i><i class="notify-c3"></i><i class="notify-c4"></i><i class="notify-c5"></i><i class="notify-c6"></i></div>
            <svg class="notify-bag-illu"><use href="#order" /></svg>
        </div>
    @elseif($icon === 'gift')
        <div class="notify-gift-zone" aria-hidden="true">
            <span class="notify-spark notify-star notify-s1"></span><span class="notify-spark notify-star notify-s2"></span><span class="notify-spark notify-star notify-s3"></span><span class="notify-spark notify-dot notify-d1"></span><span class="notify-spark notify-dot notify-d2"></span>
            <svg class="notify-gift-illu"><use href="#gift" /></svg>
        </div>
    @else
        <div class="notify-icon-zone" aria-hidden="true">
            @if($icon === 'handbag')
                <div class="notify-thumb"><svg class="notify-handbag"><use href="#handbag" /></svg></div>
            @else
                <span class="notify-halo2"></span><span class="notify-halo1"></span>
                <div class="notify-icon-disc"><svg><use href="#{{ $icon }}" /></svg></div>
            @endif
            @if($type === 'success' && $icon !== 'check')
                <span class="notify-tick-badge"><svg><use href="#check" /></svg></span>
            @endif
        </div>
    @endif

    <div class="notify-copy"><h3>{{ $title }}</h3><p>{{ $message }}</p></div>
    <button type="button" class="notify-close" data-notify-close aria-label="{{ __('Close notification') }}">
        <svg aria-hidden="true"><use href="#x" /></svg>
    </button>
    @unless(in_array($icon, ['order', 'gift'], true))
        <div class="notify-progress" aria-hidden="true"><span></span></div>
    @endunless
    @if($icon === 'cart' && $type === 'success')
        <div class="notify-confetti" aria-hidden="true"><i class="notify-c3" style="left:268px;top:28px;background:#5de693"></i><i class="notify-c2" style="left:281px;top:35px;background:#f7c54a"></i><i class="notify-c6" style="left:274px;top:54px;background:#ef9ad6"></i></div>
    @elseif($icon === 'lock' && $type === 'info')
        <div class="notify-confetti" aria-hidden="true"><i class="notify-c6" style="left:276px;top:30px;background:#8bc8ff"></i><i class="notify-c2" style="left:289px;top:40px;background:#c2e1ff"></i></div>
    @endif
</div>
@if($template)
    </template>
@endif
