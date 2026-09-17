<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ gs()->siteName(__($pageTitle)) }}</title>

    {{-- Google Fonts - আগের design-এর font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap CSS FIRST --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/theme/css/color.css') }}">

    @stack('style')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @yield('panel')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#mainSearch, #topSearch').forEach(function (searchArea) {
        const input = searchArea.querySelector('.search-input');
        const dropdown = searchArea.querySelector('.search-dropdown');
        const suggestionsUrl = searchArea.dataset.suggestionsUrl;

        if (!input || !dropdown || !suggestionsUrl) return;

        let searchTimer = null;
        let requestController = null;
        let requestSequence = 0;

        function clearDropdown() {
            dropdown.replaceChildren();
            dropdown.classList.remove('show');
        }

        function createMessage(message, iconClass = 'fa-solid fa-magnifying-glass') {
            const item = document.createElement('div');
            item.className = 'search-item';

            const icon = document.createElement('i');
            icon.className = iconClass;

            const text = document.createElement('span');
            text.textContent = message;

            item.append(icon, text);
            return item;
        }

        function showLoading() {
            dropdown.replaceChildren(createMessage('পণ্য খোঁজা হচ্ছে...', 'fa-solid fa-spinner fa-spin'));
            dropdown.classList.add('show');
        }

        function renderProducts(products) {
            dropdown.replaceChildren();

            if (!Array.isArray(products) || !products.length) {
                dropdown.append(createMessage('কোনো পণ্য পাওয়া যায়নি', 'fa-solid fa-circle-info'));
                dropdown.classList.add('show');
                return;
            }

            products.forEach(function (product) {
                const item = document.createElement('a');
                item.className = 'search-item';
                item.href = product.url;
                item.setAttribute('role', 'option');

                const icon = document.createElement('i');
                icon.className = 'fa-solid fa-magnifying-glass';

                const content = document.createElement('span');
                content.textContent = product.name;

                item.append(icon, content);

                if (product.sku) {
                    const sku = document.createElement('small');
                    sku.textContent = ' (' + product.sku + ')';
                    item.append(sku);
                }

                dropdown.append(item);
            });

            dropdown.classList.add('show');
        }

        async function loadSuggestions() {
            const search = input.value.trim();

            if (search.length < 2) {
                requestController?.abort();
                clearDropdown();
                return;
            }

            requestController?.abort();
            requestController = new AbortController();

            const currentRequest = ++requestSequence;
            const url = new URL(suggestionsUrl, window.location.origin);
            url.searchParams.set('search', search);

            showLoading();

            try {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    signal: requestController.signal
                });

                if (!response.ok) throw new Error('Search request failed');

                const data = await response.json();

                if (currentRequest !== requestSequence) return;

                renderProducts(data.products ?? []);
            } catch (error) {
                if (error.name === 'AbortError') return;
                if (currentRequest !== requestSequence) return;
                clearDropdown();
            }
        }

        input.addEventListener('input', function () {
            clearTimeout(searchTimer);

            if (input.value.trim().length < 2) {
                requestController?.abort();
                clearDropdown();
                return;
            }

            searchTimer = setTimeout(loadSuggestions, 300);
        });

        input.addEventListener('focus', function () {
            if (input.value.trim().length >= 2 && !dropdown.children.length) {
                loadSuggestions();
            }
        });

        searchArea.addEventListener('submit', function (event) {
            if (!input.value.trim()) {
                event.preventDefault();
                clearDropdown();
                input.focus();
            }
        });
    });

    document.addEventListener('click', function (event) {
        document.querySelectorAll('#mainSearch, #topSearch').forEach(function (searchArea) {
            if (!searchArea.contains(event.target)) {
                searchArea.querySelector('.search-dropdown')?.classList.remove('show');
            }
        });
    });
});
</script>
    @include('partials.notify')
    @stack('script')
</body>
</html>