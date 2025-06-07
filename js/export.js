/**
 * Table Export Functionality
 * Allows exporting HTML tables to CSV and PDF formats
 */

// Function to export table data to CSV
function exportTableToCSV(tableId, filename = 'export') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            // Skip the last column (actions)
            if (j === cols.length - 1 && i === 0) continue;
            if (j === cols.length - 1 && cols[j].querySelector('.action-btn')) continue;
            
            // Get the text content and clean it
            let text = cols[j].innerText;
            text = text.replace(/"/g, '""'); // escape double quotes
            row.push('"' + text + '"');
        }
        
        csv.push(row.join(','));
    }
    
    // Download CSV file
    downloadCSV(csv.join('\n'), filename);
}

function downloadCSV(csv, filename) {
    const csvFile = new Blob([csv], {type: "text/csv"});
    const downloadLink = document.createElement("a");
    
    // File name
    downloadLink.download = filename + '.csv';
    
    // Create a link to the file
    downloadLink.href = window.URL.createObjectURL(csvFile);
    
    // Hide download link
    downloadLink.style.display = "none";
    
    // Add the link to DOM
    document.body.appendChild(downloadLink);
    
    // Click download link
    downloadLink.click();
    
    // Clean up
    document.body.removeChild(downloadLink);
}

// Function to export table data to PDF
function exportTableToPDF(tableId, filename = 'export') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    // Create a new window for the PDF
    const printWindow = window.open('', '_blank');
    
    // Create HTML content for the PDF
    let html = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>${filename}</title>
            <style>
                body { font-family: Arial, sans-serif; }
                table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                h1 { text-align: center; }
                .no-print { display: none; }
            </style>
        </head>
        <body>
            <h1>${filename}</h1>
    `;
    
    // Clone the table and remove action buttons
    const tableClone = table.cloneNode(true);
    const actionCells = tableClone.querySelectorAll('td:last-child');
    const actionHeader = tableClone.querySelector('th:last-child');
    
    if (actionHeader) {
        actionHeader.classList.add('no-print');
    }
    
    actionCells.forEach(cell => {
        cell.classList.add('no-print');
    });
    
    html += tableClone.outerHTML;
    html += `
        </body>
        </html>
    `;
    
    // Write to the new window and print
    printWindow.document.write(html);
    printWindow.document.close();
    
    // Wait for content to load before printing
    printWindow.onload = function() {
        printWindow.print();
    };
}

// Create export dropdown menu
function createExportDropdown(tableId, title) {
    const dropdown = document.createElement('div');
    dropdown.className = 'export-dropdown';
    
    const button = document.createElement('button');
    button.className = 'export-btn';
    button.innerHTML = '<i class="fas fa-download"></i> Export';
    
    const content = document.createElement('div');
    content.className = 'export-dropdown-content';
    
    const csvLink = document.createElement('a');
    csvLink.href = 'javascript:void(0)';
    csvLink.textContent = 'Export as CSV';
    csvLink.onclick = function() {
        exportTableToCSV(tableId, title);
    };
    
    const pdfLink = document.createElement('a');
    pdfLink.href = 'javascript:void(0)';
    pdfLink.textContent = 'Export as PDF';
    pdfLink.onclick = function() {
        exportTableToPDF(tableId, title);
    };
    
    content.appendChild(csvLink);
    content.appendChild(pdfLink);
    
    dropdown.appendChild(button);
    dropdown.appendChild(content);
    
    return dropdown;
}

// Initialize export buttons when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add export button to ranks table if it exists
    const ranksTable = document.getElementById('ranksTable');
    if (ranksTable) {
        const sectionActions = ranksTable.closest('.content-section').querySelector('.section-actions');
        if (sectionActions) {
            const exportDropdown = createExportDropdown('ranksTable', 'Ranks List');
            sectionActions.appendChild(exportDropdown);
        }
    }
    
    // Add export button to units table if it exists
    const unitsTable = document.getElementById('unitsTable');
    if (unitsTable) {
        const sectionActions = unitsTable.closest('.content-section').querySelector('.section-actions');
        if (sectionActions) {
            const exportDropdown = createExportDropdown('unitsTable', 'Units List');
            sectionActions.appendChild(exportDropdown);
        }
    }
}); 