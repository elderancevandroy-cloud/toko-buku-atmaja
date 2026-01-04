// Image Handler for Dashboard
document.addEventListener('DOMContentLoaded', function() {
    const heroImage = document.querySelector('.hero-image');
    
    if (heroImage) {
        // Add loading shimmer effect
        heroImage.classList.add('hero-image-shimmer');
        
        // Remove shimmer when image loads
        heroImage.addEventListener('load', function() {
            this.classList.remove('hero-image-shimmer');
        });
        
        // Handle image error with fallback
        heroImage.addEventListener('error', function() {
            this.classList.remove('hero-image-shimmer');
            // Fallback to a beautiful SVG library illustration
            this.src = 'data:image/svg+xml;base64,' + btoa(`
                <svg width="400" height="300" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#bg)"/>
                    
                    <!-- Bookshelf -->
                    <rect x="50" y="80" width="300" height="140" fill="#8B4513" rx="5"/>
                    <rect x="60" y="90" width="280" height="120" fill="#A0522D" rx="3"/>
                    
                    <!-- Books -->
                    <rect x="70" y="95" width="15" height="110" fill="#FF6B6B" rx="2"/>
                    <rect x="90" y="95" width="12" height="110" fill="#4ECDC4" rx="2"/>
                    <rect x="107" y="95" width="18" height="110" fill="#45B7D1" rx="2"/>
                    <rect x="130" y="95" width="14" height="110" fill="#96CEB4" rx="2"/>
                    <rect x="149" y="95" width="16" height="110" fill="#FFEAA7" rx="2"/>
                    <rect x="170" y="95" width="13" height="110" fill="#DDA0DD" rx="2"/>
                    <rect x="188" y="95" width="17" height="110" fill="#98D8C8" rx="2"/>
                    <rect x="210" y="95" width="15" height="110" fill="#F7DC6F" rx="2"/>
                    <rect x="230" y="95" width="14" height="110" fill="#BB8FCE" rx="2"/>
                    <rect x="249" y="95" width="16" height="110" fill="#85C1E9" rx="2"/>
                    <rect x="270" y="95" width="12" height="110" fill="#F8C471" rx="2"/>
                    <rect x="287" y="95" width="18" height="110" fill="#82E0AA" rx="2"/>
                    <rect x="310" y="95" width="15" height="110" fill="#F1948A" rx="2"/>
                    
                    <!-- Window light effect -->
                    <rect x="150" y="30" width="100" height="40" fill="#FFF9C4" opacity="0.7" rx="5"/>
                    
                    <!-- Text -->
                    <text x="200" y="250" font-family="Arial, sans-serif" font-size="16" fill="white" text-anchor="middle" font-weight="bold">Perpustakaan Digital</text>
                    <text x="200" y="270" font-family="Arial, sans-serif" font-size="12" fill="white" text-anchor="middle">Toko Buku Atmaja</text>
                </svg>
            `);
        });
    }
});

// Function to update hero image
function updateHeroImage(imageUrl) {
    const heroImage = document.querySelector('.hero-image');
    if (heroImage && imageUrl) {
        heroImage.src = imageUrl;
    }
}