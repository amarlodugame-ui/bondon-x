@include('partials.notify-assets')

@foreach(['success', 'error', 'warning', 'info', 'special'] as $notificationType)
    @if(session()->has($notificationType))
        @php
            $flash = session($notificationType);
            $notifications = is_array($flash) && array_is_list($flash) ? $flash : [$flash];
        @endphp
        @foreach($notifications as $notification)
            <x-notify
                :type="$notificationType"
                :title="is_array($notification) ? ($notification['title'] ?? null) : null"
                :message="is_array($notification) ? ($notification['message'] ?? '') : $notification"
                :icon="is_array($notification) ? ($notification['icon'] ?? null) : null"
            />
        @endforeach
    @endif
@endforeach

@isset($errors)
    @foreach($errors->getBags() as $errorBag)
        @foreach($errorBag->all() as $validationMessage)
            <x-notify type="error" :title="__('Validation error')" :message="$validationMessage" />
        @endforeach
    @endforeach
@endisset
