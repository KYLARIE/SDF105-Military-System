    // export.js
document.addEventListener('DOMContentLoaded', function() {
    // View toggle functionality
    const viewToggleBtns = document.querySelectorAll('.view-toggle-btn');
    viewToggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            const url = new URL(window.location.href);
            url.searchParams.set('view', view);
            window.location.href = url.toString();
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdowns = document.querySelectorAll('.export-dropdown-content');
        dropdowns.forEach(dropdown => {
            if (!event.target.closest('.export-dropdown')) {
                dropdown.style.display = 'none';
            }
        });
    });
});