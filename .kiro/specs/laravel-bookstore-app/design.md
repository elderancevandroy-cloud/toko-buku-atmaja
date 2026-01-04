# Design Document

## Overview

The Laravel Bookstore Application is a comprehensive inventory and sales management system built with Laravel 11. It features automatic stock management through database triggers, custom Blade components for consistent UI, and RPC-style JSON APIs. The application uses MySQL with Laragon for local development and implements a clean MVC architecture with custom builders for forms and grids.

## Architecture

### Application Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── BookController.php
│   │   ├── CashierController.php
│   │   ├── DistributorController.php
│   │   ├── PurchaseController.php
│   │   └── SaleController.php
│   └── Requests/
│       ├── BookRequest.php
│       ├── CashierRequest.php
│       ├── DistributorRequest.php
│       ├── PurchaseRequest.php
│       └── SaleRequest.php
├── Models/
│   ├── Book.php
│   ├── Cashier.php
│   ├── Distributor.php
│   ├── Purchase.php
│   ├── Sale.php
│   └── SaleDetail.php
├── Services/
│   ├── GridBuilder.php
│   └── FormBuilder.php
└── View/
    └── Components/
        ├── Input.php
        └── Select.php

resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── components/
│   │   ├── input.blade.php
│   │   └── select.blade.php
│   ├── books/
│   ├── cashiers/
│   ├── distributors/
│   ├── purchases/
│   └── sales/
└── js/
    └── grid-builder.js

database/
├── migrations/
└── seeders/
```

### Database Design

#### Tables Structure

**buku (books)**
```sql
- id (bigint, primary key, auto_increment)
- judul (varchar(255), not null)
- pengarang (varchar(255), not null)
- penerbit (varchar(255))
- harga (decimal(10,2), not null)
- stok (int, default 0)
- created_at (timestamp)
- updated_at (timestamp)
```

**kasir (cashiers)**
```sql
- id (bigint, primary key, auto_increment)
- nama (varchar(255), not null)
- email (varchar(255), unique)
- no_karyawan (varchar(50), unique, not null)
- created_at (timestamp)
- updated_at (timestamp)
```

**distributor**
```sql
- id (bigint, primary key, auto_increment)
- nama (varchar(255), not null)
- alamat (text)
- telepon (varchar(20))
- email (varchar(255))
- created_at (timestamp)
- updated_at (timestamp)
```

**pembelian (purchases)**
```sql
- id (bigint, primary key, auto_increment)
- distributor_id (bigint, foreign key)
- buku_id (bigint, foreign key)
- jumlah (int, not null)
- harga_beli (decimal(10,2), not null)
- total (decimal(10,2), not null)
- tanggal_pembelian (date, not null)
- created_at (timestamp)
- updated_at (timestamp)
```

**penjualan (sales)**
```sql
- id (bigint, primary key, auto_increment)
- kasir_id (bigint, foreign key)
- total_harga (decimal(10,2), not null)
- tanggal_penjualan (date, not null)
- created_at (timestamp)
- updated_at (timestamp)
```

**detail_penjualan (sale_details)**
```sql
- id (bigint, primary key, auto_increment)
- penjualan_id (bigint, foreign key)
- buku_id (bigint, foreign key)
- jumlah (int, not null)
- harga_satuan (decimal(10,2), not null)
- subtotal (decimal(10,2), not null)
- created_at (timestamp)
- updated_at (timestamp)
```

#### Database Triggers

**tambah_stok Trigger**
```sql
DELIMITER $$
CREATE TRIGGER tambah_stok 
AFTER INSERT ON pembelian
FOR EACH ROW
BEGIN
    UPDATE buku 
    SET stok = stok + NEW.jumlah 
    WHERE id = NEW.buku_id;
END$$
DELIMITER ;
```

**stok_berkurang Trigger**
```sql
DELIMITER $$
CREATE TRIGGER stok_berkurang 
AFTER INSERT ON detail_penjualan
FOR EACH ROW
BEGIN
    UPDATE buku 
    SET stok = stok - NEW.jumlah 
    WHERE id = NEW.buku_id;
END$$
DELIMITER ;
```

## Components and Interfaces

### Custom Blade Components

#### Input Component (x-input)
```php
// app/View/Components/Input.php
class Input extends Component
{
    public $name;
    public $label;
    public $type;
    public $value;
    public $required;
    public $placeholder;
    
    public function __construct($name, $label = null, $type = 'text', $value = null, $required = false, $placeholder = null)
    {
        $this->name = $name;
        $this->label = $label ?? ucfirst($name);
        $this->type = $type;
        $this->value = old($name, $value);
        $this->required = $required;
        $this->placeholder = $placeholder;
    }
}
```

#### Select Component (x-select)
```php
// app/View/Components/Select.php
class Select extends Component
{
    public $name;
    public $label;
    public $options;
    public $selected;
    public $required;
    public $placeholder;
    
    public function __construct($name, $options = [], $label = null, $selected = null, $required = false, $placeholder = null)
    {
        $this->name = $name;
        $this->label = $label ?? ucfirst($name);
        $this->options = $options;
        $this->selected = old($name, $selected);
        $this->required = $required;
        $this->placeholder = $placeholder;
    }
}
```

### Builder Services

#### GridBuilder Service
```php
// app/Services/GridBuilder.php
class GridBuilder
{
    protected $model;
    protected $columns = [];
    protected $actions = [];
    protected $searchable = [];
    
    public function setModel($model)
    public function addColumn($key, $label, $sortable = true)
    public function addAction($label, $route, $class = 'btn-primary')
    public function setSearchable($columns)
    public function render($view = 'components.grid')
    public function getJsonData(Request $request)
}
```

#### FormBuilder Service
```php
// app/Services/FormBuilder.php
class FormBuilder
{
    protected $fields = [];
    protected $model;
    protected $action;
    protected $method = 'POST';
    
    public function setModel($model)
    public function setAction($action, $method = 'POST')
    public function addField($type, $name, $label, $options = [])
    public function addInput($name, $label, $type = 'text', $options = [])
    public function addSelect($name, $label, $selectOptions = [], $options = [])
    public function render($view = 'components.form')
}
```

### Controllers Design

#### Base Controller Pattern
All controllers will extend a BaseController that provides:
- GridBuilder and FormBuilder injection
- Standard CRUD methods
- JSON response formatting
- Flash message handling

#### RPC-Style JSON Endpoints
Each controller will provide JSON endpoints:
- `GET /api/{resource}` - List with pagination and search
- `POST /api/{resource}` - Create new record
- `GET /api/{resource}/{id}` - Get single record
- `PUT /api/{resource}/{id}` - Update record
- `DELETE /api/{resource}/{id}` - Delete record

## Data Models

### Eloquent Relationships

#### Book Model
```php
class Book extends Model
{
    protected $table = 'buku';
    protected $fillable = ['judul', 'pengarang', 'penerbit', 'harga', 'stok'];
    
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'buku_id');
    }
    
    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class, 'buku_id');
    }
}
```

#### Sale Model
```php
class Sale extends Model
{
    protected $table = 'penjualan';
    protected $fillable = ['kasir_id', 'total_harga', 'tanggal_penjualan'];
    
    public function cashier()
    {
        return $this->belongsTo(Cashier::class, 'kasir_id');
    }
    
    public function details()
    {
        return $this->hasMany(SaleDetail::class, 'penjualan_id');
    }
}
```

#### Purchase Model
```php
class Purchase extends Model
{
    protected $table = 'pembelian';
    protected $fillable = ['distributor_id', 'buku_id', 'jumlah', 'harga_beli', 'total', 'tanggal_pembelian'];
    
    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
    
    public function book()
    {
        return $this->belongsTo(Book::class, 'buku_id');
    }
}
```

## Error Handling

### Validation Strategy
- Use Form Request classes for all input validation
- Implement custom validation rules for business logic
- Return JSON errors for API endpoints
- Display validation errors in forms using Flash Messages

### Exception Handling
- Custom exception handler for API responses
- Database constraint violation handling
- Stock validation (prevent negative stock)
- Relationship integrity checks

### Flash Message System
```php
// Flash message types
- success: Green background, checkmark icon
- error: Red background, X icon  
- warning: Yellow background, warning icon
- info: Blue background, info icon

// Usage in controllers
return redirect()->back()->with('success', 'Data berhasil disimpan');
return redirect()->back()->with('error', 'Stok tidak mencukupi');
```

## Testing Strategy

### Manual Testing Approach
Since unit tests are not required, testing will focus on:

1. **Browser Testing**
   - Test all CRUD operations through web interface
   - Verify form validation and error messages
   - Test GridBuilder pagination and search
   - Verify automatic stock updates

2. **Database Testing**
   - Verify trigger functionality manually
   - Test referential integrity
   - Check data consistency after operations

3. **API Testing**
   - Use Postman or similar tools for JSON endpoint testing
   - Verify RPC-style responses
   - Test error handling and validation

### Development Workflow
1. Create migration and model
2. Build controller with basic CRUD
3. Create views with Blade components
4. Test web interface functionality
5. Add JSON API endpoints
6. Test API responses
7. Verify database triggers
8. Final integration testing

## Implementation Notes

### Laravel 11 Specific Features
- Use simplified directory structure
- Leverage new Blade component syntax
- Implement streamlined routing
- Use built-in validation features

### Performance Considerations
- Implement database indexing on foreign keys
- Use eager loading for relationships
- Optimize GridBuilder queries with pagination
- Cache frequently accessed data

### Security Measures
- CSRF protection on all forms
- Input sanitization and validation
- SQL injection prevention through Eloquent
- XSS protection in Blade templates