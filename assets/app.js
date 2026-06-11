document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-copy]');

    if (!button) {
        return;
    }

    const target = document.querySelector(button.dataset.copy);

    if (!target) {
        return;
    }

    try {
        await navigator.clipboard.writeText(target.textContent.trim());
        const original = button.textContent;
        button.textContent = 'Copied';
        setTimeout(() => {
            button.textContent = original;
        }, 1500);
    } catch (error) {
        console.error(error);
    }
});
