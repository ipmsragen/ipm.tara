// Mobile Navigation Toggle
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('active');
});

// Close mobile menu when clicking on a link
document.querySelectorAll('.nav-link').forEach(n => n.addEventListener('click', () => {
    hamburger.classList.remove('active');
    navMenu.classList.remove('active');
}));

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Navbar background change on scroll
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.style.background = 'rgba(255, 255, 255, 0.98)';
        navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.15)';
    } else {
        navbar.style.background = 'rgba(255, 255, 255, 0.95)';
        navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
    }
});

// Scroll animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animated');
        }
    });
}, observerOptions);

// Observe elements for animation
document.addEventListener('DOMContentLoaded', () => {
    const animateElements = document.querySelectorAll('.feature-card, .program-card, .gallery-item, .contact-item');
    animateElements.forEach(el => {
        el.classList.add('animate-on-scroll');
        observer.observe(el);
    });
});

// Counter animation for stats
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    
    function updateCounter() {
        start += increment;
        if (start < target) {
            element.textContent = Math.floor(start).toLocaleString();
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target.toLocaleString();
        }
    }
    
    updateCounter();
}

// Animate stats when they come into view
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const statNumbers = entry.target.querySelectorAll('.stat h3');
            statNumbers.forEach(stat => {
                const target = parseInt(stat.textContent.replace(/[^\d]/g, ''));
                if (target) {
                    stat.textContent = '0';
                    animateCounter(stat, target);
                }
            });
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

const statsSection = document.querySelector('.stats');
if (statsSection) {
    statsObserver.observe(statsSection);
}

// Form submission handling
const contactForm = document.querySelector('.contact-form');
if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(contactForm);
        const name = contactForm.querySelector('input[type="text"]').value;
        const email = contactForm.querySelector('input[type="email"]').value;
        const message = contactForm.querySelector('textarea').value;
        
        // Simple validation
        if (!name || !email || !message) {
            showNotification('Mohon lengkapi semua field!', 'error');
            return;
        }
        
        if (!isValidEmail(email)) {
            showNotification('Format email tidak valid!', 'error');
            return;
        }
        
        // Simulate form submission
        showNotification('Pesan berhasil dikirim! Kami akan segera menghubungi Anda.', 'success');
        contactForm.reset();
    });
}

// Email validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 400px;
    `;
    
    notification.querySelector('.notification-content').style.cssText = `
        display: flex;
        align-items: center;
        gap: 0.5rem;
    `;
    
    notification.querySelector('.notification-close').style.cssText = `
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        margin-left: auto;
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Close button functionality
    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    });
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Program card hover effects
document.querySelectorAll('.program-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-15px) scale(1.02)';
    });
    
    card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0) scale(1)';
    });
});

// Gallery modal functionality
document.querySelectorAll('.gallery-item').forEach(item => {
    item.addEventListener('click', () => {
        const overlay = item.querySelector('.gallery-overlay');
        const title = overlay.querySelector('h4').textContent;
        const description = overlay.querySelector('p').textContent;
        
        showGalleryModal(title, description);
    });
});

function showGalleryModal(title, description) {
    // Remove existing modal
    const existingModal = document.querySelector('.gallery-modal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Create modal
    const modal = document.createElement('div');
    modal.className = 'gallery-modal';
    modal.innerHTML = `
        <div class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>${title}</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>${description}</p>
                </div>
            </div>
        </div>
    `;
    
    // Add styles
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(5px);
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    
    modal.querySelector('.modal-overlay').style.cssText = `
        background: white;
        border-radius: 20px;
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow: hidden;
        transform: scale(0.8);
        transition: transform 0.3s ease;
    `;
    
    modal.querySelector('.modal-header').style.cssText = `
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 2rem;
        border-bottom: 1px solid #e2e8f0;
    `;
    
    modal.querySelector('.modal-body').style.cssText = `
        padding: 2rem;
    `;
    
    modal.querySelector('.modal-close').style.cssText = `
        background: none;
        border: none;
        font-size: 2rem;
        cursor: pointer;
        color: #666;
    `;
    
    // Add to page
    document.body.appendChild(modal);
    
    // Animate in
    setTimeout(() => {
        modal.style.opacity = '1';
        modal.querySelector('.modal-overlay').style.transform = 'scale(1)';
    }, 100);
    
    // Close functionality
    modal.querySelector('.modal-close').addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    function closeModal() {
        modal.style.opacity = '0';
        modal.querySelector('.modal-overlay').style.transform = 'scale(0.8)';
        setTimeout(() => modal.remove(), 300);
    }
}

// Parallax effect for hero section
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const parallaxElements = document.querySelectorAll('.gradient-orb');
    
    parallaxElements.forEach((element, index) => {
        const speed = 0.5 + (index * 0.1);
        element.style.transform = `translateY(${scrolled * speed}px)`;
    });
});

// Typing effect for hero title
function typeWriter(element, text, speed = 100) {
    let i = 0;
    element.innerHTML = '';
    
    function type() {
        if (i < text.length) {
            element.innerHTML += text.charAt(i);
            i++;
            setTimeout(type, speed);
        }
    }
    
    type();
}

// Initialize typing effect when page loads
document.addEventListener('DOMContentLoaded', () => {
    const heroTitle = document.querySelector('.hero-title');
    if (heroTitle) {
        const originalText = heroTitle.innerHTML;
        setTimeout(() => {
            typeWriter(heroTitle, originalText, 50);
        }, 1000);
    }
});

// Add loading animation
window.addEventListener('load', () => {
    document.body.classList.add('loaded');
});

// Add CSS for loading animation
const loadingStyles = document.createElement('style');
loadingStyles.textContent = `
    body {
        opacity: 0;
        transition: opacity 0.5s ease;
    }
    
    body.loaded {
        opacity: 1;
    }
    
    .notification {
        font-family: 'Poppins', sans-serif;
    }
    
    .gallery-modal {
        font-family: 'Poppins', sans-serif;
    }
`;
document.head.appendChild(loadingStyles);

// Add smooth reveal animation for sections
const revealElements = document.querySelectorAll('.features, .programs, .about, .gallery, .contact');
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, { threshold: 0.1 });

revealElements.forEach(element => {
    element.style.opacity = '0';
    element.style.transform = 'translateY(50px)';
    element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    revealObserver.observe(element);
});

// Add button click animations
document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('click', function(e) {
        // Create ripple effect
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s ease-out;
            pointer-events: none;
        `;
        
        this.style.position = 'relative';
        this.style.overflow = 'hidden';
        this.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
    });
});

// Add ripple animation CSS
const rippleStyles = document.createElement('style');
rippleStyles.textContent = `
    @keyframes ripple {
        to {
            transform: scale(2);
            opacity: 0;
        }
    }
`;
document.head.appendChild(rippleStyles);

// Login and Register Modal Functions
function openLoginModal() {
    const modal = document.getElementById('loginModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeLoginModal() {
    const modal = document.getElementById('loginModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

function openRegisterModal() {
    const modal = document.getElementById('registerModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeRegisterModal() {
    const modal = document.getElementById('registerModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

function switchToRegister() {
    closeLoginModal();
    setTimeout(() => {
        openRegisterModal();
    }, 300);
}

function switchToLogin() {
    closeRegisterModal();
    setTimeout(() => {
        openLoginModal();
    }, 300);
}

// Login Form Handler
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const rememberMe = document.getElementById('rememberMe').checked;
            
            // Simple validation
            if (!email || !password) {
                showNotification('Mohon lengkapi semua field!', 'error');
                return;
            }
            
            // Simulate login process
            showNotification('Sedang memproses login...', 'info');
            
            setTimeout(() => {
                // Simulate successful login
                showNotification('Login berhasil! Selamat datang di IPM TARA!', 'success');
                closeLoginModal();
                
                // Update UI to show logged in state
                updateLoginState(true, email);
                
                // Store login state
                if (rememberMe) {
                    localStorage.setItem('userEmail', email);
                    localStorage.setItem('rememberMe', 'true');
                }
            }, 1500);
        });
    }
    
    // Register Form Handler
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
        const firstName = document.getElementById('firstName').value;
        const lastName = document.getElementById('lastName').value;
        const email = document.getElementById('registerEmail').value;
        const username = document.getElementById('username').value;
        const password = document.getElementById('registerPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const birthPlace = document.getElementById('birthPlace').value;
        const gender = document.getElementById('gender').value;
        const address = document.getElementById('address').value;
        const phone = document.getElementById('phone').value;
        const school = document.getElementById('school').value;
        const nisn = document.getElementById('nisn').value;
        const pimpinanRanting = document.getElementById('pimpinanRanting').value;
        const pimpinanCabang = document.getElementById('pimpinanCabang').value || '';
        const photo = document.getElementById('photo').files[0];
        const agreeTerms = document.getElementById('agreeTerms').checked;
            
            // Validation
            if (!firstName || !lastName || !email || !username || !password || !confirmPassword || 
                !birthPlace || !gender || !address || !phone || !school || !nisn || 
                !pimpinanRanting || !photo || !agreeTerms) {
                showNotification('Mohon lengkapi semua field yang wajib!', 'error');
                return;
            }
            
            if (password !== confirmPassword) {
                showNotification('Password dan konfirmasi password tidak sama!', 'error');
                return;
            }
            
            // Validate photo file
            if (photo) {
                const maxSize = 2 * 1024 * 1024; // 2MB
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                
                if (photo.size > maxSize) {
                    showNotification('Ukuran foto maksimal 2MB!', 'error');
                    return;
                }
                
                if (!allowedTypes.includes(photo.type)) {
                    showNotification('Format foto harus JPG atau PNG!', 'error');
                    return;
                }
            }
            
            if (password.length < 8) {
                showNotification('Password minimal 8 karakter!', 'error');
                return;
            }
            
            if (!agreeTerms) {
                showNotification('Anda harus menyetujui syarat dan ketentuan!', 'error');
                return;
            }
            
            if (!isValidEmail(email)) {
                showNotification('Format email tidak valid!', 'error');
                return;
            }
            
            // Simulate registration process
            showNotification('Sedang memproses pendaftaran...', 'info');
            
            setTimeout(() => {
                // Simulate successful registration
                showNotification(`Pendaftaran berhasil! Selamat bergabung dengan IPM TARA, ${firstName} ${lastName}!`, 'success');
                closeRegisterModal();
                setTimeout(() => {
                    openLoginModal();
                }, 1000);
            }, 2000);
        });
    }
    
    // Social Login Handlers
    const googleButtons = document.querySelectorAll('.btn-google');
    googleButtons.forEach(button => {
        button.addEventListener('click', function() {
            showNotification('Fitur login dengan Google akan segera tersedia!', 'info');
        });
    });
    
    const facebookButtons = document.querySelectorAll('.btn-facebook');
    facebookButtons.forEach(button => {
        button.addEventListener('click', function() {
            showNotification('Fitur login dengan Facebook akan segera tersedia!', 'info');
        });
    });
    
    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            const loginModal = document.getElementById('loginModal');
            const registerModal = document.getElementById('registerModal');
            
            if (e.target === loginModal) {
                closeLoginModal();
            } else if (e.target === registerModal) {
                closeRegisterModal();
            }
        }
    });
    
    // Check if user is already logged in
    checkLoginState();
});

// Update login state in UI
function updateLoginState(isLoggedIn, email = '') {
    const navAuth = document.querySelector('.nav-auth');
    if (!navAuth) return;
    
    if (isLoggedIn) {
        navAuth.innerHTML = `
            <div class="user-menu">
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span>${email}</span>
                </div>
                <div class="user-dropdown">
                    <a href="#" onclick="showUserProfile()">
                        <i class="fas fa-user"></i> Profil
                    </a>
                    <a href="#" onclick="showUserSettings()">
                        <i class="fas fa-cog"></i> Pengaturan
                    </a>
                    <a href="#" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </a>
                </div>
            </div>
        `;
        
        // Add user menu styles
        addUserMenuStyles();
    } else {
        navAuth.innerHTML = `
            <button class="btn btn-login" onclick="openLoginModal()">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
            <button class="btn btn-register" onclick="openRegisterModal()">
                <i class="fas fa-user-plus"></i> Daftar
            </button>
        `;
    }
}

// Add user menu styles
function addUserMenuStyles() {
    const existingStyles = document.getElementById('userMenuStyles');
    if (existingStyles) return;
    
    const styles = document.createElement('style');
    styles.id = 'userMenuStyles';
    styles.textContent = `
        .user-menu {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #f8fafc;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .user-info:hover {
            background: #e2e8f0;
        }
        
        .user-info i {
            font-size: 1.2rem;
            color: #6366f1;
        }
        
        .user-info span {
            font-size: 0.9rem;
            color: #333;
            font-weight: 500;
        }
        
        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 0;
            min-width: 150px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .user-menu:hover .user-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .user-dropdown a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            color: #333;
            text-decoration: none;
            transition: background 0.3s ease;
        }
        
        .user-dropdown a:hover {
            background: #f8fafc;
        }
        
        .user-dropdown a i {
            width: 16px;
            color: #666;
        }
    `;
    document.head.appendChild(styles);
}

// Check login state on page load
function checkLoginState() {
    const rememberMe = localStorage.getItem('rememberMe');
    const userEmail = localStorage.getItem('userEmail');
    
    if (rememberMe === 'true' && userEmail) {
        updateLoginState(true, userEmail);
    }
}

// User menu functions
function showUserProfile() {
    showNotification('Fitur profil akan segera tersedia!', 'info');
}

function showUserSettings() {
    showNotification('Fitur pengaturan akan segera tersedia!', 'info');
}

function logout() {
    localStorage.removeItem('userEmail');
    localStorage.removeItem('rememberMe');
    updateLoginState(false);
    showNotification('Anda telah berhasil keluar!', 'success');
}

// Password strength indicator
function addPasswordStrengthIndicator() {
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    
    passwordInputs.forEach(input => {
        if (input.id === 'registerPassword') {
            input.addEventListener('input', function() {
                const password = this.value;
                const strength = calculatePasswordStrength(password);
                updatePasswordStrengthIndicator(this, strength);
            });
        }
    });
}

function calculatePasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
}

function updatePasswordStrengthIndicator(input, strength) {
    let indicator = input.parentNode.querySelector('.password-strength');
    
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.className = 'password-strength';
        indicator.style.cssText = `
            margin-top: 0.5rem;
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
        `;
        input.parentNode.appendChild(indicator);
    }
    
    const strengthBar = indicator.querySelector('.strength-bar') || document.createElement('div');
    strengthBar.className = 'strength-bar';
    strengthBar.style.cssText = `
        height: 100%;
        transition: all 0.3s ease;
        width: ${(strength / 5) * 100}%;
        background: ${strength < 2 ? '#ef4444' : strength < 4 ? '#f59e0b' : '#10b981'};
    `;
    
    if (!indicator.querySelector('.strength-bar')) {
        indicator.appendChild(strengthBar);
    }
}

// Initialize password strength indicator
document.addEventListener('DOMContentLoaded', addPasswordStrengthIndicator);

// Data Pimpinan Ranting dan Cabang
const pimpinanRantingData = [
    'PR IPM SMP Muhammadiyah 1 Sragen',
    'PR IPM SMP Muhammadiyah 2 Masaran',
    'PR IPM SMP Muhammadiyah 3 Sambungmacan',
    'PR IPM SMP Muhammadiyah 4 Sukodono',
    'PR IPM SMP Muhammadiyah 5 Tanon',
    'PR IPM SMP Muhammadiyah 7 Sumberlawang',
    'PR IPM SMP Muhammadiyah 9 Gemolong',
    'PR IPM SMP Muhammadiyah 11 Kedawung',
    'PR IPM SMP Muhammadiyah 12 Kalijambe',
    'PR IPM SMP Al Basyiir Muhammadiyah Gondang',
    'PR IPM SMP Al-Qalam Muhammadiyah Gemolong',
    'PR IPM SMP Birrul Walidain Muhammadiyah Plupuh',
    'PR IPM SMP Birrul Walidain Muh Sragen',
    'PR IPM SMP Darul Ihsan Muhammadiyah Sragen',
    'PR IPM SMP At-Taqwa Muhammadiyah Miri',
    'PR IPM MTs Muhammadiyah 1 Gemolong',
    'PR IPM MTs Muhammadiyah 2 Kalijambe',
    'PR IPM MTs Muhammadiyah 3 Masaran',
    'PR IPM MTs Muhammadiyah 4 Sambungmacan',
    'PR IPM MTs Muhammadiyah 5 Trombol',
    'PR IPM MTs Muhammadiyah 6 Sidoharjo',
    'PR IPM MTs Muhammadiyah 7 Sambirejo',
    'PR IPM MTs Muhammadiyah 9 Mondokan',
    'PR IPM SMA Muhammadiyah 1 Sragen',
    'PR IPM SMA Muhammadiyah 2 Gemolong',
    'PR IPM SMA Muhammadiyah 3 Masaran',
    'PR IPM SMA Muhammadiyah 8 Kalijambe',
    'PR IPM SMA Muhammadiyah 9 Sambirejo',
    'PR IPM SMA Trensains Muhammadiyah Sragen',
    'PR IPM SMK Muhammadiyah 1 Sragen',
    'PR IPM SMK Muhammadiyah 2 Sragen',
    'PR IPM SMK Muhammadiyah 3 Gemolong',
    'PR IPM SMK Muhammadiyah 4 Sragen',
    'PR IPM SMK Muhammadiyah 5 Miri',
    'PR IPM SMK Muhammadiyah 6 Gemolong',
    'PR IPM SMK Muhammadiyah 7 Sambungmacan',
    'PR IPM SMK Muhammadiyah 8 Tanon',
    'PR IPM SMK Muhammadiyah 9 Gondang',
    'PR IPM SMK Muhammadiyah 10 Masaran',
    'PR IPM SMK Muhammadiyah 11 Sumberlawang',
    'PR IPM SMK At-Taqwa Muhammadiyah Miri',
    'PR IPM MA Darul Ihsan Muhammadiyah Sragen',
    'PR IPM PPTQM Darrul Hikmah Muh Masaran'
];

const pimpinanCabangData = [
    'PC IPM Sragen Kota',
    'PC IPM Sumberlawang',
    'PC IPM Kalijambe',
    'PC IPM Gemolong'
];

// Initialize dropdown search functionality
function initializeDropdownSearch() {
    // Pimpinan Ranting Dropdown
    const rantingSearch = document.getElementById('pimpinanRantingSearch');
    const rantingDropdown = document.getElementById('rantingDropdown');
    const rantingHidden = document.getElementById('pimpinanRanting');
    
    if (rantingSearch && rantingDropdown && rantingHidden) {
        setupDropdownSearch(rantingSearch, rantingDropdown, rantingHidden, pimpinanRantingData);
    }
    
    // Pimpinan Cabang Dropdown
    const cabangSearch = document.getElementById('pimpinanCabangSearch');
    const cabangDropdown = document.getElementById('cabangDropdown');
    const cabangHidden = document.getElementById('pimpinanCabang');
    
    if (cabangSearch && cabangDropdown && cabangHidden) {
        setupDropdownSearch(cabangSearch, cabangDropdown, cabangHidden, pimpinanCabangData);
    }
}

function setupDropdownSearch(searchInput, dropdown, hiddenInput, data) {
    let selectedIndex = -1;
    let filteredData = [...data];
    
    // Show all options initially
    function showAllOptions() {
        dropdown.innerHTML = '';
        filteredData = [...data];
        filteredData.forEach((item, index) => {
            const option = document.createElement('div');
            option.className = 'dropdown-item';
            option.textContent = item;
            option.dataset.index = index;
            option.addEventListener('click', () => selectOption(item, option));
            dropdown.appendChild(option);
        });
        dropdown.classList.add('show');
    }
    
    // Filter options based on search
    function filterOptions(searchTerm) {
        filteredData = data.filter(item => 
            item.toLowerCase().includes(searchTerm.toLowerCase())
        );
        
        dropdown.innerHTML = '';
        
        if (filteredData.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'no-results';
            noResults.textContent = 'Tidak ada hasil ditemukan';
            dropdown.appendChild(noResults);
        } else {
            filteredData.forEach((item, index) => {
                const option = document.createElement('div');
                option.className = 'dropdown-item';
                option.textContent = item;
                option.dataset.index = index;
                option.addEventListener('click', () => selectOption(item, option));
                dropdown.appendChild(option);
            });
        }
        
        dropdown.classList.add('show');
        selectedIndex = -1;
    }
    
    // Select an option
    function selectOption(value, element) {
        searchInput.value = value;
        hiddenInput.value = value;
        dropdown.classList.remove('show');
        
        // Remove previous selection
        dropdown.querySelectorAll('.dropdown-item').forEach(item => {
            item.classList.remove('selected');
        });
        
        // Add selection to clicked item
        element.classList.add('selected');
    }
    
    // Event listeners
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value;
        if (searchTerm.length === 0) {
            showAllOptions();
        } else {
            filterOptions(searchTerm);
        }
    });
    
    searchInput.addEventListener('focus', () => {
        if (searchInput.value.length === 0) {
            showAllOptions();
        } else {
            filterOptions(searchInput.value);
        }
    });
    
    searchInput.addEventListener('keydown', (e) => {
        const items = dropdown.querySelectorAll('.dropdown-item:not(.no-results)');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            updateHighlight(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateHighlight(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (selectedIndex >= 0 && items[selectedIndex]) {
                selectOption(filteredData[selectedIndex], items[selectedIndex]);
            }
        } else if (e.key === 'Escape') {
            dropdown.classList.remove('show');
            selectedIndex = -1;
        }
    });
    
    function updateHighlight(items) {
        items.forEach((item, index) => {
            item.classList.toggle('highlighted', index === selectedIndex);
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
            selectedIndex = -1;
        }
    });
}

// Initialize dropdown search when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeDropdownSearch);

// Fix highlight styling
document.addEventListener('DOMContentLoaded', function() {
    const highlightElements = document.querySelectorAll('.highlight');
    highlightElements.forEach(element => {
        element.style.color = '#fbbf24';
        element.style.fontWeight = '700';
        element.style.textShadow = '0 0 10px rgba(251, 191, 36, 0.3)';
        element.style.display = 'inline';
    });
});

// Book Modal Functions
const bookData = {
    'ideologi-gerakan': {
        title: 'Ideologi Gerakan IPM',
        name: 'Ideologi Gerakan Ikatan Pelajar Muhammadiyah',
        description: 'Buku panduan yang menjelaskan landasan ideologis, visi, misi, dan nilai-nilai dasar yang menjadi pedoman dalam setiap gerakan dan aktivitas IPM.',
        downloadLink: 'https://drive.google.com/uc?export=download&id=1x4W3EafiDLdUkaVmaU_Ls0RtDUEhPkJ7' // Link Google Drive untuk download langsung
    },
    'administrasi-kesekretariatan': {
        title: 'Pedoman Administrasi Kesekretariatan IPM',
        name: 'Pedoman Administrasi Kesekretariatan Ikatan Pelajar Muhammadiyah',
        description: 'Panduan lengkap untuk mengelola administrasi kesekretariatan IPM, termasuk sistem surat-menyurat, arsip, dan dokumentasi organisasi.',
        downloadLink: '#' // Link Google Drive akan diisi nanti
    },
    'administrasi-keuangan': {
        title: 'Pedoman Administrasi Keuangan IPM',
        name: 'Pedoman Administrasi Keuangan Ikatan Pelajar Muhammadiyah',
        description: 'Panduan pengelolaan keuangan organisasi IPM, termasuk sistem akuntansi, pelaporan, dan transparansi keuangan.',
        downloadLink: '#' // Link Google Drive akan diisi nanti
    },
    'persidangan': {
        title: 'Pedoman Persidangan IPM',
        name: 'Pedoman Persidangan Ikatan Pelajar Muhammadiyah',
        description: 'Panduan teknis pelaksanaan persidangan IPM, termasuk tata tertib, prosedur, dan protokol persidangan.',
        downloadLink: '#' // Link Google Drive akan diisi nanti
    },
    'tata-keorganisasian': {
        title: 'Pedoman Tata Keorganisasian IPM',
        name: 'Pedoman Tata Keorganisasian Ikatan Pelajar Muhammadiyah',
        description: 'Panduan struktur organisasi, hierarki kepemimpinan, dan tata kelola organisasi IPM di semua tingkatan.',
        downloadLink: '#' // Link Google Drive akan diisi nanti
    },
    'protokoler-organisasi': {
        title: 'Protokoler Organisasi IPM',
        name: 'Protokoler Organisasi Ikatan Pelajar Muhammadiyah',
        description: 'Panduan protokoler dan tata cara dalam berbagai acara dan kegiatan resmi IPM.',
        downloadLink: '#' // Link Google Drive akan diisi nanti
    },
    'pedoman-ranting': {
        title: 'Pedoman Ranting IPM',
        name: 'Pedoman Ranting Ikatan Pelajar Muhammadiyah',
        description: 'Panduan khusus untuk pengelolaan ranting IPM, termasuk struktur, program, dan aktivitas di tingkat ranting.',
        downloadLink: '#' // Link Google Drive akan diisi nanti
    }
};

function openBookModal(bookId) {
    const modal = document.getElementById('bookModal');
    const book = bookData[bookId];
    
    if (!modal || !book) return;
    
    // Update modal content
    document.getElementById('bookTitle').textContent = book.title;
    document.getElementById('bookName').textContent = book.name;
    document.getElementById('bookDescription').textContent = book.description;
    document.getElementById('bookDownloadLink').href = book.downloadLink;
    
    // Show modal
    modal.style.display = 'flex';
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeBookModal() {
    const modal = document.getElementById('bookModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}
