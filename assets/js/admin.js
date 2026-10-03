// AutoDeal — admin helpers

function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this? This cannot be undone.');
}

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('liveSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('table tbody tr[data-search]').forEach(function (row) {
                const text = row.getAttribute('data-search').toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }

    // Live preview of newly selected images before upload
    const fileInput = document.getElementById('carImages');
    const previewGrid = document.getElementById('newImagePreview');
    if (fileInput && previewGrid) {
        fileInput.addEventListener('change', function () {
            previewGrid.innerHTML = '';
            Array.from(fileInput.files).forEach(function (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const div = document.createElement('div');
                    div.className = 'thumb';
                    div.innerHTML = `<img src="${e.target.result}" alt="">`;
                    previewGrid.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    }
});
