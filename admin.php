<?php
require_once 'config.php';

// Vérifier que l'utilisateur est admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Récupérer les statistiques pour le tableau de bord
$pdo = getDBConnection();

// Nombre de biens
$stmt = $pdo->query("SELECT COUNT(*) as count FROM biens WHERE statut = 'disponible'");
$statBiens = $stmt->fetch()['count'];

// Nombre de réservations ce mois
$stmt = $pdo->query("SELECT COUNT(*) as count FROM reservations WHERE MONTH(date_creation) = MONTH(CURRENT_DATE())");
$statReservations = $stmt->fetch()['count'];

// Nombre d'utilisateurs
$stmt = $pdo->query("SELECT COUNT(*) as count FROM utilisateurs");
$statUsers = $stmt->fetch()['count'];

// Revenus ce mois
$stmt = $pdo->query("SELECT SUM(prix_total) as total FROM reservations WHERE statut = 'confirmee' AND MONTH(date_creation) = MONTH(CURRENT_DATE())");
$statRevenus = $stmt->fetch()['total'] ?? 0;

// Demandes de services en attente
$stmt = $pdo->query("SELECT COUNT(*) as count FROM demandes_services WHERE statut = 'en_attente'");
$statServices = $stmt->fetch()['count'];

// Note moyenne
$stmt = $pdo->query("SELECT AVG(note) as avg_note FROM avis WHERE statut = 'publie'");
$statAvis = $stmt->fetch()['avg_note'] ? round($stmt->fetch()['avg_note'], 1) : 0;

// Récupérer les biens pour le tableau
$stmt = $pdo->query("SELECT * FROM biens ORDER BY date_creation DESC LIMIT 10");
$biens = $stmt->fetchAll();

// Récupérer les réservations récentes
$stmt = $pdo->query("
    SELECT r.*, u.nom as client_nom, b.titre as bien_titre 
    FROM reservations r 
    JOIN utilisateurs u ON r.utilisateur_id = u.id 
    JOIN biens b ON r.bien_id = b.id 
    ORDER BY r.date_creation DESC 
    LIMIT 10
");
$reservations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Bkimmo</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header admin-header">
        <div class="container">
            <nav class="navbar">
                <div class="logo">
                    <a href="index.php">
                        <img src="Design sans titre (4) (1).svg" alt="Bkimmo">
                        <span>Bkimmo Admin</span>
                    </a>
                </div>
                <div class="header-actions">
                    <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode">
                        <i class="fas fa-moon"></i>
                    </button>
                    <div class="user-menu">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
                        <a href="logout.php" class="btn-logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Admin Dashboard -->
    <section class="admin-dashboard">
        <div class="container">
            <div class="admin-layout">
                <!-- Sidebar -->
                <aside class="admin-sidebar">
                    <nav class="sidebar-nav">
                        <ul>
                            <li class="active">
                                <a href="#dashboard" class="sidebar-link">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Tableau de bord</span>
                                </a>
                            </li>
                            <li>
                                <a href="#biens" class="sidebar-link">
                                    <i class="fas fa-building"></i>
                                    <span>Biens meublés</span>
                                </a>
                            </li>
                            <li>
                                <a href="#reservations" class="sidebar-link">
                                    <i class="fas fa-calendar-check"></i>
                                    <span>Réservations</span>
                                </a>
                            </li>
                            <li>
                                <a href="#utilisateurs" class="sidebar-link">
                                    <i class="fas fa-users"></i>
                                    <span>Utilisateurs</span>
                                </a>
                            </li>
                            <li>
                                <a href="#services" class="sidebar-link">
                                    <i class="fas fa-concierge-bell"></i>
                                    <span>Demandes services</span>
                                </a>
                            </li>
                            <li>
                                <a href="#messagerie" class="sidebar-link">
                                    <i class="fas fa-comments"></i>
                                    <span>Messagerie</span>
                                </a>
                            </li>
                            <li>
                                <a href="#transactions" class="sidebar-link">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Transactions</span>
                                </a>
                            </li>
                            <li>
                                <a href="#recherches" class="sidebar-link">
                                    <i class="fas fa-search"></i>
                                    <span>Recherches</span>
                                </a>
                            </li>
                            <li>
                                <a href="#calendrier" class="sidebar-link">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Calendrier</span>
                                </a>
                            </li>
                            <li>
                                <a href="#api" class="sidebar-link">
                                    <i class="fas fa-plug"></i>
                                    <span>Intégrations API</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </aside>

                <!-- Main Content -->
                <main class="admin-main">
                    <!-- Dashboard Section -->
                    <section id="dashboard" class="admin-section active">
                        <h2>Tableau de bord</h2>
                        
                        <!-- Stats Cards -->
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 id="statBiens"><?php echo $statBiens; ?></h3>
                                    <p>Biens meublés</p>
                                    <span class="stat-trend positive">+2 ce mois</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 id="statReservations"><?php echo $statReservations; ?></h3>
                                    <p>Réservations ce mois</p>
                                    <span class="stat-trend positive">+15% vs mois dernier</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 id="statUsers"><?php echo $statUsers; ?></h3>
                                    <p>Utilisateurs</p>
                                    <span class="stat-trend positive">+23 ce mois</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-euro-sign"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 id="statRevenus"><?php echo number_format($statRevenus, 0, '', ' '); ?>€</h3>
                                    <p>Revenus ce mois</p>
                                    <span class="stat-trend positive">+22% vs mois dernier</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 id="statServices"><?php echo $statServices; ?></h3>
                                    <p>Demandes services</p>
                                    <span class="stat-trend neutral">En attente</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 id="statAvis"><?php echo $statAvis; ?></h3>
                                    <p>Note moyenne</p>
                                    <span class="stat-trend positive">Basé sur 45 avis</span>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Section -->
                        <div class="charts-grid">
                            <div class="chart-card">
                                <h3>Revenus mensuels</h3>
                                <div class="chart-container">
                                    <div class="bar-chart">
                                        <div class="bar" style="height: 60%;">
                                            <span class="bar-label">Jan</span>
                                        </div>
                                        <div class="bar" style="height: 75%;">
                                            <span class="bar-label">Fév</span>
                                        </div>
                                        <div class="bar" style="height: 45%;">
                                            <span class="bar-label">Mar</span>
                                        </div>
                                        <div class="bar" style="height: 90%;">
                                            <span class="bar-label">Avr</span>
                                        </div>
                                        <div class="bar active" style="height: 85%;">
                                            <span class="bar-label">Mai</span>
                                        </div>
                                        <div class="bar" style="height: 70%;">
                                            <span class="bar-label">Juin</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="chart-card">
                                <h3>Répartition des réservations</h3>
                                <div class="chart-container">
                                    <div class="pie-chart">
                                        <div class="pie-segment" style="--percentage: 45; --color: #3b82f6;">
                                            <span class="pie-label">Nuit</span>
                                        </div>
                                        <div class="pie-segment" style="--percentage: 30; --color: #f59e0b;">
                                            <span class="pie-label">Semaine</span>
                                        </div>
                                        <div class="pie-segment" style="--percentage: 25; --color: #10b981;">
                                            <span class="pie-label">Mois</span>
                                        </div>
                                    </div>
                                    <div class="pie-legend">
                                        <div class="legend-item">
                                            <span class="legend-color" style="background: #3b82f6;"></span>
                                            <span>Nuit (45%)</span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-color" style="background: #f59e0b;"></span>
                                            <span>Semaine (30%)</span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-color" style="background: #10b981;"></span>
                                            <span>Mois (25%)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Indicators -->
                        <div class="performance-section">
                            <h3>Indicateurs de performance</h3>
                            <div class="performance-grid">
                                <div class="performance-item">
                                    <div class="performance-header">
                                        <h4>Taux d'occupation</h4>
                                        <span class="performance-value">78%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 78%;"></div>
                                    </div>
                                </div>
                                <div class="performance-item">
                                    <div class="performance-header">
                                        <h4>Taux de conversion</h4>
                                        <span class="performance-value">32%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 32%;"></div>
                                    </div>
                                </div>
                                <div class="performance-item">
                                    <div class="performance-header">
                                        <h4>Satisfaction client</h4>
                                        <span class="performance-value">96%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 96%;"></div>
                                    </div>
                                </div>
                                <div class="performance-item">
                                    <div class="performance-header">
                                        <h4>Réponse moyenne</h4>
                                        <span class="performance-value">2h</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 85%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="recent-activity">
                            <h3>Activité récente</h3>
                            <div class="activity-list">
                                <div class="activity-item">
                                    <div class="activity-icon success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h4>Nouvelle réservation</h4>
                                        <p>Jean Dupont - 340€</p>
                                        <span class="activity-time">Il y a 2h</span>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-icon info">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h4>Nouvel utilisateur</h4>
                                        <p>Marie Martin</p>
                                        <span class="activity-time">Il y a 5h</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Performing Properties -->
                        <div class="top-properties">
                            <h3>Biens performants</h3>
                            <div class="properties-list">
                                <div class="property-item">
                                    <div class="property-image">
                                        <img src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=100" alt="Bien">
                                    </div>
                                    <div class="property-info">
                                        <h4>Appartement Centre-Ville</h4>
                                        <p>28 rés. | 2,380€</p>
                                    </div>
                                    <div class="property-rating">
                                        <i class="fas fa-star"></i>
                                        <span>4.9</span>
                                    </div>
                                </div>
                                <div class="property-item">
                                    <div class="property-image">
                                        <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=100" alt="Bien">
                                    </div>
                                    <div class="property-info">
                                        <h4>Studio Quartier Latin</h4>
                                        <p>22 rés. | 1,430€</p>
                                    </div>
                                    <div class="property-rating">
                                        <i class="fas fa-star"></i>
                                        <span>4.7</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Biens Section -->
                    <section id="biens" class="admin-section">
                        <div class="section-header">
                            <h2>Gestion des biens meublés</h2>
                            <button class="btn btn-primary" id="addBienBtn">
                                <i class="fas fa-plus"></i>
                                Ajouter un bien
                            </button>
                        </div>
                        
                        <div class="biens-table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Titre</th>
                                        <th>Adresse</th>
                                        <th>Prix</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="biensTableBody">
                                    <tr>
                                        <td><img src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=100" alt="Bien"></td>
                                        <td>Appartement Centre-Ville</td>
                                        <td>15 Rue de la République, Paris</td>
                                        <td>85€/nuit</td>
                                        <td><span class="status-badge available">Disponible</span></td>
                                        <td>
                                            <button class="btn-action edit"><i class="fas fa-edit"></i></button>
                                            <button class="btn-action delete"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=100" alt="Bien"></td>
                                        <td>Studio Quartier Latin</td>
                                        <td>42 Boulevard Saint-Michel, Paris</td>
                                        <td>65€/nuit</td>
                                        <td><span class="status-badge reserved">Réservé</span></td>
                                        <td>
                                            <button class="btn-action edit"><i class="fas fa-edit"></i></button>
                                            <button class="btn-action delete"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Reservations Section -->
                    <section id="reservations" class="admin-section">
                        <div class="section-header">
                            <h2>Gestion des réservations</h2>
                        </div>
                        
                        <div class="reservations-table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Client</th>
                                        <th>Bien</th>
                                        <th>Date arrivée</th>
                                        <th>Date départ</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#RES001</td>
                                        <td>Jean Dupont</td>
                                        <td>Appartement Centre-Ville</td>
                                        <td>15/05/2024</td>
                                        <td>20/05/2024</td>
                                        <td><span class="status-badge confirmed">Confirmée</span></td>
                                        <td>
                                            <button class="btn-action view"><i class="fas fa-eye"></i></button>
                                            <button class="btn-action edit"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Users Section -->
                    <section id="utilisateurs" class="admin-section">
                        <div class="section-header">
                            <h2>Gestion des utilisateurs</h2>
                        </div>
                        
                        <div class="users-table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Type</th>
                                        <th>Date inscription</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Jean Dupont</td>
                                        <td>jean.dupont@email.com</td>
                                        <td>+33 6 12 34 56 78</td>
                                        <td>Client</td>
                                        <td>10/04/2024</td>
                                        <td>
                                            <button class="btn-action view"><i class="fas fa-eye"></i></button>
                                            <button class="btn-action delete"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Services Section -->
                    <section id="services" class="admin-section">
                        <div class="section-header">
                            <h2>Demandes de services immobiliers</h2>
                        </div>
                        
                        <div class="services-list">
                            <div class="service-request-card">
                                <div class="service-header">
                                    <h3>Demande de gestion locative</h3>
                                    <span class="status-badge pending">En attente</span>
                                </div>
                                <div class="service-details">
                                    <p><strong>Client:</strong> Pierre Martin</p>
                                    <p><strong>Email:</strong> pierre.martin@email.com</p>
                                    <p><strong>Type de service:</strong> Gestion locative</p>
                                    <p><strong>Description:</strong> Je souhaite gérer mon appartement situé à Paris 11ème</p>
                                    <p><strong>Date:</strong> 20/04/2024</p>
                                </div>
                                <div class="service-actions">
                                    <button class="btn btn-primary">Contacter</button>
                                    <button class="btn btn-secondary">Archiver</button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Messagerie Section -->
                    <section id="messagerie" class="admin-section">
                        <div class="section-header">
                            <h2>Messagerie</h2>
                        </div>
                        
                        <div class="messaging-container">
                            <div class="conversations-list">
                                <div class="conversation-item active">
                                    <div class="conversation-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="conversation-info">
                                        <h4>Jean Dupont</h4>
                                        <p>Dernier message: Quand puis-je visiter l'appartement ?</p>
                                        <span class="conversation-time">Il y a 2h</span>
                                    </div>
                                    <span class="unread-badge">2</span>
                                </div>
                                <div class="conversation-item">
                                    <div class="conversation-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="conversation-info">
                                        <h4>Marie Martin</h4>
                                        <p>Dernier message: Merci pour votre réponse</p>
                                        <span class="conversation-time">Hier</span>
                                    </div>
                                </div>
                                <div class="conversation-item">
                                    <div class="conversation-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="conversation-info">
                                        <h4>Pierre Bernard</h4>
                                        <p>Dernier message: Je suis intéressé par le studio</p>
                                        <span class="conversation-time">Il y a 3 jours</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="chat-window">
                                <div class="chat-header">
                                    <h3>Jean Dupont</h3>
                                    <span class="chat-status">En ligne</span>
                                </div>
                                <div class="chat-messages">
                                    <div class="message received">
                                        <div class="message-content">
                                            <p>Bonjour, je suis intéressé par l'appartement Centre-Ville. Quand puis-je le visiter ?</p>
                                            <span class="message-time">14:30</span>
                                        </div>
                                    </div>
                                    <div class="message sent">
                                        <div class="message-content">
                                            <p>Bonjour Jean, vous pouvez visiter l'appartement demain à 10h ou 14h. Quelle heure vous convient le mieux ?</p>
                                            <span class="message-time">15:00</span>
                                        </div>
                                    </div>
                                    <div class="message received">
                                        <div class="message-content">
                                            <p>10h serait parfait pour moi. Merci !</p>
                                            <span class="message-time">15:15</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="chat-input">
                                    <input type="text" placeholder="Écrivez votre message...">
                                    <button class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Transactions Section -->
                    <section id="transactions" class="admin-section">
                        <div class="section-header">
                            <h2>Transactions</h2>
                            <button class="btn btn-primary">
                                <i class="fas fa-download"></i>
                                Exporter
                            </button>
                        </div>
                        
                        <div class="transactions-table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Client</th>
                                        <th>Type</th>
                                        <th>Montant</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#TRX001</td>
                                        <td>Jean Dupont</td>
                                        <td>Réservation</td>
                                        <td>340€</td>
                                        <td>15/04/2024</td>
                                        <td><span class="status-badge confirmed">Payée</span></td>
                                        <td>
                                            <button class="btn-action view"><i class="fas fa-eye"></i></button>
                                            <button class="btn-action edit"><i class="fas fa-download"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#TRX002</td>
                                        <td>Marie Martin</td>
                                        <td>Réservation</td>
                                        <td>650€</td>
                                        <td>18/04/2024</td>
                                        <td><span class="status-badge pending">En attente</span></td>
                                        <td>
                                            <button class="btn-action view"><i class="fas fa-eye"></i></button>
                                            <button class="btn-action edit"><i class="fas fa-check"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#TRX003</td>
                                        <td>Pierre Bernard</td>
                                        <td>Services</td>
                                        <td>150€</td>
                                        <td>20/04/2024</td>
                                        <td><span class="status-badge confirmed">Payée</span></td>
                                        <td>
                                            <button class="btn-action view"><i class="fas fa-eye"></i></button>
                                            <button class="btn-action edit"><i class="fas fa-download"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Recherches Section -->
                    <section id="recherches" class="admin-section">
                        <div class="section-header">
                            <h2>Recherches effectuées</h2>
                        </div>
                        
                        <div class="searches-table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Recherche</th>
                                        <th>Filtres</th>
                                        <th>Résultats</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Jean Dupont</td>
                                        <td>Appartement Paris</td>
                                        <td>1-2 chambres, 50-100€/nuit</td>
                                        <td>12 résultats</td>
                                        <td>15/04/2024</td>
                                        <td>
                                            <button class="btn-action view"><i class="fas fa-eye"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Marie Martin</td>
                                        <td>Studio Lyon</td>
                                        <td>Moins de 50€/nuit</td>
                                        <td>8 résultats</td>
                                        <td>18/04/2024</td>
                                        <td>
                                            <button class="btn-action view"><i class="fas fa-eye"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Pierre Bernard</td>
                                        <td>Appartement meublé Marseille</td>
                                        <td>2 chambres, semaine</td>
                                        <td>5 résultats</td>
                                        <td>20/04/2024</td>
                                        <td>
                                            <button class="btn-action view"><i class="fas fa-eye"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Calendar Section -->
                    <section id="calendrier" class="admin-section">
                        <div class="section-header">
                            <h2>Calendrier des disponibilités</h2>
                            <button class="btn btn-secondary">
                                <i class="fas fa-sync"></i>
                                Synchroniser
                            </button>
                        </div>
                        
                        <div class="calendar-integration">
                            <div class="integration-card">
                                <div class="integration-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="integration-content">
                                    <h3>Google Calendar</h3>
                                    <p>Connectez votre calendrier Google pour synchroniser les disponibilités</p>
                                    <button class="btn btn-primary">Connecter</button>
                                </div>
                            </div>
                            <div class="integration-card">
                                <div class="integration-icon">
                                    <i class="fab fa-airbnb"></i>
                                </div>
                                <div class="integration-content">
                                    <h3>Airbnb</h3>
                                    <p>Synchronisez vos disponibilités avec Airbnb</p>
                                    <button class="btn btn-primary">Connecter</button>
                                </div>
                            </div>
                            <div class="integration-card">
                                <div class="integration-icon">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div class="integration-content">
                                    <h3>Booking.com</h3>
                                    <p>Synchronisez vos disponibilités avec Booking.com</p>
                                    <button class="btn btn-primary">Connecter</button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- API Section -->
                    <section id="api" class="admin-section">
                        <div class="section-header">
                            <h2>Intégrations API</h2>
                        </div>
                        
                        <div class="api-settings">
                            <div class="api-card">
                                <h3>Configuration API</h3>
                                <p>Configurez les clés API pour les intégrations externes</p>
                                <form class="api-form">
                                    <div class="form-group">
                                        <label>Google Calendar API Key</label>
                                        <input type="text" placeholder="Entrez votre clé API">
                                    </div>
                                    <div class="form-group">
                                        <label>Airbnb API Key</label>
                                        <input type="text" placeholder="Entrez votre clé API">
                                    </div>
                                    <div class="form-group">
                                        <label>Booking.com API Key</label>
                                        <input type="text" placeholder="Entrez votre clé API">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                                </form>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </section>

    <!-- Modal for Adding/Editing Bien -->
    <div class="modal" id="bienModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Ajouter un bien</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <form class="modal-form" id="bienForm">
                <div class="form-group">
                    <label>Titre *</label>
                    <input type="text" name="titre" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Adresse *</label>
                        <input type="text" name="adresse" required>
                    </div>
                    <div class="form-group">
                        <label>Ville *</label>
                        <input type="text" name="ville" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Code postal *</label>
                        <input type="text" name="code_postal" required>
                    </div>
                    <div class="form-group">
                        <label>Surface (m²) *</label>
                        <input type="number" name="surface" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Chambres *</label>
                        <input type="number" name="chambres" required>
                    </div>
                    <div class="form-group">
                        <label>Salles de bain *</label>
                        <input type="number" name="sdb" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Prix *</label>
                        <input type="number" name="prix" required>
                    </div>
                    <div class="form-group">
                        <label>Période *</label>
                        <select name="periode" required>
                            <option value="nuit">Nuit</option>
                            <option value="semaine">Semaine</option>
                            <option value="mois">Mois</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="url" name="image">
                </div>
                <div class="form-group">
                    <label>Statut</label>
                    <select name="statut">
                        <option value="disponible">Disponible</option>
                        <option value="reserve">Réservé</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" id="cancelModal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script src="js/admin.js"></script>
</body>
</html>
