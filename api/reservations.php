<?php
require_once '../config.php';

header('Content-Type: application/json');

// Récupérer toutes les réservations (admin)
function getAllReservations() {
    if (!isAdmin()) {
        http_response_code(403);
        return ['error' => 'Accès non autorisé'];
    }
    
    $pdo = getDBConnection();
    $stmt = $pdo->query("
        SELECT r.*, u.nom as client_nom, u.email as client_email, b.titre as bien_titre 
        FROM reservations r 
        JOIN utilisateurs u ON r.utilisateur_id = u.id 
        JOIN biens b ON r.bien_id = b.id 
        ORDER BY r.date_creation DESC
    ");
    return $stmt->fetchAll();
}

// Récupérer les réservations d'un utilisateur
function getUserReservations($userId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT r.*, b.titre as bien_titre, b.image as bien_image 
        FROM reservations r 
        JOIN biens b ON r.bien_id = b.id 
        WHERE r.utilisateur_id = ? 
        ORDER BY r.date_creation DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Créer une réservation
function createReservation($data) {
    if (!isLoggedIn()) {
        http_response_code(401);
        return ['error' => 'Non connecté'];
    }
    
    $pdo = getDBConnection();
    
    // Vérifier les disponibilités
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM reservations 
        WHERE bien_id = ? 
        AND statut IN ('en_attente', 'confirmee')
        AND ((date_arrivee <= ? AND date_depart >= ?) OR (date_arrivee <= ? AND date_depart >= ?))
    ");
    $stmt->execute([
        $data['bien_id'],
        $data['date_arrivee'],
        $data['date_arrivee'],
        $data['date_depart'],
        $data['date_depart']
    ]);
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        return ['success' => false, 'error' => 'Le bien n\'est pas disponible pour ces dates'];
    }
    
    // Calculer le prix total
    $stmt = $pdo->prepare("SELECT prix, periode FROM biens WHERE id = ?");
    $stmt->execute([$data['bien_id']]);
    $bien = $stmt->fetch();
    
    if (!$bien) {
        return ['success' => false, 'error' => 'Bien non trouvé'];
    }
    
    $dateArrivee = new DateTime($data['date_arrivee']);
    $dateDepart = new DateTime($data['date_depart']);
    $interval = $dateArrivee->diff($dateDepart);
    $jours = $interval->days;
    
    if ($bien['periode'] === 'nuit') {
        $prixTotal = $bien['prix'] * $jours;
    } elseif ($bien['periode'] === 'semaine') {
        $prixTotal = $bien['prix'] * ceil($jours / 7);
    } else {
        $prixTotal = $bien['prix'] * ceil($jours / 30);
    }
    
    // Créer la réservation
    $stmt = $pdo->prepare("
        INSERT INTO reservations (utilisateur_id, bien_id, date_arrivee, date_depart, nombre_personnes, message, prix_total, statut) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'en_attente')
    ");
    
    try {
        $stmt->execute([
            $_SESSION['user_id'],
            $data['bien_id'],
            $data['date_arrivee'],
            $data['date_depart'],
            $data['nombre_personnes'] ?? 1,
            $data['message'] ?? null,
            $prixTotal
        ]);
        
        return [
            'success' => true,
            'id' => $pdo->lastInsertId(),
            'prix_total' => $prixTotal
        ];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Erreur lors de la création de la réservation'];
    }
}

// Mettre à jour le statut d'une réservation (admin)
function updateReservationStatus($id, $statut) {
    if (!isAdmin()) {
        http_response_code(403);
        return ['error' => 'Accès non autorisé'];
    }
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE reservations SET statut = ? WHERE id = ?");
    
    try {
        $stmt->execute([$statut, $id]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Router
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        if ($action === 'user' && isLoggedIn()) {
            echo json_encode(getUserReservations($_SESSION['user_id']));
        } elseif ($action === 'all') {
            echo json_encode(getAllReservations());
        } elseif (isset($_GET['id'])) {
            // Implémenter getReservationById si nécessaire
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode(createReservation($data));
        break;
        
    case 'PUT':
        if (isset($_GET['id']) && isset($_GET['statut'])) {
            echo json_encode(updateReservationStatus($_GET['id'], $_GET['statut']));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
}
?>
