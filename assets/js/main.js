// fixIT - Main JavaScript

// Detect API base path based on current directory
// If we're in admin/ subdirectory, we need ../api/
const currentPath = window.location.pathname;
const isAdminPage = currentPath.includes('/admin/');
const API_BASE = isAdminPage ? '../api/' : 'api/';

// City data
const citiesByState = {
    'Kosovo': ['Pristina', 'Prizren', 'Peja', 'Gjakova', 'Mitrovica', 'Ferizaj', 'Gjilan'],
    'Albania': ['Tirana', 'Durres', 'Vlora', 'Shkoder', 'Elbasan', 'Korca'],
    'North Macedonia': ['Skopje', 'Bitola', 'Kumanovo', 'Prilep', 'Tetovo'],
    'Serbia': ['Belgrade', 'Novi Sad', 'Nis', 'Kragujevac', 'Subotica'],
    'Montenegro': ['Podgorica', 'Niksic', 'Pljevlja', 'Bijelo Polje', 'Cetinje']
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    setupStateCitySelectors();
    checkAuthStatus();
    setupAccessibilityControls();
    setupNavbarScroll();
});

// Navbar scroll effect
function setupNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }
}

// State/City Selector Setup
function setupStateCitySelectors() {
    // Login modal
    const loginState = document.getElementById('loginState');
    const loginCity = document.getElementById('loginCity');
    
    if (loginState) {
        loginState.addEventListener('change', function() {
            updateCityOptions(this.value, loginCity);
        });
    }
    
    // Signup modal
    const signupState = document.getElementById('signupState');
    const signupCity = document.getElementById('signupCity');
    
    if (signupState) {
        signupState.addEventListener('change', function() {
            updateCityOptions(this.value, signupCity);
        });
    }
}

function updateCityOptions(state, citySelect) {
    if (!citySelect) return;
    
    citySelect.innerHTML = '<option value="">Select City</option>';
    
    if (state && citiesByState[state]) {
        citySelect.disabled = false;
        citiesByState[state].forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
    } else {
        citySelect.disabled = true;
    }
}

// Modal Functions
function openLoginModal() {
    closeWelcomeModal();
    document.getElementById('loginModal').style.display = 'flex';
}

function closeLoginModal() {
    document.getElementById('loginModal').style.display = 'none';
}

function openSignupModal() {
    closeWelcomeModal();
    document.getElementById('signupModal').style.display = 'flex';
}

function closeSignupModal() {
    document.getElementById('signupModal').style.display = 'none';
}

function closeWelcomeModal() {
    const welcomeModal = document.getElementById('welcomeModal');
    if (welcomeModal) {
        welcomeModal.style.opacity = '0';
        welcomeModal.style.transition = 'opacity 0.5s ease';
        setTimeout(() => {
            welcomeModal.style.display = 'none';
        }, 500);
    }
}

// Authentication Functions
async function handleLogin(event) {
    event.preventDefault();
    
    const username = document.getElementById('loginUsername').value;
    const password = document.getElementById('loginPassword').value;
    const state = document.getElementById('loginState')?.value || '';
    const city = document.getElementById('loginCity')?.value || '';
    
    if (!username || !password) {
        showAlert('Please fill in all required fields', 'error');
        return;
    }
    
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loading"></span> Logging in...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch(API_BASE + 'auth.php?action=login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username, password, state, city })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Login successful! Welcome back!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showAlert(data.message || 'Invalid username or password', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    } catch (error) {
        showAlert('An error occurred. Please try again.', 'error');
        console.error('Login error:', error);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

async function handleSignup(event) {
    event.preventDefault();
    
    const name = document.getElementById('signupName').value;
    const surname = document.getElementById('signupSurname').value;
    const username = document.getElementById('signupUsername').value;
    const email = document.getElementById('signupEmail').value;
    const password = document.getElementById('signupPassword').value;
    const state = document.getElementById('signupState').value;
    const city = document.getElementById('signupCity').value;
    
    if (!name || !surname || !username || !email || !password || !state || !city) {
        showAlert('Please fill in all required fields', 'error');
        return;
    }
    
    if (password.length < 6) {
        showAlert('Password must be at least 6 characters long', 'error');
        return;
    }
    
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loading"></span> Creating account...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch(API_BASE + 'auth.php?action=register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ name, surname, username, email, password, state, city })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Account created successfully! Please login.', 'success');
            setTimeout(() => {
                closeSignupModal();
                openLoginModal();
            }, 1500);
        } else {
            showAlert(data.message || 'Registration failed. Username or email may already exist.', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    } catch (error) {
        showAlert('An error occurred. Please try again.', 'error');
        console.error('Signup error:', error);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

async function logout() {
    try {
        const response = await fetch(API_BASE + 'auth.php?action=logout', {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Redirect to home page after logout
            const homePath = isAdminPage ? '../index.php' : 'index.php';
            window.location.href = homePath;
        } else {
            showAlert('Logout failed. Please try again.', 'error');
        }
    } catch (error) {
        console.error('Logout error:', error);
        showAlert('An error occurred during logout.', 'error');
    }
}

async function checkAuthStatus() {
    try {
        const response = await fetch(API_BASE + 'auth.php?action=check');
        const data = await response.json();
        
        if (data.logged_in) {
            // User is logged in
            window.currentUser = data.user;
        }
    } catch (error) {
        console.error('Auth check error:', error);
    }
}

function isLoggedIn() {
    return window.currentUser !== undefined;
}

// Alert System
function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '80px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '3000';
    alertDiv.style.minWidth = '300px';
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        alertDiv.style.transition = 'opacity 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(alertDiv);
        }, 300);
    }, 3000);
}

// Accessibility Controls
function setupAccessibilityControls() {
    // Font size controls
    const fontSizeIncrease = document.getElementById('fontSizeIncrease');
    const fontSizeDecrease = document.getElementById('fontSizeDecrease');
    const fontSizeReset = document.getElementById('fontSizeReset');
    
    if (fontSizeIncrease) {
        fontSizeIncrease.addEventListener('click', () => changeFontSize(2));
    }
    if (fontSizeDecrease) {
        fontSizeDecrease.addEventListener('click', () => changeFontSize(-2));
    }
    if (fontSizeReset) {
        fontSizeReset.addEventListener('click', () => resetFontSize());
    }
}

function changeFontSize(delta) {
    const currentSize = parseInt(getComputedStyle(document.body).fontSize) || 16;
    const newSize = Math.max(12, Math.min(24, currentSize + delta));
    document.body.style.fontSize = newSize + 'px';
    localStorage.setItem('fontSize', newSize);
}

function resetFontSize() {
    document.body.style.fontSize = '';
    localStorage.removeItem('fontSize');
}

// Load saved preferences
window.addEventListener('DOMContentLoaded', function() {
    const savedFontSize = localStorage.getItem('fontSize');
    if (savedFontSize) {
        document.body.style.fontSize = savedFontSize + 'px';
    }
});

