@if(config('demo.enabled'))
    <div role="note" class="border-b border-moss-600/20 bg-moss-100 px-5 py-3 text-sm text-moss-700">
        <strong>Portfolio demo</strong> — Explore the sample garage. Records are fictional and read-only; uploads and account changes are disabled.
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const allowed = [@json(route('login.store')), @json(route('logout'))];
            document.querySelectorAll('form').forEach(form => {
                if (form.method.toLowerCase() !== 'get' && !allowed.includes(form.action)) {
                    form.querySelectorAll('input, select, textarea, button').forEach(control => {
                        control.disabled = true;
                        control.title = 'Read-only portfolio demo';
                    });
                }
            });
        });
    </script>
@endif
