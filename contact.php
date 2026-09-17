<?php
// Désactiver les warnings parasites pour garder un JSON propre
error_reporting(0);
ini_set('display_errors', 0);

session_start();

header('Content-Type: application/json');

// 1. Uniquement les requêtes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

// 2. Vérification Anti-Spam (30 secondes)
$maintenant = time();
if (isset($_SESSION['dernier_envoi'])) {
    $tempsEcoule = $maintenant - $_SESSION['dernier_envoi'];
    if ($tempsEcoule < 30) {
        $attente = 30 - $tempsEcoule;
        echo json_encode(['success' => false, 'message' => "Veuillez patienter encore {$attente} seconde(s)."]);
        exit; // Stoppe net le script si le délai n'est pas passé
    }
}

// 3. Récupération et nettoyage des champs
$nom     = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
$email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$sujet   = filter_input(INPUT_POST, 'sujet', FILTER_SANITIZE_SPECIAL_CHARS);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$nom || !$email || !$sujet || !$message) {
    echo json_encode(['success' => false, 'message' => 'Champs invalides ou incomplets.']);
    exit;
}

// Limiter la taille des données
$nom     = substr(trim($nom), 0, 80);
$email   = substr(trim($email), 0, 100);
$sujet   = substr(trim($sujet), 0, 100);
$message = substr(trim($message), 0, 2000);

$fichierJson = '/srv/portfolio/data/message.json';

// 4. Lecture des messages existants
$donneesExistantes = [];
if (file_exists($fichierJson)) {
    $contenu = file_get_contents($fichierJson);
    $donneesExistantes = json_decode($contenu, true);
    if (!is_array($donneesExistantes)) {
        $donneesExistantes = [];
    }
}

// 5. Ajout du message
$donneesExistantes[] = [
    'id'      => time() . '_' . mt_rand(1000, 9999),
    'date'    => date('Y-m-d H:i:s'),
    'nom'     => $nom,
    'email'   => $email,
    'sujet'   => $sujet,
    'message' => $message,
    'ip'      => $_SERVER['REMOTE_ADDR'] ?? 'inconnue'
];

// 6. Écriture dans le fichier + mise à jour du timer
$jsonEnregistre = json_encode($donneesExistantes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

if (file_put_contents($fichierJson, $jsonEnregistre, LOCK_EX) !== false) {
    // On met à jour l'heure du dernier envoi SEULEMENT si l'écriture a réussi
    $_SESSION['dernier_envoi'] = $maintenant;
    
    // Permission 666 pour l'accès écriture du bot Node.js
    chmod($fichierJson, 0666);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur d\'écriture sur le serveur.']);
}