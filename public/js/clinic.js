(() => {
    // Protect edits in either examination form when the other form is saved.
    const forms = [...document.querySelectorAll('form[method="POST"], form[method="post"]')];
    const dirty = new Set();
    forms.forEach(form => {
        if (form.querySelector('input[type="password"]') || form.querySelector('input[value="DELETE"]') || form.action.endsWith('/logout')) return;
        form.addEventListener('input', () => dirty.add(form));
        form.addEventListener('change', () => dirty.add(form));
        form.addEventListener('submit', event => {
            if ([...dirty].some(other => other !== form) && !confirm('Ada perubahan pada bagian lain yang belum disimpan. Lanjutkan dan tinggalkan perubahan tersebut?')) {
                event.preventDefault(); return;
            }
            dirty.clear();
        });
    });
    window.addEventListener('beforeunload', event => {
        if (dirty.size) { event.preventDefault(); event.returnValue = ''; }
    });
    const guarantor = document.querySelector('#penjamin');
    const number = document.querySelector('#no_penjamin');
    if (guarantor && number && location.pathname === '/') {
        const sync = () => { number.required = ['BPJS', 'Asuransi Swasta'].includes(guarantor.value); };
        guarantor.addEventListener('change', sync); sync();
    }
})();
