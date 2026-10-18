import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Реакции-стикеры на комментарии: отправляем в фоне и меняем только блок реакций,
// без перезагрузки страницы. Если что-то пошло не так — обычная отправка формы.
document.addEventListener('submit', async (event) => {
    const form = event.target.closest('.reactions__form');
    if (!form || !event.submitter) return;

    event.preventDefault();
    if (form.dataset.pending) return;
    form.dataset.pending = '1';

    const type = event.submitter.value;
    const data = new FormData(form);
    data.set('type', type);

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: data,
        });

        if (!response.ok) throw new Error(response.status);

        const { html } = await response.json();
        const block = form.closest('.reactions');
        const template = document.createElement('template');
        template.innerHTML = html.trim();
        const fresh = template.content.firstElementChild;

        block.replaceWith(fresh);

        // лёгкая анимация на нажатом стикере
        fresh.querySelector(`.reaction-chip[value="${type}"]`)?.classList.add('is-bumped');
    } catch (e) {
        delete form.dataset.pending;
        // запасной вариант: обычная отправка с перезагрузкой
        const fallback = document.createElement('input');
        fallback.type = 'hidden';
        fallback.name = 'type';
        fallback.value = type;
        form.appendChild(fallback);
        form.submit();
    }
});

// Лайк поста (сердечко): так же в фоне, без перезагрузки.
// Гость получит 401 — тогда обычная отправка, и Laravel отправит его на страницу входа.
document.addEventListener('submit', async (event) => {
    const form = event.target.closest('.like-form');
    if (!form) return;

    event.preventDefault();
    if (form.dataset.pending) return;
    form.dataset.pending = '1';

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form),
        });

        if (!response.ok) throw new Error(response.status);

        const { liked, count } = await response.json();
        const button = form.querySelector('.like-btn');

        button.classList.toggle('is-liked', liked);
        button.setAttribute('aria-pressed', liked ? 'true' : 'false');
        button.title = liked ? 'Убрать лайк' : 'Нравится';
        button.querySelector('.like-btn__count').textContent = count;

        // перезапуск анимации сердечка
        button.classList.remove('is-bumped');
        void button.offsetWidth;
        button.classList.add('is-bumped');
    } catch (e) {
        form.submit();
        return;
    }

    delete form.dataset.pending;
});
