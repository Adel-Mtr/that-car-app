const ready = (callback) => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback, { once: true });
    } else {
        callback();
    }
};

ready(() => {
    const sidebar = document.querySelector('[data-sidebar]');
    const sidebarBackdrop = document.querySelector('[data-sidebar-backdrop]');

    const setSidebarOpen = (open) => {
        sidebar?.classList.toggle('-translate-x-full', !open);
        sidebarBackdrop?.classList.toggle('hidden', !open);
        document.body.classList.toggle('overflow-hidden', open);
    };

    document.querySelectorAll('[data-sidebar-open]').forEach((button) => {
        button.addEventListener('click', () => setSidebarOpen(true));
    });
    document.querySelectorAll('[data-sidebar-close], [data-sidebar-backdrop]').forEach((button) => {
        button.addEventListener('click', () => setSidebarOpen(false));
    });

    document.querySelectorAll('[data-dialog-open]').forEach((button) => {
        button.addEventListener('click', () => {
            const dialog = document.getElementById(button.dataset.dialogOpen);
            dialog?.showModal();
        });
    });
    document.querySelectorAll('[data-dialog-close]').forEach((button) => {
        button.addEventListener('click', () => button.closest('dialog')?.close());
    });
    document.querySelectorAll('dialog').forEach((dialog) => {
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
            }
        });
    });

    const tabs = document.querySelectorAll('[data-tab]');
    const panels = document.querySelectorAll('[data-tab-panel]');
    const activateTab = (name) => {
        tabs.forEach((tab) => {
            const active = tab.dataset.tab === name;
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
            tab.classList.toggle('bg-ink-950', active);
            tab.classList.toggle('text-white', active);
            tab.classList.toggle('text-ink-700', !active);
        });
        panels.forEach((panel) => panel.classList.toggle('hidden', panel.dataset.tabPanel !== name));
    };
    tabs.forEach((tab) => tab.addEventListener('click', () => activateTab(tab.dataset.tab)));

    document.querySelectorAll('[data-dismiss]').forEach((button) => {
        button.addEventListener('click', () => button.closest('[data-dismissible]')?.remove());
    });

    document.querySelectorAll('[data-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            await navigator.clipboard.writeText(button.dataset.copy);
            const original = button.textContent;
            button.textContent = 'Copied';
            window.setTimeout(() => {
                button.textContent = original;
            }, 1600);
        });
    });

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js').catch(() => undefined);
    }
});
