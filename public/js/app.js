document.querySelectorAll('.delete-form').forEach((form) => {
    form.addEventListener('submit', (event) => {
        const titulo = form.dataset.title || 'esta película';
        if (!window.confirm(`¿Eliminar "${titulo}"? Esta acción no se puede deshacer.`)) {
            event.preventDefault();
        }
    });
});
