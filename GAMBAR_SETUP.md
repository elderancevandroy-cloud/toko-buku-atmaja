# Setup Gambar Perpustakaan untuk Dashboard

## Langkah-langkah untuk mengganti gambar placeholder:

### 1. Simpan Gambar Perpustakaan
Simpan gambar perpustakaan yang Anda berikan dengan nama `library-hero.jpg` ke dalam folder:
```
storage/app/public/images/library-hero.jpg
```

### 2. Pastikan Storage Link Aktif
Jalankan command berikut jika belum:
```bash
php artisan storage:link
```

### 3. Format Gambar yang Disarankan
- **Format**: JPG atau PNG
- **Ukuran**: 800x600 pixels (rasio 4:3)
- **Ukuran file**: Maksimal 2MB untuk performa optimal
- **Kualitas**: High quality untuk tampilan yang tajam

### 4. Alternatif Penyimpanan
Jika ingin menyimpan di folder public langsung:
```
public/images/library-hero.jpg
```
Dan ubah path di dashboard menjadi:
```php
{{ asset('images/library-hero.jpg') }}
```

### 5. Fitur yang Sudah Diimplementasikan

#### ✅ Responsive Design
- Gambar akan menyesuaikan ukuran layar
- Mobile-friendly dengan height yang lebih kecil

#### ✅ Hover Effects
- Zoom effect saat hover
- Overlay dengan informasi tambahan
- Animasi floating pada icon

#### ✅ Loading States
- Shimmer effect saat loading
- Fallback SVG jika gambar gagal load
- Error handling yang elegant

#### ✅ Accessibility
- Alt text yang descriptive
- Lazy loading untuk performa
- Proper semantic markup

### 6. Customization Options

#### Mengubah Overlay Text
Edit di `resources/views/dashboard.blade.php`:
```php
<div class="hero-text floating-overlay-icon">
    <i class="bi bi-book-half hero-icon"></i>
    <p class="fw-bold mb-0">Perpustakaan Digital</p>
    <small>Toko Buku Modern</small>
</div>
```

#### Mengubah Badge Corner
```php
<span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
    <i class="bi bi-star-fill me-1"></i>Premium
</span>
```

#### Mengubah Hover Animation
Edit CSS di bagian `@push('styles')` pada dashboard.blade.php

### 7. Testing
Setelah mengganti gambar, test:
1. Buka dashboard di browser
2. Hover pada gambar untuk melihat overlay effect
3. Test di mobile device untuk responsiveness
4. Check loading performance

### 8. Backup
Simpan gambar asli sebagai backup di:
```
storage/app/public/images/backup/
```

## Troubleshooting

### Gambar Tidak Muncul
1. Check apakah file ada di path yang benar
2. Pastikan storage:link sudah dijalankan
3. Check permission folder storage
4. Clear cache: `php artisan cache:clear`

### Gambar Terlalu Besar
1. Compress gambar menggunakan tools online
2. Resize ke ukuran yang disarankan
3. Convert ke format WebP untuk performa lebih baik

### Styling Tidak Sesuai
1. Check CSS di bagian `@push('styles')`
2. Adjust object-fit property
3. Modify border-radius dan shadow sesuai kebutuhan