# Implementation Plan

- [x] 1. Set up Laravel project structure and base configuration





  - Create new Laravel 11 project
  - Configure database connection for MySQL with Laragon
  - Set up basic routing structure
  - _Requirements: 9.1_

- [x] 2. Create database migrations and models





  - [x] 2.1 Create migration for buku (books) table


    - Define table structure with id, judul, pengarang, penerbit, harga, stok columns
    - Set appropriate data types and constraints
    - _Requirements: 9.1, 1.2_

  - [x] 2.2 Create migration for kasir (cashiers) table


    - Define table structure with id, nama, email, no_karyawan columns
    - Set unique constraints for email and no_karyawan
    - _Requirements: 9.1, 4.2_

  - [x] 2.3 Create migration for distributor table


    - Define table structure with id, nama, alamat, telepon, email columns
    - _Requirements: 9.1, 3.2_

  - [x] 2.4 Create migration for pembelian (purchases) table


    - Define table structure with foreign keys to distributor and buku
    - Include jumlah, harga_beli, total, tanggal_pembelian columns
    - _Requirements: 9.1, 5.2_

  - [x] 2.5 Create migration for penjualan (sales) table


    - Define table structure with foreign key to kasir
    - Include total_harga, tanggal_penjualan columns
    - _Requirements: 9.1, 6.2_

  - [x] 2.6 Create migration for detail_penjualan (sale_details) table


    - Define table structure with foreign keys to penjualan and buku
    - Include jumlah, harga_satuan, subtotal columns
    - _Requirements: 9.1, 6.4_

  - [x] 2.7 Create database triggers for automatic stock management


    - Implement tambah_stok trigger for pembelian table
    - Implement stok_berkurang trigger for detail_penjualan table
    - _Requirements: 9.2, 9.3, 2.1, 2.2_

- [x] 3. Create Eloquent models with relationships





  - [x] 3.1 Create Book model


    - Define fillable fields and table name
    - Set up relationships to Purchase and SaleDetail models
    - _Requirements: 1.1, 9.4_

  - [x] 3.2 Create Cashier model


    - Define fillable fields and table name
    - Set up relationship to Sale model
    - _Requirements: 4.1, 9.4_

  - [x] 3.3 Create Distributor model


    - Define fillable fields and table name
    - Set up relationship to Purchase model
    - _Requirements: 3.1, 9.4_

  - [x] 3.4 Create Purchase model


    - Define fillable fields and table name
    - Set up relationships to Distributor and Book models
    - _Requirements: 5.1, 9.4_

  - [x] 3.5 Create Sale and SaleDetail models


    - Define fillable fields and table names
    - Set up relationships between Sale, SaleDetail, Cashier, and Book models
    - _Requirements: 6.1, 9.4_

- [x] 4. Create custom Blade components





  - [x] 4.1 Create Input component (x-input)


    - Build Input component class with name, label, type, value, required, placeholder properties
    - Create input.blade.php template with validation error display
    - _Requirements: 7.1, 7.3_

  - [x] 4.2 Create Select component (x-select)


    - Build Select component class with name, options, label, selected, required properties
    - Create select.blade.php template with validation error display
    - _Requirements: 7.2, 7.3_

- [x] 5. Create builder services





  - [x] 5.1 Create GridBuilder service


    - Implement GridBuilder class with model binding, column configuration, and action buttons
    - Add search functionality and pagination support
    - Create JSON data method for AJAX loading
    - _Requirements: 1.3, 8.4_

  - [x] 5.2 Create FormBuilder service


    - Implement FormBuilder class with field management and form generation
    - Add support for different input types and validation
    - Create form rendering method
    - _Requirements: 1.4, 7.4_

- [x] 6. Create base layout and view inheritance





  - [x] 6.1 Create main layout template


    - Build app.blade.php with navigation, flash message display, and content sections
    - Include CSS and JavaScript assets
    - _Requirements: 7.5_

  - [x] 6.2 Create grid and form component templates


    - Build grid.blade.php template for GridBuilder
    - Build form.blade.php template for FormBuilder
    - _Requirements: 1.3, 1.4_

- [x] 7. Create Form Request classes for validation




  - [x] 7.1 Create BookRequest validation class


    - Define validation rules for book creation and updates
    - Include custom error messages in Indonesian
    - _Requirements: 1.2, 7.3_

  - [x] 7.2 Create CashierRequest validation class


    - Define validation rules for cashier creation and updates
    - _Requirements: 4.2, 7.3_

  - [x] 7.3 Create DistributorRequest validation class


    - Define validation rules for distributor creation and updates
    - _Requirements: 3.2, 7.3_

  - [x] 7.4 Create PurchaseRequest validation class


    - Define validation rules for purchase creation and updates
    - _Requirements: 5.2, 7.3_

  - [x] 7.5 Create SaleRequest validation class


    - Define validation rules for sale creation and updates
    - _Requirements: 6.2, 7.3_

- [x] 8. Create controllers with CRUD operations





  - [x] 8.1 Create BookController


    - Implement index, create, store, show, edit, update, destroy methods
    - Add GridBuilder and FormBuilder integration
    - Include JSON API endpoints for RPC-style responses
    - _Requirements: 1.1, 1.3, 1.4, 1.5, 8.1, 8.2_

  - [x] 8.2 Create CashierController


    - Implement full CRUD operations with validation
    - Add GridBuilder and FormBuilder integration
    - Include JSON API endpoints
    - _Requirements: 4.1, 4.3, 4.4, 8.1, 8.2_

  - [x] 8.3 Create DistributorController


    - Implement full CRUD operations with validation
    - Add GridBuilder and FormBuilder integration
    - Include JSON API endpoints
    - _Requirements: 3.1, 3.3, 3.4, 8.1, 8.2_

  - [x] 8.4 Create PurchaseController


    - Implement CRUD operations with distributor and book relationships
    - Add automatic stock increase functionality
    - Include JSON API endpoints
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 8.1, 8.2_

  - [x] 8.5 Create SaleController


    - Implement CRUD operations for sales and sale details
    - Add automatic stock decrease functionality
    - Calculate total amounts and handle multiple sale details
    - Include JSON API endpoints
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 8.1, 8.2_

- [x] 9. Create view templates for each module





  - [x] 9.1 Create book management views


    - Build index.blade.php with GridBuilder integration
    - Build create.blade.php and edit.blade.php with FormBuilder
    - Build show.blade.php for book details
    - _Requirements: 1.3, 1.4_

  - [x] 9.2 Create cashier management views


    - Build complete CRUD view templates using Blade components
    - _Requirements: 4.3, 4.4_

  - [x] 9.3 Create distributor management views


    - Build complete CRUD view templates using Blade components
    - _Requirements: 3.3, 3.4_

  - [x] 9.4 Create purchase management views


    - Build views with distributor and book selection dropdowns
    - Include purchase history and details display
    - _Requirements: 5.4_

  - [x] 9.5 Create sale management views


    - Build views for creating sales with multiple book selections
    - Include sale history and details display
    - Add cashier selection functionality
    - _Requirements: 6.1, 6.2, 6.5_

- [x] 10. Set up routing and API endpoints





  - [x] 10.1 Create web routes for all modules


    - Define resource routes for books, cashiers, distributors, purchases, sales
    - _Requirements: 1.1, 3.1, 4.1, 5.1, 6.1_

  - [x] 10.2 Create API routes for JSON endpoints


    - Define RPC-style API routes for all CRUD operations
    - _Requirements: 8.1, 8.2, 8.3_

- [x] 11. Implement flash message system





  - [x] 11.1 Add flash message handling to controllers


    - Implement success, error, warning, and info message types
    - Add flash messages for all CRUD operations
    - _Requirements: 1.5, 7.4_

  - [x] 11.2 Create flash message display in layout


    - Add flash message display component to main layout
    - Style messages with appropriate colors and icons
    - _Requirements: 7.4_

- [x] 12. Add JavaScript for dynamic functionality





  - [x] 12.1 Create GridBuilder JavaScript


    - Implement AJAX loading for grid data
    - Add search and pagination functionality
    - _Requirements: 8.4_

  - [x] 12.2 Create form enhancement JavaScript


    - Add dynamic form validation feedback
    - Implement dependent dropdown functionality for sales
    - _Requirements: 7.3_

- [x] 13. Create database seeders for testing data




  - Create seeders for books, cashiers, distributors
  - Generate sample data for testing purposes
  - _Requirements: 9.1_