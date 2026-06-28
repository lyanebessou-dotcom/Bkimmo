// Authentication JavaScript

// Register Form Handler
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        // Password validation
        if (password !== confirmPassword) {
            alert('Les mots de passe ne correspondent pas.');
            return;
        }
        
        if (password.length < 8) {
            alert('Le mot de passe doit contenir au moins 8 caractères.');
            return;
        }
        
        // Get form data
        const formData = {
            nom: document.getElementById('nom').value,
            email: document.getElementById('email').value,
            telephone: document.getElementById('telephone').value,
            password: password,
            type_compte: document.getElementById('type_compte').value
        };
        
        // Store in localStorage (for demo purposes)
        // In production, this would be sent to a backend API
        const users = JSON.parse(localStorage.getItem('users') || '[]');
        
        // Check if email already exists
        if (users.find(user => user.email === formData.email)) {
            alert('Cet email est déjà utilisé.');
            return;
        }
        
        // Add new user
        formData.id = Date.now();
        formData.date_creation = new Date().toISOString();
        users.push(formData);
        localStorage.setItem('users', JSON.stringify(users));
        
        // Show success message
        alert('Compte créé avec succès! Vous pouvez maintenant vous connecter.');
        
        // Redirect to login page
        window.location.href = 'login.html';
    });
}

// Login Form Handler
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    console.log('Login form found:', loginForm);

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            console.log('Email:', email);
            console.log('Password:', password);
            
            // Check for admin access with specific credentials
            if (email === 'lyanebessou@gmail.com' && password === '12345678') {
                console.log('Admin credentials matched');
                const adminUser = {
                    id: 1,
                    nom: 'Administrateur',
                    email: 'lyanebessou@gmail.com',
                    telephone: '+33123456789',
                    password: '12345678',
                    type_compte: 'admin',
                    date_creation: new Date().toISOString()
                };
                
                // Store admin session
                localStorage.setItem('currentUser', JSON.stringify(adminUser));
                console.log('Admin session stored');
                
                // Show success message
                alert('Connexion administrateur réussie!');
                
                // Redirect to admin page
                console.log('Redirecting to admin.html');
                window.location.href = 'admin.html';
                return;
            }
            
            console.log('Not admin credentials, checking users...');
            
            // Get users from localStorage
            const users = JSON.parse(localStorage.getItem('users') || '[]');
            console.log('Users found:', users.length);
            
            // Find user
            const user = users.find(u => u.email === email && u.password === password);
            
            if (user) {
                // Store user session
                localStorage.setItem('currentUser', JSON.stringify(user));
                
                // Show success message
                alert('Connexion réussie!');
                
                // Redirect based on user type
                if (user.type_compte === 'admin') {
                    window.location.href = 'admin.html';
                } else {
                    window.location.href = 'index.html';
                }
            } else {
                alert('Email ou mot de passe incorrect.');
            }
        });
    } else {
        console.error('Login form not found!');
    }
});

// Logout Handler
const logoutBtn = document.getElementById('logoutBtn');
if (logoutBtn) {
    logoutBtn.addEventListener('click', function() {
        localStorage.removeItem('currentUser');
        window.location.href = 'login.html';
    });
}

// Check if user is logged in
function checkAuth() {
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));
    if (!currentUser) {
        // Redirect to login if not authenticated
        if (window.location.pathname.includes('admin.html')) {
            window.location.href = 'login.html';
        }
    }
    return currentUser;
}

// Initialize auth check on admin page
if (window.location.pathname.includes('admin.html')) {
    checkAuth();
}

// Theme toggle for auth pages
const themeToggle = document.getElementById('themeToggle');
if (themeToggle) {
    const html = document.documentElement;
    const savedTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);
    
    themeToggle.addEventListener('click', () => {
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    });
}

function updateThemeIcon(theme) {
    const icon = themeToggle.querySelector('i');
    if (theme === 'dark') {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
    } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
    }
}

// Navigation toggle for auth pages
const navToggle = document.querySelector('.nav-toggle');
const navMenu = document.querySelector('.nav-menu');

if (navToggle && navMenu) {
    navToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
}

// Close nav menu when clicking on a link
document.querySelectorAll('.nav-menu a').forEach(link => {
    link.addEventListener('click', () => {
        if (navMenu) {
            navMenu.classList.remove('active');
        }
    });
});
