/**
 * GridBuilder JavaScript
 * Provides AJAX loading, search, and pagination functionality for data grids
 */

class GridBuilder {
    constructor(options = {}) {
        this.options = {
            gridSelector: '#data-grid',
            searchInputSelector: '#grid-search',
            searchButtonSelector: '#search-btn',
            gridBodySelector: '#grid-body',
            paginationSelector: '.pagination',
            sortableSelector: '.sortable',
            loadingClass: 'loading',
            searchDelay: 500,
            ...options
        };

        this.ajaxUrl = options.ajaxUrl || null;
        this.currentPage = 1;
        this.currentSearch = '';
        this.currentSort = '';
        this.currentDirection = 'asc';
        this.searchTimeout = null;
        this.isLoading = false;

        this.init();
    }

    init() {
        this.bindEvents();
        this.updateSortIndicators();
    }

    bindEvents() {
        // Search functionality
        const searchInput = document.querySelector(this.options.searchInputSelector);
        const searchButton = document.querySelector(this.options.searchButtonSelector);

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.handleSearch(e.target.value);
            });

            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.performSearch(e.target.value);
                }
            });
        }

        if (searchButton) {
            searchButton.addEventListener('click', () => {
                const searchValue = searchInput ? searchInput.value : '';
                this.performSearch(searchValue);
            });
        }

        // Sorting functionality
        const sortableHeaders = document.querySelectorAll(this.options.sortableSelector);
        sortableHeaders.forEach(header => {
            header.addEventListener('click', (e) => {
                const sortBy = header.dataset.sort;
                this.handleSort(sortBy);
            });
        });

        // Pagination functionality (if using AJAX)
        if (this.ajaxUrl) {
            this.bindPaginationEvents();
        }
    }

    handleSearch(searchValue) {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.performSearch(searchValue);
        }, this.options.searchDelay);
    }

    performSearch(searchValue) {
        this.currentSearch = searchValue;
        this.currentPage = 1; // Reset to first page on search

        if (this.ajaxUrl) {
            this.loadData();
        } else {
            // Fallback to page reload for non-AJAX grids
            this.updateUrl();
        }
    }

    handleSort(sortBy) {
        let newDirection = 'asc';
        
        if (this.currentSort === sortBy && this.currentDirection === 'asc') {
            newDirection = 'desc';
        }

        this.currentSort = sortBy;
        this.currentDirection = newDirection;
        this.currentPage = 1; // Reset to first page on sort

        if (this.ajaxUrl) {
            this.loadData();
        } else {
            // Fallback to page reload for non-AJAX grids
            this.updateUrl();
        }
    }

    handlePagination(page) {
        this.currentPage = page;

        if (this.ajaxUrl) {
            this.loadData();
        } else {
            this.updateUrl();
        }
    }

    loadData() {
        if (this.isLoading || !this.ajaxUrl) return;

        this.isLoading = true;
        this.showLoading();

        const params = new URLSearchParams({
            page: this.currentPage,
            search: this.currentSearch,
            sort_by: this.currentSort,
            sort_direction: this.currentDirection
        });

        fetch(`${this.ajaxUrl}?${params}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                this.updateGridContent(data);
                this.updatePagination(data.pagination);
                this.updateSortIndicators();
            } else {
                this.showError(data.message || 'Error loading data');
            }
        })
        .catch(error => {
            console.error('Grid loading error:', error);
            this.showError('Failed to load data. Please try again.');
        })
        .finally(() => {
            this.isLoading = false;
            this.hideLoading();
        });
    }

    updateGridContent(data) {
        const gridBody = document.querySelector(this.options.gridBodySelector);
        if (!gridBody) return;

        if (data.data && data.data.length > 0) {
            let html = '';
            
            data.data.forEach(item => {
                html += '<tr>';
                
                // Render columns
                data.columns.forEach(column => {
                    html += '<td>';
                    if (column.key.includes('.')) {
                        // Handle nested properties
                        const keys = column.key.split('.');
                        let value = item;
                        keys.forEach(key => {
                            value = value && value[key] ? value[key] : '';
                        });
                        html += this.escapeHtml(value);
                    } else {
                        html += this.escapeHtml(item[column.key] || '');
                    }
                    html += '</td>';
                });

                // Render actions
                if (data.actions && data.actions.length > 0) {
                    html += '<td class="text-center">';
                    html += '<div class="btn-group btn-group-sm" role="group">';
                    
                    data.actions.forEach(action => {
                        const url = action.route.replace('{id}', item.id);
                        const onclick = action.class.includes('danger') ? 
                            'onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\')"' : '';
                        
                        html += `<a href="${url}" class="btn ${action.class}" ${onclick}>`;
                        if (action.icon) {
                            html += `<i class="bi bi-${action.icon}"></i> `;
                        }
                        html += `${action.label}</a>`;
                    });
                    
                    html += '</div>';
                    html += '</td>';
                }
                
                html += '</tr>';
            });
            
            gridBody.innerHTML = html;
        } else {
            // Show empty state
            const columnCount = data.columns.length + (data.actions && data.actions.length > 0 ? 1 : 0);
            gridBody.innerHTML = `
                <tr>
                    <td colspan="${columnCount}" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                        Tidak ada data yang ditemukan.
                    </td>
                </tr>
            `;
        }
    }

    updatePagination(pagination) {
        const paginationContainer = document.querySelector(this.options.paginationSelector);
        if (!paginationContainer || !pagination) return;

        let html = '';
        
        // Previous button
        if (pagination.current_page > 1) {
            html += `<li class="page-item">
                        <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
                     </li>`;
        } else {
            html += `<li class="page-item disabled">
                        <span class="page-link">Previous</span>
                     </li>`;
        }

        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            if (i === pagination.current_page) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            }
        }

        if (endPage < pagination.last_page) {
            if (endPage < pagination.last_page - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.last_page}">${pagination.last_page}</a></li>`;
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            html += `<li class="page-item">
                        <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
                     </li>`;
        } else {
            html += `<li class="page-item disabled">
                        <span class="page-link">Next</span>
                     </li>`;
        }

        paginationContainer.innerHTML = html;
        this.bindPaginationEvents();

        // Update info text
        const infoElement = document.querySelector('.pagination-info');
        if (infoElement) {
            infoElement.textContent = `Menampilkan ${pagination.from} sampai ${pagination.to} dari ${pagination.total} data`;
        }
    }

    bindPaginationEvents() {
        const paginationLinks = document.querySelectorAll('.pagination .page-link[data-page]');
        paginationLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(e.target.dataset.page);
                if (page && page !== this.currentPage) {
                    this.handlePagination(page);
                }
            });
        });
    }

    updateSortIndicators() {
        // Reset all sort indicators
        const sortableHeaders = document.querySelectorAll(this.options.sortableSelector);
        sortableHeaders.forEach(header => {
            const icon = header.querySelector('i');
            if (icon) {
                icon.className = 'bi bi-arrow-down-up ms-1 text-muted';
            }
        });

        // Update active sort indicator
        if (this.currentSort) {
            const activeHeader = document.querySelector(`[data-sort="${this.currentSort}"]`);
            if (activeHeader) {
                const icon = activeHeader.querySelector('i');
                if (icon) {
                    icon.className = this.currentDirection === 'asc' ? 
                        'bi bi-arrow-up ms-1' : 'bi bi-arrow-down ms-1';
                }
            }
        }
    }

    showLoading() {
        const grid = document.querySelector(this.options.gridSelector);
        if (grid) {
            grid.classList.add(this.options.loadingClass);
        }

        const gridBody = document.querySelector(this.options.gridBodySelector);
        if (gridBody) {
            gridBody.style.opacity = '0.5';
        }
    }

    hideLoading() {
        const grid = document.querySelector(this.options.gridSelector);
        if (grid) {
            grid.classList.remove(this.options.loadingClass);
        }

        const gridBody = document.querySelector(this.options.gridBodySelector);
        if (gridBody) {
            gridBody.style.opacity = '1';
        }
    }

    showError(message) {
        // Create or update error message
        let errorDiv = document.querySelector('.grid-error-message');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger grid-error-message';
            errorDiv.innerHTML = `
                <i class="bi bi-exclamation-triangle"></i>
                <span class="error-text">${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const grid = document.querySelector(this.options.gridSelector);
            if (grid) {
                grid.parentNode.insertBefore(errorDiv, grid);
            }
        } else {
            errorDiv.querySelector('.error-text').textContent = message;
        }
    }

    updateUrl() {
        const url = new URL(window.location);
        
        if (this.currentSearch) {
            url.searchParams.set('search', this.currentSearch);
        } else {
            url.searchParams.delete('search');
        }

        if (this.currentSort) {
            url.searchParams.set('sort_by', this.currentSort);
            url.searchParams.set('sort_direction', this.currentDirection);
        } else {
            url.searchParams.delete('sort_by');
            url.searchParams.delete('sort_direction');
        }

        if (this.currentPage > 1) {
            url.searchParams.set('page', this.currentPage);
        } else {
            url.searchParams.delete('page');
        }

        window.location.href = url.toString();
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Public methods for external control
    refresh() {
        if (this.ajaxUrl) {
            this.loadData();
        } else {
            window.location.reload();
        }
    }

    setSearch(searchValue) {
        this.currentSearch = searchValue;
        const searchInput = document.querySelector(this.options.searchInputSelector);
        if (searchInput) {
            searchInput.value = searchValue;
        }
        this.performSearch(searchValue);
    }

    setSort(sortBy, direction = 'asc') {
        this.currentSort = sortBy;
        this.currentDirection = direction;
        if (this.ajaxUrl) {
            this.loadData();
        } else {
            this.updateUrl();
        }
    }
}

// Auto-initialize GridBuilder for grids with data-ajax-url attribute
document.addEventListener('DOMContentLoaded', function() {
    const grids = document.querySelectorAll('[data-ajax-url]');
    grids.forEach(grid => {
        const ajaxUrl = grid.dataset.ajaxUrl;
        new GridBuilder({ ajaxUrl });
    });
});

// Export for manual initialization
window.GridBuilder = GridBuilder;