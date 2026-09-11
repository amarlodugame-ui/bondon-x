@once
    <link rel="stylesheet" href="{{ asset('assets/theme/css/notify.css') }}">
    @include('partials.notify-icons')
    <div class="notify-stack" data-notify-stack aria-label="{{ __('Notifications') }}"></div>
    @foreach(['success', 'error', 'warning', 'info', 'special'] as $templateType)
        <x-notify :type="$templateType" :template="true" />
    @endforeach
    <x-notify type="success" icon="cart" :template="true" />
    <x-notify type="success" icon="order" :template="true" />
    <x-notify type="success" icon="handbag" :template="true" />
    <x-notify type="info" icon="lock" :template="true" />
    <script src="{{ asset('assets/theme/js/notify.js') }}" defer></script>
@endonce
