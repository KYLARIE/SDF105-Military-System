document.addEventListener('DOMContentLoaded', function() {
    // View toggle functionality
    document.querySelectorAll('.view-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            const url = new URL(window.location.href);
            
            const form = document.querySelector('.filter-form');
            const formData = new FormData(form);
            
            const params = new URLSearchParams();
            
            for (const [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }
            
            params.set('view', view);
            window.location.href = 'index.php?' + params.toString();
        });
    });

    // Delete confirmation (unchanged)
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this record?')) {
                e.preventDefault();
            }
        });
    });

    // New Export Functionality
    function exportTable(format) {
        // Get the filter form
        const form = document.querySelector('.filter-form');
        const formData = new FormData(form);
        
        // Create a hidden form for submission
        const exportForm = document.createElement('form');
        exportForm.method = 'POST';
        exportForm.action = 'export.php';
        exportForm.style.display = 'none';
        
        // Add all filter values
        for (const [key, value] of formData.entries()) {
            if (value) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                exportForm.appendChild(input);
            }
        }
        
        // Add export format
        const formatInput = document.createElement('input');
        formatInput.type = 'hidden';
        formatInput.name = 'export_format';
        formatInput.value = format;
        exportForm.appendChild(formatInput);
        
        // Submit the form
        document.body.appendChild(exportForm);
        exportForm.submit();
        document.body.removeChild(exportForm);
    }

});