<?php
// 1. Initialisation de la session pour l'anti-spam par IP/navigateur
session_start();

header('Content-Type: application/json');

// 2. Accepter uniquement les requêtes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

// 3. SÉCURITÉ : Anti-spam temporel (1 envoi toutes les 30 secondes max)
if (isset($_SESSION['dernier_envoi']) && (time() - $_SESSION['dernier_envoi']) < 30) {
    echo json_encode(['success' => false, 'message' => 'Veuillez patienter 30 secondes entre chaque envoi.']);
    exit;
}

// 4. Nettoyage et assainissement des données reçues
$nom     = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
$email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$sujet   = filter_input(INPUT_POST, 'sujet', FILTER_SANITIZE_SPECIAL_CHARS);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

// Vérification de la présence de tous les champs
if (!$nom || !$email || !$sujet || !$message) {
    echo json_encode(['success' => false, 'message' => 'Champs invalides ou incomplets.']);
    exit;
}

// 5. SÉCURITÉ : Limiter la longueur des champs pour éviter les abus de mémoire
$nom     = mb_substr(trim($nom), 0, 80);
$email   = mb_substr(trim($email), 0, 100);
$sujet   = mb_substr(trim($sujet), 0, 100);
$message = mb_substr(trim($message), 0, 2000);

// 6. Définition du chemin du fichier JSON
$fichierJson = '/srv/portfolio/data/message.json';
$dossier     = dirname($fichierJson);

// Si le dossier n'existe pas, tentative de création
if (!is_dir($dossier)) {
    mkdir($dossier, 0755, true);
}

// 7. SÉCURITÉ : Anti-saturation du stockage (5 Mo max pour message.json)
if (file_exists($fichierJson) && filesize($fichierJson) > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'La boîte de réception est actuellement pleine.']);
    exit;
}

// 8. Lecture et parsing des messages existants
$donneesExistantes = [];
if (file_exists($fichierJson)) {
    $contenu = file_get_contents($fichierJson);
    $donneesExistantes = json_decode($contenu, true) ?? [];
}

// 9. Création de la nouvelle entrée
$nouveauMessage = [
    'id'      => uniqid('', true),
    'date'    => date('Y-m-d H:i:s'),
    'nom'     => $nom,
    'email'   => $email,
    'sujet'   => $sujet,
    'message' => $message,
    'ip'      => $_SERVER['REMOTE_ADDR'] ?? 'inconnue'
];

// Ajout du message dans la liste
$donneesExistantes[] = $nouveauMessage;

// 10. Écriture sécurisée dans le fichier JSON
$jsonEnregistre = json_encode($donneesExistantes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

if (file_put_contents($fichierJson, $jsonEnregistre, LOCK_EX) !== false) {
    // Mettre à jour l'horodatage de l'envoi en session
    $_SESSION['dernier_envoi'] = time();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement sur le serveur.']);
}