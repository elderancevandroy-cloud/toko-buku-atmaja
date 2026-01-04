@props([
    'data' => null,
    'columns' => [],
    'actions' => [],
    'searchable' => false,
    'title' => null,
    'createRoute' => null,
    'createLabel' => 'Tambah Data',
    'emptyMessage' => 'Tidak ada data yang ditemukan.',
    'ajaxUrl' => null
])

<div class="card">
    @if($title || $createRoute || $searchable)
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                @if($title)
                    <h5 class="card-title mb-0">{{ $title }}</h5>
                @endif
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    @if($searchable)
                        <div class="input-group" style="width: 250px;">
                            <input type="text" 
                                   class="form-control" 
                                   id="grid-search" 
                                   placeholder="Cari data..."
                                   value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="button" id="search-btn">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    @endif
                    
                    @if($createRoute)
                        <a href="{{ $createRoute }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> {{ $createLabel }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="data-grid">
                <thead class="table-light">
                    <tr>
                        @foreach($columns as $column)
                            <th scope="col" 
                                @if($column['sortable']) 
                                    class="sortable" 
                                    data-sort="{{ $column['key'] }}"
                                    style="cursor: pointer;"
                                @endif>
                                {{ $column['label'] }}
                                @if($column['sortable'])
                                    <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                                @endif
                            </th>
                        @endforeach
                        
                        @if(count($actions) > 0)
                            <th scope="col" class="text-center" style="width: 150px;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="grid-body">
                    @if($data && $data->count() > 0)
                        @foreach($data as $item)
                            <tr>
                                @foreach($columns as $column)
                                    <td>
                                        @if(str_contains($column['key'], '.'))
                                            @php
                                                $keys = explode('.', $column['key']);
                                                $value = $item;
                                                foreach($keys as $key) {
                                                    $value = $value->{$key} ?? '';
                                                }
                                            @endphp
                                            {{ $value }}
                                        @else
                                            {{ $item->{$column['key']} ?? '' }}
                                        @endif
                                    </td>
                                @endforeach
                                
                                @if(count($actions) > 0)
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            @foreach($actions as $action)
                                                @php
                                                    // Check if route is a route name or URL pattern
                                                    if (str_contains($action['route'], '{id}')) {
                                                        $url = str_replace('{id}', $item->id, $action['route']);
                                                    } else {
                                                        // Assume it's a route name
                                                        $url = route($action['route'], $item->id);
                                                    }
                                                @endphp
                                                <a href="{{ $url }}" 
                                                   class="btn {{ $action['class'] }}"
                                                   @if(str_contains($action['class'], 'danger'))
                                                       onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                                   @endif>
                                                    @if($action['icon'])
                                                        <i class="bi bi-{{ $action['icon'] }}"></i>
                                                    @endif
                                                    {{ $action['label'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ count($columns) + (count($actions) > 0 ? 1 : 0) }}" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                {{ $emptyMessage }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @if($data && $data->hasPages())
    <div class="card-footer">
        <div class="row align-items-center">
            <div class="col">
                <small class="text-muted">
                    Menampilkan {{ $data->firstItem() }} sampai {{ $data->lastItem() }} 
                    dari {{ $data->total() }} data
                </small>
            </div>
            <div class="col-auto">
                {{ $data->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

@if($searchable || $ajaxUrl)
@push('scripts')
@vite(['resources/js/grid-builder.js'])
@if($ajaxUrl)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AJAX-enabled GridBuilder
    new GridBuilder({
        ajaxUrl: '{{ $ajaxUrl }}'
    });
});
</script>
@else
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('grid-search');
    const searchBtn = document.getElementById('search-btn');
    const sortableHeaders = document.querySelectorAll('.sortable');
    
    // Search functionality
    if (searchInput && searchBtn) {
        let searchTimeout;
        
        function performSearch() {
            const searchValue = searchInput.value;
            const url = new URL(window.location);
            
            if (searchValue) {
                url.searchParams.set('search', searchValue);
            } else {
                url.searchParams.delete('search');
            }
            
            window.location.href = url.toString();
        }
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });
        
        searchBtn.addEventListener('click', performSearch);
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });
    }
    
    // Sorting functionality
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortBy = this.dataset.sort;
            const url = new URL(window.location);
            const currentSort = url.searchParams.get('sort_by');
            const currentDirection = url.searchParams.get('sort_direction') || 'asc';
            
            let newDirection = 'asc';
            if (currentSort === sortBy && currentDirection === 'asc') {
                newDirection = 'desc';
            }
            
            url.searchParams.set('sort_by', sortBy);
            url.searchParams.set('sort_direction', newDirection);
            
            window.location.href = url.toString();
        });
    });
    
    // Update sort indicators
    const currentSort = new URLSearchParams(window.location.search).get('sort_by');
    const currentDirection = new URLSearchParams(window.location.search).get('sort_direction') || 'asc';
    
    if (currentSort) {
        const activeHeader = document.querySelector(`[data-sort="${currentSort}"]`);
        if (activeHeader) {
            const icon = activeHeader.querySelector('i');
            if (icon) {
                icon.className = currentDirection === 'asc' ? 'bi bi-arrow-up ms-1' : 'bi bi-arrow-down ms-1';
            }
        }
    }
});
</script>
@endif
@endpush
@endif