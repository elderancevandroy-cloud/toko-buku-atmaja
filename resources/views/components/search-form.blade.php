@props(['searchFields' => [], 'filters' => [], 'action' => ''])

<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-search"></i> Pencarian & Filter
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ $action }}" id="searchForm">
            <div class="row">
                <!-- Global Search -->
                <div class="col-md-4 mb-3">
                    <label for="search" class="form-label">Pencarian Global</label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari berdasarkan {{ implode(', ', $searchFields) }}">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Date Range -->
                <div class="col-md-3 mb-3">
                    <label for="date_from" class="form-label">Dari Tanggal</label>
                    <input type="date" 
                           class="form-control" 
                           id="date_from" 
                           name="date_from" 
                           value="{{ request('date_from') }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label for="date_to" class="form-label">Sampai Tanggal</label>
                    <input type="date" 
                           class="form-control" 
                           id="date_to" 
                           name="date_to" 
                           value="{{ request('date_to') }}">
                </div>

                <!-- Dynamic Filters -->
                @foreach($filters as $filter)
                <div class="col-md-3 mb-3">
                    <label for="{{ $filter['name'] }}" class="form-label">{{ $filter['label'] }}</label>
                    @if($filter['type'] === 'select')
                        <select class="form-control" id="{{ $filter['name'] }}" name="{{ $filter['name'] }}">
                            <option value="">Semua {{ $filter['label'] }}</option>
                            @foreach($filter['options'] as $value => $label)
                                <option value="{{ $value }}" {{ request($filter['name']) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($filter['type'] === 'number')
                        <input type="number" 
                               class="form-control" 
                               id="{{ $filter['name'] }}" 
                               name="{{ $filter['name'] }}" 
                               value="{{ request($filter['name']) }}" 
                               placeholder="{{ $filter['placeholder'] ?? '' }}">
                    @else
                        <input type="text" 
                               class="form-control" 
                               id="{{ $filter['name'] }}" 
                               name="{{ $filter['name'] }}" 
                               value="{{ request($filter['name']) }}" 
                               placeholder="{{ $filter['placeholder'] ?? '' }}">
                    @endif
                </div>
                @endforeach
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ $action }}" class="btn btn-secondary">
                        <i class="fas fa-refresh"></i> Reset
                    </a>
                    <button type="button" class="btn btn-info" id="toggleAdvanced">
                        <i class="fas fa-cog"></i> Filter Lanjutan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Clear search
    document.getElementById('clearSearch').addEventListener('click', function() {
        document.getElementById('search').value = '';
        document.getElementById('searchForm').submit();
    });

    // Auto submit on filter change
    document.querySelectorAll('select, input[type="date"]').forEach(function(element) {
        element.addEventListener('change', function() {
            document.getElementById('searchForm').submit();
        });
    });

    // Toggle advanced filters
    document.getElementById('toggleAdvanced').addEventListener('click', function() {
        const advancedFilters = document.querySelectorAll('.col-md-3:not(:first-child):not(:nth-child(2)):not(:nth-child(3))');
        advancedFilters.forEach(function(filter) {
            filter.style.display = filter.style.display === 'none' ? 'block' : 'none';
        });
    });

    // Initially hide advanced filters if no values
    const hasAdvancedValues = @json(collect($filters)->pluck('name')->some(function($name) { return request($name); }));
    if (!hasAdvancedValues) {
        const advancedFilters = document.querySelectorAll('.col-md-3:nth-child(n+5)');
        advancedFilters.forEach(function(filter) {
            filter.style.display = 'none';
        });
    }
});
</script>
@endpush