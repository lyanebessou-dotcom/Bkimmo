<?php
require_once '../config.php';

header('Content-Type: application/json');

// Récupérer tous les biens
function getAllBiens() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM biens WHERE statut = 'disponible' ORDER BY date_creation DESC");
    return $stmt->fetchAll();
}

// Récupérer un bien par ID
function getBienById($id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM biens WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Récupérer les biens par durée
function getBiensByDuree($duree) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM biens WHERE duree = ? AND statut = 'disponible' ORDER BY date_creation DESC");
    $stmt->execute([$duree]);
    return $stmt->fetchAll();
}

// Créer un nouveau bien (admin uniquement)
function createBien($data) {
    if (!isAdmin()) {
        http_response_code(403);
        return ['error' => 'Accès non autorisé'];
    }
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO biens (titre, description, adresse, ville, code_postal, surface, chambres, sdb, prix, periode, duree, image, statut, proprietaire_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([
            $data['titre'],
            $data['description'],
            $data['adresse'],
            $data['ville'],
            $data['code_postal'],
            $data['surface'],
            $data['chambres'],
            $data['sdb'],
            $data['prix'],
            $data['periode'],
            $data['duree'],
            $data['image'] ?? null,
            $data['statut'] ?? 'disponible',
            $_SESSION['user_id'] ?? null
        ]);
        return ['success' => true, 'id' => $pdo->lastInsertId()];
    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Mettre à jour un bien (admin uniquement)
function updateBien($id, $data) {
    if (!isAdmin()) {
        http_response_code(403);
        return ['error' => 'Accès non autorisé'];
    }
    
    $pdo = getDBConnection();
    $fields = [];
    $values = [];
    
    foreach ($data as $key => $value) {
        if ($key !== 'id') {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
    }
    
    $values[] = $id;
    $sql = "UPDATE biens SET " . implode(', ', $fields) . " WHERE id = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Supprimer un bien (admin uniquement)
function deleteBien($id) {
    if (!isAdmin()) {
        http_response_code(403);
        return ['error' => 'Accès non autorisé'];
    }
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM biens WHERE id = ?");
    
    try {
        $stmt->execute([$id]);
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
        if ($action === 'by_duree' && isset($_GET['duree'])) {
            echo json_encode(getBiensByDuree($_GET['duree']));
        } elseif (isset($_GET['id'])) {
            echo json_encode(getBienById($_GET['id']));
        } else {
            echo json_encode(getAllBiens());
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode(createBien($data));
        break;
        
    case 'PUT':
        if (isset($_GET['id'])) {
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode(updateBien($_GET['id'], $data));
        }
        break;
        
    case 'DELETE':
        if (isset($_GET['id'])) {
            echo json_encode(deleteBien($_GET['id']));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
}
?>
