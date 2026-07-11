<?php
require_once '../config.php';

header('Content-Type: application/json');

// Connexion utilisateur
function login($email, $password) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['nom'];
        $_SESSION['user_type'] = $user['type_compte'];
        
        return [
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'email' => $user['email'],
                'type_compte' => $user['type_compte']
            ]
        ];
    }
    
    return ['success' => false, 'error' => 'Email ou mot de passe incorrect'];
}

// Inscription utilisateur
function register($data) {
    $pdo = getDBConnection();
    
    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'Cet email est déjà utilisé'];
    }
    
    // Vérifier si les mots de passe correspondent
    if ($data['password'] !== $data['confirm_password']) {
        return ['success' => false, 'error' => 'Les mots de passe ne correspondent pas'];
    }
    
    // Hasher le mot de passe
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
    // Insérer l'utilisateur
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, telephone, password, type_compte) VALUES (?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([
            $data['nom'],
            $data['email'],
            $data['telephone'],
            $hashedPassword,
            $data['type_compte'] ?? 'client'
        ]);
        
        return [
            'success' => true,
            'user_id' => $pdo->lastInsertId()
        ];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Erreur lors de l\'inscription'];
    }
}

// Déconnexion
function logout() {
    session_destroy();
    return ['success' => true];
}

// Vérifier la session
function checkSession() {
    if (isLoggedIn()) {
        return [
            'success' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'nom' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email'],
                'type_compte' => $_SESSION['user_type']
            ]
        ];
    }
    return ['success' => false, 'error' => 'Non connecté'];
}

// Router
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($action === 'login') {
            echo json_encode(login($data['email'], $data['password']));
        } elseif ($action === 'register') {
            echo json_encode(register($data));
        } elseif ($action === 'logout') {
            echo json_encode(logout());
        }
        break;
        
    case 'GET':
        if ($action === 'check') {
            echo json_encode(checkSession());
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
}
?>
