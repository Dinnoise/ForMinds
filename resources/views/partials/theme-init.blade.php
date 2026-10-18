{{-- Подключается в <head> до стилей: тема ставится до отрисовки, чтобы страница не мигала другой темой --}}
<script>
    try {
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.dataset.theme = 'dark';
        }
    } catch (e) {}

    function toggleTheme() {
        var root = document.documentElement;
        var next = root.dataset.theme === 'dark' ? 'light' : 'dark';

        if (next === 'dark') {
            root.dataset.theme = 'dark';
        } else {
            delete root.dataset.theme;
        }

        try { localStorage.setItem('theme', next); } catch (e) {}
    }
</script>
