/**
 * Form Enhancement JavaScript
 * Provides dynamic form validation feedback and dependent dropdown functionality
 */

class FormEnhancer {
    constructor(options = {}) {
        this.options = {
            formSelector: 'form',
            realTimeValidation: true,
            showValidationIcons: true,
            validationDelay: 300,
            dependentDropdowns: [],
            ...options
        };

        this.validationTimeout = null;
        this.init();
    }

    init() {
        this.setupFormValidation();
        this.setupDependentDropdowns();
        this.setupNumberFormatting();
        this.setupDateValidation();
        this.setupCustomValidators();
    }

    setupFormValidation() {
        const forms = document.querySelectorAll(this.options.formSelector);
        
        forms.forEach(form => {
            // Enhanced form submission validation
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    this.showValidationSummary(form);
                }
            });

            // Real-time validation for form fields
            if (this.options.realTimeValidation) {
                this.setupRealTimeValidation(form);
            }

            // Add validation icons if enabled
            if (this.options.showValidationIcons) {
                this.addValidationIcons(form);
            }
        });
    }

    setupRealTimeValidation(form) {
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            // Validation on blur
            input.addEventListener('blur', () => {
                this.validateField(input);
            });

            // Validation on input with delay
            input.addEventListener('input', () => {
                clearTimeout(this.validationTimeout);
                this.validationTimeout = setTimeout(() => {
                    this.validateField(input);
                }, this.options.validationDelay);
            });

            // Clear validation on focus
            input.addEventListener('focus', () => {
                this.clearFieldValidation(input);
            });
        });
    }

    validateField(field) {
        const isValid = this.checkFieldValidity(field);
        this.updateFieldValidationState(field, isValid);
        return isValid;
    }

    checkFieldValidity(field) {
        // Check HTML5 validity
        if (!field.checkValidity()) {
            return false;
        }

        // Custom validation rules
        const value = field.value.trim();
        const fieldName = field.name;
        const fieldType = field.type;

        // Required field validation
        if (field.hasAttribute('required') && !value) {
            field.setCustomValidity('Field ini wajib diisi');
            return false;
        }

        // Email validation
        if (fieldType === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                field.setCustomValidity('Format email tidak valid');
                return false;
            }
        }

        // Phone number validation
        if (fieldName.includes('telepon') || fieldName.includes('phone')) {
            const phoneRegex = /^[\d\s\-\+\(\)]+$/;
            if (value && !phoneRegex.test(value)) {
                field.setCustomValidity('Format nomor telepon tidak valid');
                return false;
            }
        }

        // Price validation
        if (fieldName.includes('harga') || fieldName.includes('price')) {
            const price = parseFloat(value);
            if (value && (isNaN(price) || price < 0)) {
                field.setCustomValidity('Harga harus berupa angka positif');
                return false;
            }
        }

        // Stock validation
        if (fieldName.includes('stok') || fieldName.includes('stock') || fieldName.includes('jumlah')) {
            const stock = parseInt(value);
            if (value && (isNaN(stock) || stock < 0)) {
                field.setCustomValidity('Jumlah harus berupa angka positif');
                return false;
            }
        }

        // Employee ID validation
        if (fieldName.includes('no_karyawan')) {
            if (value && value.length < 3) {
                field.setCustomValidity('Nomor karyawan minimal 3 karakter');
                return false;
            }
        }

        // Clear custom validity if all checks pass
        field.setCustomValidity('');
        return true;
    }

    updateFieldValidationState(field, isValid) {
        const formGroup = field.closest('.mb-3, .form-group, .col-md-6, .col-md-12');
        const feedback = formGroup?.querySelector('.invalid-feedback, .valid-feedback');
        
        // Remove existing validation classes
        field.classList.remove('is-valid', 'is-invalid');
        
        if (field.value.trim()) {
            if (isValid) {
                field.classList.add('is-valid');
                this.showFieldFeedback(field, 'Valid', 'valid');
            } else {
                field.classList.add('is-invalid');
                this.showFieldFeedback(field, field.validationMessage, 'invalid');
            }
        }
    }

    showFieldFeedback(field, message, type) {
        const formGroup = field.closest('.mb-3, .form-group, .col-md-6, .col-md-12');
        if (!formGroup) return;

        // Remove existing feedback
        const existingFeedback = formGroup.querySelector('.invalid-feedback, .valid-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }

        // Add new feedback
        const feedbackDiv = document.createElement('div');
        feedbackDiv.className = type === 'valid' ? 'valid-feedback' : 'invalid-feedback';
        feedbackDiv.textContent = message;
        
        field.parentNode.appendChild(feedbackDiv);
    }

    clearFieldValidation(field) {
        field.classList.remove('is-valid', 'is-invalid');
        const formGroup = field.closest('.mb-3, .form-group, .col-md-6, .col-md-12');
        const feedback = formGroup?.querySelector('.invalid-feedback, .valid-feedback');
        if (feedback) {
            feedback.remove();
        }
    }

    validateForm(form) {
        const fields = form.querySelectorAll('input, select, textarea');
        let isFormValid = true;
        let firstInvalidField = null;

        fields.forEach(field => {
            const isFieldValid = this.validateField(field);
            if (!isFieldValid && !firstInvalidField) {
                firstInvalidField = field;
            }
            isFormValid = isFormValid && isFieldValid;
        });

        // Focus on first invalid field
        if (firstInvalidField) {
            firstInvalidField.focus();
            firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        return isFormValid;
    }

    showValidationSummary(form) {
        const invalidFields = form.querySelectorAll('.is-invalid');
        if (invalidFields.length === 0) return;

        // Create or update validation summary
        let summaryDiv = form.querySelector('.validation-summary');
        if (!summaryDiv) {
            summaryDiv = document.createElement('div');
            summaryDiv.className = 'alert alert-danger validation-summary';
            form.insertBefore(summaryDiv, form.firstChild);
        }

        const errors = Array.from(invalidFields).map(field => {
            const label = this.getFieldLabel(field);
            return `${label}: ${field.validationMessage}`;
        });

        summaryDiv.innerHTML = `
            <h6><i class="bi bi-exclamation-triangle"></i> Terdapat kesalahan pada form:</h6>
            <ul class="mb-0">
                ${errors.map(error => `<li>${error}</li>`).join('')}
            </ul>
        `;

        summaryDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    getFieldLabel(field) {
        const formGroup = field.closest('.mb-3, .form-group, .col-md-6, .col-md-12');
        const label = formGroup?.querySelector('label');
        return label ? label.textContent.replace('*', '').trim() : field.name;
    }

    addValidationIcons(form) {
        const inputs = form.querySelectorAll('input:not([type="hidden"]), select, textarea');
        
        inputs.forEach(input => {
            if (input.parentNode.classList.contains('input-group')) return;
            
            const wrapper = document.createElement('div');
            wrapper.className = 'position-relative';
            
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);
            
            const icon = document.createElement('i');
            icon.className = 'validation-icon position-absolute';
            icon.style.cssText = 'right: 10px; top: 50%; transform: translateY(-50%); z-index: 5; display: none;';
            
            wrapper.appendChild(icon);
            
            // Update icon based on validation state
            const observer = new MutationObserver(() => {
                if (input.classList.contains('is-valid')) {
                    icon.className = 'validation-icon position-absolute bi bi-check-circle text-success';
                    icon.style.display = 'block';
                } else if (input.classList.contains('is-invalid')) {
                    icon.className = 'validation-icon position-absolute bi bi-x-circle text-danger';
                    icon.style.display = 'block';
                } else {
                    icon.style.display = 'none';
                }
            });
            
            observer.observe(input, { attributes: true, attributeFilter: ['class'] });
        });
    }

    setupDependentDropdowns() {
        this.options.dependentDropdowns.forEach(config => {
            this.initDependentDropdown(config);
        });

        // Auto-detect dependent dropdowns for sales forms
        this.setupSalesDependentDropdowns();
    }

    setupSalesDependentDropdowns() {
        // Handle book selection in sales forms
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('book-select')) {
                this.handleBookSelection(e.target);
            }
        });

        // Handle quantity changes
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                this.handleQuantityChange(e.target);
            }
        });
    }

    handleBookSelection(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const row = selectElement.closest('tr');
        
        if (!row) return;

        const priceInput = row.querySelector('.price-input');
        const quantityInput = row.querySelector('.quantity-input');
        
        if (selectedOption.value) {
            const price = selectedOption.dataset.price;
            const stock = selectedOption.dataset.stock;
            
            if (priceInput) {
                priceInput.value = price;
                priceInput.dispatchEvent(new Event('input'));
            }
            
            if (quantityInput) {
                quantityInput.setAttribute('max', stock);
                quantityInput.setAttribute('data-stock', stock);
                
                // Reset quantity if it exceeds available stock
                if (parseInt(quantityInput.value) > parseInt(stock)) {
                    quantityInput.value = Math.min(parseInt(quantityInput.value), parseInt(stock));
                }
            }
        } else {
            if (priceInput) priceInput.value = '';
            if (quantityInput) {
                quantityInput.removeAttribute('max');
                quantityInput.removeAttribute('data-stock');
            }
        }

        this.calculateRowSubtotal(row);
    }

    handleQuantityChange(quantityInput) {
        const row = quantityInput.closest('tr');
        const stock = parseInt(quantityInput.dataset.stock);
        const quantity = parseInt(quantityInput.value);
        
        // Validate stock availability
        if (stock && quantity > stock) {
            this.showStockWarning(quantityInput, stock);
            quantityInput.value = stock;
        }
        
        this.calculateRowSubtotal(row);
    }

    showStockWarning(input, availableStock) {
        // Create temporary warning message
        const warning = document.createElement('div');
        warning.className = 'alert alert-warning alert-sm mt-1';
        warning.innerHTML = `<i class="bi bi-exclamation-triangle"></i> Stok tidak mencukupi! Tersedia: ${availableStock}`;
        
        const existingWarning = input.parentNode.querySelector('.alert-warning');
        if (existingWarning) {
            existingWarning.remove();
        }
        
        input.parentNode.appendChild(warning);
        
        // Auto-remove warning after 3 seconds
        setTimeout(() => {
            if (warning.parentNode) {
                warning.remove();
            }
        }, 3000);
    }

    calculateRowSubtotal(row) {
        const priceInput = row.querySelector('.price-input');
        const quantityInput = row.querySelector('.quantity-input');
        const subtotalInput = row.querySelector('.subtotal-input');
        
        if (!priceInput || !quantityInput || !subtotalInput) return;
        
        const price = parseFloat(priceInput.value) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        const subtotal = price * quantity;
        
        subtotalInput.value = subtotal.toFixed(2);
        
        // Trigger total calculation
        this.calculateFormTotal();
    }

    calculateFormTotal() {
        const subtotalInputs = document.querySelectorAll('.subtotal-input');
        let total = 0;
        
        subtotalInputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        
        // Update total display
        const totalDisplay = document.getElementById('totalHarga');
        const totalInput = document.getElementById('totalHargaInput');
        
        if (totalDisplay) {
            totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }
        
        if (totalInput) {
            totalInput.value = total.toFixed(2);
        }
    }

    setupNumberFormatting() {
        // Format price inputs
        document.addEventListener('blur', (e) => {
            if (e.target.name && (e.target.name.includes('harga') || e.target.name.includes('price'))) {
                this.formatPriceInput(e.target);
            }
        });

        // Format number inputs
        document.addEventListener('input', (e) => {
            if (e.target.type === 'number') {
                this.validateNumberInput(e.target);
            }
        });
    }

    formatPriceInput(input) {
        const value = parseFloat(input.value);
        if (!isNaN(value) && value >= 0) {
            input.value = value.toFixed(2);
        }
    }

    validateNumberInput(input) {
        const min = parseFloat(input.getAttribute('min'));
        const max = parseFloat(input.getAttribute('max'));
        const value = parseFloat(input.value);
        
        if (!isNaN(min) && value < min) {
            input.setCustomValidity(`Nilai minimum adalah ${min}`);
        } else if (!isNaN(max) && value > max) {
            input.setCustomValidity(`Nilai maksimum adalah ${max}`);
        } else {
            input.setCustomValidity('');
        }
    }

    setupDateValidation() {
        const dateInputs = document.querySelectorAll('input[type="date"]');
        
        dateInputs.forEach(input => {
            input.addEventListener('change', () => {
                this.validateDateInput(input);
            });
        });
    }

    validateDateInput(input) {
        const value = input.value;
        const today = new Date().toISOString().split('T')[0];
        
        if (input.name.includes('tanggal_penjualan') || input.name.includes('tanggal_pembelian')) {
            if (value > today) {
                input.setCustomValidity('Tanggal tidak boleh lebih dari hari ini');
                return false;
            }
        }
        
        input.setCustomValidity('');
        return true;
    }

    setupCustomValidators() {
        // Add custom validation for specific fields
        document.addEventListener('input', (e) => {
            const field = e.target;
            
            // Employee ID validation
            if (field.name === 'no_karyawan') {
                this.validateEmployeeId(field);
            }
            
            // Book title validation
            if (field.name === 'judul') {
                this.validateBookTitle(field);
            }
        });
    }

    validateEmployeeId(input) {
        const value = input.value.trim();
        const pattern = /^[A-Z0-9]{3,10}$/;
        
        if (value && !pattern.test(value)) {
            input.setCustomValidity('Nomor karyawan harus 3-10 karakter (huruf besar dan angka)');
        } else {
            input.setCustomValidity('');
        }
    }

    validateBookTitle(input) {
        const value = input.value.trim();
        
        if (value && value.length < 2) {
            input.setCustomValidity('Judul buku minimal 2 karakter');
        } else {
            input.setCustomValidity('');
        }
    }

    // Public methods
    addDependentDropdown(config) {
        this.options.dependentDropdowns.push(config);
        this.initDependentDropdown(config);
    }

    initDependentDropdown(config) {
        const parentSelect = document.querySelector(config.parent);
        const childSelect = document.querySelector(config.child);
        
        if (!parentSelect || !childSelect) return;
        
        parentSelect.addEventListener('change', () => {
            this.updateDependentOptions(parentSelect, childSelect, config);
        });
    }

    updateDependentOptions(parentSelect, childSelect, config) {
        const parentValue = parentSelect.value;
        
        // Clear child options
        childSelect.innerHTML = '<option value="">Loading...</option>';
        childSelect.disabled = true;
        
        // Fetch new options (this would typically be an AJAX call)
        if (config.dataSource) {
            fetch(`${config.dataSource}?parent=${parentValue}`)
                .then(response => response.json())
                .then(data => {
                    this.populateSelectOptions(childSelect, data);
                    childSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error loading dependent options:', error);
                    childSelect.innerHTML = '<option value="">Error loading options</option>';
                });
        }
    }

    populateSelectOptions(select, options) {
        select.innerHTML = '<option value="">Pilih...</option>';
        
        options.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option.value;
            optionElement.textContent = option.text;
            select.appendChild(optionElement);
        });
    }

    // Utility methods
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
}

// Auto-initialize FormEnhancer
document.addEventListener('DOMContentLoaded', function() {
    new FormEnhancer();
});

// Export for manual initialization
window.FormEnhancer = FormEnhancer;