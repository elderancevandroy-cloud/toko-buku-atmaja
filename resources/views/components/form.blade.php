@props([
    'fields' => [],
    'form_config' => [],
    'model' => null,
    'has_file_upload' => false,
    'http_method' => 'POST',
    'needs_method_spoofing' => false,
    'title' => null,
    'card' => true
])

@php
    $formAction = $form_config['action'] ?? '#';
    $submitLabel = $form_config['submit_label'] ?? 'Simpan';
    $cancelUrl = $form_config['cancel_url'] ?? url()->previous();
    $enctype = $has_file_upload ? 'multipart/form-data' : 'application/x-www-form-urlencoded';
@endphp

@if($card)
<div class="card">
    @if($title)
    <div class="card-header">
        <h5 class="card-title mb-0">{{ $title }}</h5>
    </div>
    @endif
    
    <div class="card-body">
@endif

<form action="{{ $formAction }}" 
      method="{{ $http_method }}" 
      @if($has_file_upload) enctype="multipart/form-data" @endif
      novalidate>
    
    @csrf
    
    @if($needs_method_spoofing)
        @method($form_config['method'])
    @endif
    
    <div class="row">
        @foreach($fields as $field)
            @if($field['type'] === 'hidden')
                <input type="hidden" name="{{ $field['name'] }}" value="{{ $field['value'] }}">
            @else
                <div class="col-md-{{ $field['col_width'] ?? 12 }} mb-3">
                    @if($field['type'] === 'select')
                        <x-select 
                            name="{{ $field['name'] }}"
                            label="{{ $field['label'] }}"
                            :options="$field['options']"
                            :selected="$field['value']"
                            :required="$field['required']"
                            :placeholder="$field['empty_option'] ?? 'Pilih ' . $field['label']"
                        />
                    @elseif($field['type'] === 'textarea')
                        <label for="{{ $field['name'] }}" class="form-label">
                            {{ $field['label'] }}
                            @if($field['required'])
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        <textarea 
                            class="form-control @error($field['name']) is-invalid @enderror"
                            id="{{ $field['name'] }}"
                            name="{{ $field['name'] }}"
                            rows="{{ $field['rows'] ?? 3 }}"
                            @if($field['placeholder']) placeholder="{{ $field['placeholder'] }}" @endif
                            @if($field['required']) required @endif
                            @if($field['attributes'])
                                @foreach($field['attributes'] as $attr => $value)
                                    {{ $attr }}="{{ $value }}"
                                @endforeach
                            @endif
                        >{{ $field['value'] }}</textarea>
                        @error($field['name'])
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    @elseif($field['type'] === 'checkbox')
                        <div class="form-check">
                            <input 
                                class="form-check-input @error($field['name']) is-invalid @enderror"
                                type="checkbox"
                                id="{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                value="{{ $field['value'] ?? 1 }}"
                                @if(old($field['name'], $field['value'])) checked @endif
                                @if($field['required']) required @endif
                                @if($field['attributes'])
                                    @foreach($field['attributes'] as $attr => $value)
                                        {{ $attr }}="{{ $value }}"
                                    @endforeach
                                @endif
                            >
                            <label class="form-check-label" for="{{ $field['name'] }}">
                                {{ $field['label'] }}
                                @if($field['required'])
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            @error($field['name'])
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @elseif($field['type'] === 'radio')
                        <label class="form-label">
                            {{ $field['label'] }}
                            @if($field['required'])
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        <div class="form-radio-group">
                            @foreach($field['options'] as $value => $label)
                                <div class="form-check">
                                    <input 
                                        class="form-check-input @error($field['name']) is-invalid @enderror"
                                        type="radio"
                                        id="{{ $field['name'] }}_{{ $value }}"
                                        name="{{ $field['name'] }}"
                                        value="{{ $value }}"
                                        @if(old($field['name'], $field['value']) == $value) checked @endif
                                        @if($field['required']) required @endif
                                    >
                                    <label class="form-check-label" for="{{ $field['name'] }}_{{ $value }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                            @error($field['name'])
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @else
                        <x-input 
                            name="{{ $field['name'] }}"
                            label="{{ $field['label'] }}"
                            type="{{ $field['type'] }}"
                            :value="$field['value']"
                            :required="$field['required']"
                            :placeholder="$field['placeholder']"
                            :min="$field['min'] ?? null"
                            :max="$field['max'] ?? null"
                            :step="$field['step'] ?? null"
                        />
                    @endif
                </div>
            @endif
        @endforeach
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ $cancelUrl }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> {{ $submitLabel }}
                </button>
            </div>
        </div>
    </div>
</form>

@if($card)
    </div>
</div>
@endif

@push('scripts')
@vite(['resources/js/form-enhancements.js'])
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation enhancement
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // Focus on first invalid field
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid') && this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });
        });
    }
    
    // Number input validation
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('input', function() {
            const min = parseFloat(this.getAttribute('min'));
            const max = parseFloat(this.getAttribute('max'));
            const value = parseFloat(this.value);
            
            if (!isNaN(min) && value < min) {
                this.setCustomValidity(`Nilai minimum adalah ${min}`);
            } else if (!isNaN(max) && value > max) {
                this.setCustomValidity(`Nilai maksimum adalah ${max}`);
            } else {
                this.setCustomValidity('');
            }
        });
    });
    
    // Decimal input formatting for price fields
    const priceInputs = document.querySelectorAll('input[name*="harga"], input[name*="price"]');
    priceInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const value = parseFloat(this.value);
            if (!isNaN(value)) {
                this.value = value.toFixed(2);
            }
        });
    });
});
</script>
@endpush