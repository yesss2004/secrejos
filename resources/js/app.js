import './bootstrap';

const ready = (callback) => {
    if (document.readyState !== 'loading') {
        callback();
    } else {
        document.addEventListener('DOMContentLoaded', callback);
    }
};

ready(() => {
    /* ------------------------------------------------------------------
     * Sidebar (mobile)
     * ------------------------------------------------------------------ */
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    const setSidebar = (open) => {
        if (!sidebar) return;
        sidebar.classList.toggle('-translate-x-full', !open);
        overlay?.classList.toggle('hidden', !open);
        document.body.classList.toggle('overflow-hidden', open);
    };

    document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            setSidebar(sidebar?.classList.contains('-translate-x-full'));
        });
    });

    overlay?.addEventListener('click', () => setSidebar(false));

    document.querySelectorAll('#sidebar a').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 1024) setSidebar(false);
        });
    });

    /* ------------------------------------------------------------------
     * Dropdowns
     * ------------------------------------------------------------------ */
    const closeDropdowns = (except = null) => {
        document.querySelectorAll('[data-dropdown]').forEach((dropdown) => {
            if (dropdown !== except) dropdown.classList.add('hidden');
        });
    };

    document.querySelectorAll('[data-dropdown-toggle]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            const target = document.getElementById(button.dataset.dropdownToggle);
            if (!target) return;
            const willOpen = target.classList.contains('hidden');
            closeDropdowns(target);
            target.classList.toggle('hidden', !willOpen);
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('[data-dropdown]')) closeDropdowns();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeDropdowns();
            setSidebar(false);
        }
    });

    /* ------------------------------------------------------------------
     * Confirm destructive actions
     * ------------------------------------------------------------------ */
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    /* ------------------------------------------------------------------
     * Auto dismiss toasts
     * ------------------------------------------------------------------ */
    document.querySelectorAll('[data-toast]').forEach((toast) => {
        window.setTimeout(() => {
            toast.style.transition = 'opacity .35s ease, transform .35s ease';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(20px)';
            window.setTimeout(() => toast.remove(), 400);
        }, 6000);
    });

    /* ------------------------------------------------------------------
     * Report type -> month field
     * ------------------------------------------------------------------ */
    const typeInputs = document.querySelectorAll('[data-report-type]');
    const monthField = document.querySelector('[data-month-field]');

    if (typeInputs.length && monthField) {
        const monthSelect = monthField.querySelector('select');
        const syncMonth = () => {
            const selected = Array.from(typeInputs).find((input) => input.checked)?.value;
            const isMonthly = selected === 'bulanan';
            monthField.classList.toggle('hidden', !isMonthly);
            if (monthSelect) {
                monthSelect.required = isMonthly;
                monthSelect.disabled = !isMonthly;
            }
        };
        typeInputs.forEach((input) => input.addEventListener('change', syncMonth));
        syncMonth();
    }

    /* ------------------------------------------------------------------
     * File upload preview
     * ------------------------------------------------------------------ */
    document.querySelectorAll('[data-upload]').forEach((wrapper) => {
        const input = wrapper.querySelector('input[type="file"]');
        const zone = wrapper.querySelector('[data-dropzone]');
        const list = wrapper.querySelector('[data-file-list]');
        const maxFiles = Number(input?.dataset.maxFiles || 5);
        const maxSize = Number(input?.dataset.maxSize || 10 * 1024 * 1024);
        let files = [];

        if (!input || !zone || !list) return;

        const renderFiles = () => {
            list.innerHTML = '';
            files.forEach((file, index) => {
                const row = document.createElement('div');
                row.className = 'file-row';

                const icon = document.createElement('span');
                icon.className = 'icon-tile tone-emerald h-9 w-9 rounded-lg text-[10px] font-extrabold';
                icon.textContent = (file.name.split('.').pop() || 'FILE').slice(0, 4).toUpperCase();

                const meta = document.createElement('div');
                meta.className = 'min-w-0 flex-1';
                const name = document.createElement('p');
                name.className = 'truncate text-xs font-semibold text-slate-700';
                name.textContent = file.name;
                const size = document.createElement('p');
                size.className = 'text-[11px] text-slate-400';
                size.textContent = `${(file.size / 1024).toFixed(0)} KB`;
                meta.append(name, size);

                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'rounded-lg p-1.5 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600';
                remove.innerHTML = '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>';
                remove.addEventListener('click', () => {
                    files.splice(index, 1);
                    syncInput();
                    renderFiles();
                });

                row.append(icon, meta, remove);
                list.append(row);
            });
        };

        const syncInput = () => {
            const dataTransfer = new DataTransfer();
            files.forEach((file) => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
            zone.classList.toggle('hidden', files.length >= maxFiles);
        };

        const addFiles = (incoming) => {
            files = Array.from(incoming)
                .slice(0, maxFiles)
                .filter((file) => file.size <= maxSize);
            syncInput();
            renderFiles();
        };

        input.addEventListener('change', () => addFiles(input.files));

        ['dragenter', 'dragover'].forEach((eventName) => {
            zone.addEventListener(eventName, (event) => {
                event.preventDefault();
                zone.classList.add('is-dragging');
            });
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            zone.addEventListener(eventName, (event) => {
                event.preventDefault();
                zone.classList.remove('is-dragging');
            });
        });

        zone.addEventListener('drop', (event) => {
            if (event.dataTransfer?.files?.length) addFiles(event.dataTransfer.files);
        });
    });

    /* ------------------------------------------------------------------
     * Single file label
     * ------------------------------------------------------------------ */
    document.querySelectorAll('input[type="file"][data-file-label]').forEach((input) => {
        input.addEventListener('change', () => {
            const label = document.querySelector(`[data-file-name="${input.id}"]`);
            if (label) label.textContent = input.files?.[0]?.name || 'Belum ada berkas dipilih';
        });
    });

    /* ------------------------------------------------------------------
     * Password visibility
     * ------------------------------------------------------------------ */
    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.togglePassword);
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
        });
    });

    /* ------------------------------------------------------------------
     * Copy to clipboard
     * ------------------------------------------------------------------ */
    document.querySelectorAll('[data-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            const value = button.dataset.copy;
            try {
                await navigator.clipboard.writeText(value);
                const original = button.innerHTML;
                button.innerHTML = '<span class="text-emerald-600">Tersalin!</span>';
                window.setTimeout(() => (button.innerHTML = original), 1500);
            } catch (error) {
                // Clipboard API can be unavailable on insecure origins.
            }
        });
    });

    /* ------------------------------------------------------------------
     * Keyboard shortcut: "/" focuses global search
     * ------------------------------------------------------------------ */
    const globalSearch = document.getElementById('global-search');
    document.addEventListener('keydown', (event) => {
        if (event.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
            event.preventDefault();
            globalSearch?.focus();
        }
    });

    /* ------------------------------------------------------------------
     * Character counter
     * ------------------------------------------------------------------ */
    const description = document.getElementById('description');
    const charCount = document.querySelector('[data-char-count]');
    if (description && charCount) {
        const updateCount = () => {
            charCount.textContent = `${description.value.length} karakter`;
        };
        description.addEventListener('input', updateCount);
        updateCount();
    }

    /* ------------------------------------------------------------------
     * Print
     * ------------------------------------------------------------------ */
    document.querySelectorAll('[data-print]').forEach((button) => {
        button.addEventListener('click', () => window.print());
    });
});
