<?php

use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

test('session messages display the matching notification', function (string $type, string $title, string $color, string $icon) {
    $this->withSession([$type => 'A notification message']);

    $this->view('partials.notify')
        ->assertSeeText($title)
        ->assertSeeText('A notification message')
        ->assertSee('notify-'.$color, false)
        ->assertSee('href="#'.$icon.'"', false);
})->with([
    'success' => ['success', 'Success!', 'green', 'check'],
    'error' => ['error', 'Oops!', 'red', 'error'],
    'warning' => ['warning', 'Warning!', 'amber', 'warning'],
    'info' => ['info', 'Information', 'blue', 'info'],
    'special' => ['special', 'Special Offer!', 'purple', 'gift'],
]);

test('multiple notifications share assets and preserve custom titles and icons', function () {
    $this->withSession([
        'success' => [
            ['title' => 'Added to Cart!', 'message' => 'Your handbag is in the cart.', 'icon' => 'cart'],
            'Your profile has been updated.',
        ],
        'info' => ['title' => 'Please Login', 'message' => 'Login to continue.', 'icon' => 'lock'],
    ]);

    $view = $this->view('partials.notify');

    $view->assertSeeTextInOrder(['Added to Cart!', 'Your handbag is in the cart.', 'Your profile has been updated.', 'Please Login', 'Login to continue.'])
        ->assertSee('href="#cart"', false)
        ->assertSee('href="#lock"', false);
    expect(substr_count((string) $view, 'data-notify-type='))->toBe(3);
    expect(substr_count((string) $view, 'id="check"'))->toBe(1);
    expect(substr_count((string) $view, 'assets/theme/js/notify.js'))->toBe(1);
    expect(substr_count((string) $view, 'assets/theme/css/notify.css'))->toBe(1);
    expect(substr_count((string) $view, 'data-notify-stack'))->toBe(1);
});

test('validation messages from default and named bags display as errors', function () {
    $errors = (new ViewErrorBag)
        ->put('default', new MessageBag(['identity' => 'Invalid login credentials.']))
        ->put('review', new MessageBag(['rating' => 'Choose a rating.']));

    $view = $this->view('partials.notify', compact('errors'));

    $view->assertSeeText('Invalid login credentials.')->assertSeeText('Choose a rating.');
    expect(substr_count((string) $view, 'data-notify-type="error"'))->toBe(2);
});

test('notification text is escaped and unknown icons cannot inject markup', function () {
    $title = '<script>alert("title")</script>';
    $message = '<img src=x onerror=alert(1)>';
    $icon = '"><script>alert("icon")</script>';

    $this->blade('<x-notify type="success" :title="$title" :message="$message" :icon="$icon" />', compact('title', 'message', 'icon'))
        ->assertSee($title)
        ->assertSee($message)
        ->assertDontSee($title, false)
        ->assertDontSee($message, false)
        ->assertDontSee($icon, false)
        ->assertSee('href="#check"', false);
});

test('the component works directly and falls back to info for an unknown type', function () {
    $this->blade('<x-notify type="unknown" title="Notice" message="Read this." id="custom-notice" />')
        ->assertSeeText('Notice')
        ->assertSeeText('Read this.')
        ->assertSee('data-notify-type="info"', false)
        ->assertSee('id="custom-notice"', false);
});

test('the illustrated cards keep their reusable svg artwork', function (string $icon, string $class) {
    $this->blade('<x-notify type="success" :icon="$icon" message="Completed." />', compact('icon'))
        ->assertSee('class="'.$class.'"', false)
        ->assertSee('href="#'.$icon.'"', false);
})->with([
    'order' => ['order', 'notify-bag-illu'],
    'gift' => ['gift', 'notify-gift-illu'],
    'product' => ['handbag', 'notify-handbag'],
]);

test('no notification cards are rendered without messages', function () {
    $this->view('partials.notify')->assertDontSee('data-notify-type=', false);
});
