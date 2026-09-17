<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

// Anti-spam 30 secondes
$maintenant = time();
if (isset($_SESSION['dernier_envoi'])) {
    $tempsEcoule = $maintenant - $_SESSION['dernier_envoi'];
    if ($tempsEcoule < 30) {
        $attente = 30 - $tempsEcoule;
        echo json_encode(['success' => false, 'message' => "Veuillez patienter encore {$attente} seconde(s)."]);
        exit;
    }
}

// Nettoyage des champs
$nom     = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
$email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$sujet   = filter_input(INPUT_POST, 'sujet', FILTER_SANITIZE_SPECIAL_CHARS);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$nom || !$email || !$sujet || !$message) {
    echo json_encode(['success' => false, 'message' => 'Champs invalides ou incomplets.']);
    exit;
}

$nom     = substr(trim($nom), 0, 80);
$email   = substr(trim($email), 0, 100);
$sujet   = substr(trim($sujet), 0, 100);
$message = substr(trim($message), 0, 2000);

$fichierJson = '/srv/portfolio/data/message.json';

// LECTURE / ÉCRITURE SÉCURISÉE
$fp = fopen($fichierJson, 'c+');

if ($fp && flock($fp, LOCK_EX)) {
    $taille = filesize($fichierJson);
    $donneesExistantes = [];

    if ($taille > 0) {
        $contenu = fread($fp, $taille);
        $donneesExistantes = json_decode($contenu, true);
        if (!is_array($donneesExistantes)) {
            $donneesExistantes = [];
        }
    }

    $donneesExistantes[] = [
        'id'      => time() . '_' . mt_rand(1000, 9999),
        'date'    => date('Y-m-d H:i:s'),
        'nom'     => $nom,
        'email'   => $email,
        'sujet'   => $sujet,
        'message' => $message,
        'ip'      => $_SERVER['REMOTE_ADDR'] ?? 'inconnue'
    ];

    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($donneesExistantes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    chmod($fichierJson, 0666);
    $_SESSION['dernier_envoi'] = $maintenant;

    // ÉTAPE CLÉ : Déclenchement asynchrone du script Node.js par PHP
    exec('node /home/quentin/Bots/J-Bot/commands/Utils/msgPorfolio.js > /dev/null 2>&1 &');

    echo json_encode(['success' => true]);
} else {
    if ($fp) fclose($fp);
    echo json_encode(['success' => false, 'message' => 'Erreur d\'accès au fichier.']);
}