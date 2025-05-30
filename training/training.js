document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.export-dropdown');
    
    dropdowns.forEach(dropdown => {
        const btn = dropdown.querySelector('.export-btn');
        const content = dropdown.querySelector('.export-dropdown-content');
        
        btn.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const isShowing = content.style.display === 'block';
                document.querySelectorAll('.export-dropdown-content').forEach(d => {
                    d.style.display = 'none';
                });
                content.style.display = isShowing ? 'none' : 'block';
            }
        });
        
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target)) {
                content.style.display = 'none';
            }
        });
    });
});