// Admin Dashboard JavaScript

// Sidebar Navigation
const sidebarLinks = document.querySelectorAll('.sidebar-link');
const adminSections = document.querySelectorAll('.admin-section');

sidebarLinks.forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Remove active class from all links
        sidebarLinks.forEach(l => l.classList.remove('active'));
        
        // Add active class to clicked link
        this.classList.add('active');
        
        // Hide all sections
        adminSections.forEach(section => section.classList.remove('active'));
        
        // Show target section
        const targetId = this.getAttribute('href').substring(1);
        const targetSection = document.getElementById(targetId);
        if (targetSection) {
            targetSection.classList.add('active');
        }
    });
});

// Modal Handling
const bienModal = document.getElementById('bienModal');
const addBienBtn = document.getElementById('addBienBtn');
const closeModal = document.getElementById('closeModal');
const cancelModal = document.getElementById('cancelModal');
const bienForm = document.getElementById('bienForm');

if (addBienBtn) {
    addBienBtn.addEventListener('click', () => {
        bienModal.classList.add('active');
        document.getElementById('modalTitle').textContent = 'Ajouter un bien';
        bienForm.reset();
    });
}

if (closeModal) {
    closeModal.addEventListener('click', () => {
        bienModal.classList.remove('active');
    });
}

if (cancelModal) {
    cancelModal.addEventListener('click', () => {
        bienModal.classList.remove('active');
    });
}

// Close modal when clicking outside
bienModal.addEventListener('click', (e) => {
    if (e.target === bienModal) {
        bienModal.classList.remove('active');
    }
});

// Bien Form Submission
if (bienForm) {
    bienForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const bienData = Object.fromEntries(formData);
        
        // Get existing biens from localStorage
        const biens = JSON.parse(localStorage.getItem('biens') || '[]');
        
        // Add new bien
        bienData.id = Date.now();
        bienData.date_creation = new Date().toISOString();
        biens.push(bienData);
        localStorage.setItem('biens', JSON.stringify(biens));
        
        // Refresh table
        loadBiens();
        
        // Close modal
        bienModal.classList.remove('active');
        
        // Show success message
        alert('Bien ajouté avec succès!');
    });
}

// Load Biens into Table
function loadBiens() {
    const biens = JSON.parse(localStorage.getItem('biens') || '[]');
    const tableBody = document.getElementById('biensTableBody');
    
    if (tableBody) {
        tableBody.innerHTML = '';
        
        biens.forEach(bien => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><img src="${bien.image || 'https://via.placeholder.com/100'}" alt="Bien"></td>
                <td>${bien.titre}</td>
                <td>${bien.adresse}, ${bien.ville}</td>
                <td>${bien.prix}€/${bien.periode}</td>
                <td><span class="status-badge ${bien.statut}">${bien.statut}</span></td>
                <td>
                    <button class="btn-action edit" onclick="editBien(${bien.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn-action delete" onclick="deleteBien(${bien.id})"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }
}

// Edit Bien
function editBien(id) {
    const biens = JSON.parse(localStorage.getItem('biens') || '[]');
    const bien = biens.find(b => b.id === id);
    
    if (bien) {
        document.getElementById('modalTitle').textContent = 'Modifier le bien';
        
        // Fill form with bien data
        const form = document.getElementById('bienForm');
        form.querySelector('[name="titre"]').value = bien.titre;
        form.querySelector('[name="description"]').value = bien.description || '';
        form.querySelector('[name="adresse"]').value = bien.adresse;
        form.querySelector('[name="ville"]').value = bien.ville;
        form.querySelector('[name="code_postal"]').value = bien.code_postal;
        form.querySelector('[name="surface"]').value = bien.surface;
        form.querySelector('[name="chambres"]').value = bien.chambres;
        form.querySelector('[name="sdb"]').value = bien.sdb;
        form.querySelector('[name="prix"]').value = bien.prix;
        form.querySelector('[name="periode"]').value = bien.periode;
        form.querySelector('[name="image"]').value = bien.image || '';
        form.querySelector('[name="statut"]').value = bien.statut;
        
        // Show modal
        bienModal.classList.add('active');
    }
}

// Delete Bien
function deleteBien(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce bien?')) {
        const biens = JSON.parse(localStorage.getItem('biens') || '[]');
        const updatedBiens = biens.filter(b => b.id !== id);
        localStorage.setItem('biens', JSON.stringify(updatedBiens));
        
        // Refresh table
        loadBiens();
        
        alert('Bien supprimé avec succès!');
    }
}

// Load Reservations
function loadReservations() {
    const reservations = JSON.parse(localStorage.getItem('reservations') || '[]');
    // Implementation for loading reservations
}

// Load Users
function loadUsers() {
    const users = JSON.parse(localStorage.getItem('users') || '[]');
    // Implementation for loading users
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Load initial data
    loadBiens();
    
    // Check authentication
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));
    if (!currentUser) {
        window.location.href = 'login.html';
    }
    
    // Set user name in header
    const userName = document.querySelector('.user-name');
    if (userName && currentUser) {
        userName.textContent = currentUser.nom;
    }
});

// Theme toggle
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

// API Form Handler
const apiForm = document.querySelector('.api-form');
if (apiForm) {
    apiForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const apiKeys = {
            googleCalendar: this.querySelector('[placeholder*="Google"]').value,
            airbnb: this.querySelector('[placeholder*="Airbnb"]').value,
            booking: this.querySelector('[placeholder*="Booking"]').value
        };
        
        localStorage.setItem('apiKeys', JSON.stringify(apiKeys));
        
        alert('Clés API sauvegardées avec succès!');
    });
}

// Load saved API keys
document.addEventListener('DOMContentLoaded', function() {
    const apiKeys = JSON.parse(localStorage.getItem('apiKeys') || '{}');
    const apiForm = document.querySelector('.api-form');
    
    if (apiForm) {
        if (apiKeys.googleCalendar) {
            apiForm.querySelector('[placeholder*="Google"]').value = apiKeys.googleCalendar;
        }
        if (apiKeys.airbnb) {
            apiForm.querySelector('[placeholder*="Airbnb"]').value = apiKeys.airbnb;
        }
        if (apiKeys.booking) {
            apiForm.querySelector('[placeholder*="Booking"]').value = apiKeys.booking;
        }
    }
});

// Messaging functionality
const conversationItems = document.querySelectorAll('.conversation-item');
conversationItems.forEach(item => {
    item.addEventListener('click', function() {
        conversationItems.forEach(i => i.classList.remove('active'));
        this.classList.add('active');
        
        const userName = this.querySelector('h4').textContent;
        const chatHeader = document.querySelector('.chat-header h3');
        if (chatHeader) {
            chatHeader.textContent = userName;
        }
    });
});

// Chat input functionality
const chatInput = document.querySelector('.chat-input input');
const chatSendBtn = document.querySelector('.chat-input button');

if (chatInput && chatSendBtn) {
    const sendMessage = () => {
        const message = chatInput.value.trim();
        if (message) {
            const chatMessages = document.querySelector('.chat-messages');
            if (chatMessages) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message sent';
                messageDiv.innerHTML = `
                    <div class="message-content">
                        <p>${message}</p>
                        <span class="message-time">${new Date().toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})}</span>
                    </div>
                `;
                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
                chatInput.value = '';
            }
        }
    };
    
    chatSendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
}

// Transaction export functionality
const exportBtn = document.querySelector('#transactions .btn-primary');
if (exportBtn) {
    exportBtn.addEventListener('click', function() {
        alert('Export des transactions en cours... (Fonctionnalité à implémenter avec backend)');
    });
}

// Search view functionality
const viewSearchBtns = document.querySelectorAll('#recherches .btn-action.view');
viewSearchBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        const searchQuery = row.cells[1].textContent;
        alert(`Détails de la recherche: ${searchQuery}\n(Fonctionnalité à implémenter avec backend)`);
    });
});

// Transaction view functionality
const viewTransactionBtns = document.querySelectorAll('#transactions .btn-action.view');
viewTransactionBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        const transactionId = row.cells[0].textContent;
        alert(`Détails de la transaction ${transactionId}\n(Fonctionnalité à implémenter avec backend)`);
    });
});
