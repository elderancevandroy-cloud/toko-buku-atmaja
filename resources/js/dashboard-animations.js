// Dashboard Animations and Interactions
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize all dashboard animations
    initCounterAnimations();
    initFloatingAnimations();
    initCardHoverEffects();
    initScrollAnimations();
    
    // Counter Animation Function
    function initCounterAnimations() {
        const counters = document.querySelectorAll('.counter');
        
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const increment = target / 100;
            let current = 0;
            
            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    counter.textContent = Math.ceil(current);
                    setTimeout(updateCounter, 20);
                } else {
                    counter.textContent = target;
                }
            };
            
            // Start animation when element is visible
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        updateCounter();
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            
            observer.observe(counter);
        });
    }
    
    // Floating Animations
    function initFloatingAnimations() {
        // Add CSS animations dynamically
        const style = document.createElement('style');
        style.textContent = `
            .floating-book {
                animation: float 3s ease-in-out infinite;
            }
            
            .floating-icon {
                animation: floatIcon 4s ease-in-out infinite;
                opacity: 0.8;
                font-size: 1.5rem;
            }
            
            @keyframes float {
                0%, 100% { 
                    transform: translate(-50%, -50%) translateY(0px) rotate(0deg); 
                }
                50% { 
                    transform: translate(-50%, -50%) translateY(-20px) rotate(5deg); 
                }
            }
            
            @keyframes floatIcon {
                0%, 100% { 
                    transform: translateY(0px) rotate(0deg); 
                    opacity: 0.8; 
                }
                25% { 
                    transform: translateY(-10px) rotate(5deg); 
                    opacity: 1; 
                }
                50% { 
                    transform: translateY(-20px) rotate(-5deg); 
                    opacity: 0.6; 
                }
                75% { 
                    transform: translateY(-10px) rotate(3deg); 
                    opacity: 1; 
                }
            }
            
            .dashboard-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .dashboard-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
            }
            
            .stats-card {
                background-color: #020949 !important;
                color: white;
                overflow: hidden;
                position: relative;
            }
            
            .stats-card::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: rgba(255,255,255,0.1);
                transform: rotate(45deg);
                transition: all 0.5s;
                opacity: 0;
            }
            
            .stats-card:hover::before {
                animation: shine 0.8s ease-in-out;
            }
            
            @keyframes shine {
                0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); opacity: 0; }
                50% { opacity: 1; }
                100% { transform: translateX(100%) translateY(100%) rotate(45deg); opacity: 0; }
            }
            
            .pulse-animation {
                animation: pulse 2s infinite;
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Card Hover Effects
    function initCardHoverEffects() {
        // Stats cards hover effect
        document.querySelectorAll('.stats-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05) translateY(-5px)';
                
                // Add pulse to icon
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.add('pulse-animation');
                }
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1) translateY(0)';
                
                // Remove pulse from icon
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.remove('pulse-animation');
                }
            });
        });
        
        // Dashboard cards general hover
        document.querySelectorAll('.dashboard-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    }
    
    // Scroll Animations
    function initScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                }
            });
        }, observerOptions);
        
        // Observe all cards that don't already have animations
        document.querySelectorAll('.card:not(.animate__animated)').forEach(card => {
            observer.observe(card);
        });
    }
    
    // Add sparkle effect on random intervals
    function addSparkleEffect() {
        const sparkleContainer = document.getElementById('bookAnimation');
        if (!sparkleContainer) return;
        
        setInterval(() => {
            const sparkle = document.createElement('div');
            sparkle.innerHTML = '✨';
            sparkle.style.position = 'absolute';
            sparkle.style.left = Math.random() * 100 + '%';
            sparkle.style.top = Math.random() * 100 + '%';
            sparkle.style.fontSize = '1rem';
            sparkle.style.animation = 'sparkle 2s ease-out forwards';
            sparkle.style.pointerEvents = 'none';
            
            // Add sparkle animation
            if (!document.querySelector('#sparkle-style')) {
                const sparkleStyle = document.createElement('style');
                sparkleStyle.id = 'sparkle-style';
                sparkleStyle.textContent = `
                    @keyframes sparkle {
                        0% { opacity: 0; transform: scale(0) rotate(0deg); }
                        50% { opacity: 1; transform: scale(1) rotate(180deg); }
                        100% { opacity: 0; transform: scale(0) rotate(360deg); }
                    }
                `;
                document.head.appendChild(sparkleStyle);
            }
            
            sparkleContainer.appendChild(sparkle);
            
            // Remove sparkle after animation
            setTimeout(() => {
                if (sparkle.parentNode) {
                    sparkle.parentNode.removeChild(sparkle);
                }
            }, 2000);
        }, 3000);
    }
    
    // Initialize sparkle effect
    addSparkleEffect();
    
    // Add typing effect to welcome text
    function initTypingEffect() {
        const welcomeText = document.querySelector('.display-4');
        if (welcomeText) {
            const originalText = welcomeText.textContent;
            welcomeText.textContent = '';
            
            let i = 0;
            const typeWriter = () => {
                if (i < originalText.length) {
                    welcomeText.textContent += originalText.charAt(i);
                    i++;
                    setTimeout(typeWriter, 100);
                }
            };
            
            // Start typing effect after a delay
            setTimeout(typeWriter, 500);
        }
    }
    
    // Initialize typing effect
    initTypingEffect();
});

// Export functions for external use
window.DashboardAnimations = {
    initCounterAnimations,
    initFloatingAnimations,
    initCardHoverEffects,
    initScrollAnimations
};