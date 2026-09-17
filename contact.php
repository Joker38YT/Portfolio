<?php
header('Content-Type: application/json');

// Vérifie que la requête est bien en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupération et nettoyage des champs du formulaire
$nom     = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
$email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$sujet   = filter_input(INPUT_POST, 'sujet', FILTER_SANITIZE_SPECIAL_CHARS);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$nom || !$email || !$sujet || !$message) {
    echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs correctement.']);
    exit;
}

// Structure de la nouvelle entrée
$nouveauMessage = [
    'id'        => uniqid(),
    'date'      => date('Y-m-d H:i:s'),
    'nom'       => $nom,
    'email'     => $email,
    'sujet'     => $sujet,
    'message'   => $message
];

// Chemin absolu vers le fichier cible
$fichierJson = '/srv/portfolio/data/message.json';

// Si le dossier n'existe pas, on tente de le créer
$dossier = dirname($fichierJson);
if (!is_dir($dossier)) {
    mkdir($dossier, 0755, true);
}

// Lecture des données existantes
$donneesExistantes = [];
if (file_exists($fichierJson)) {
    $contenu = file_get_contents($fichierJson);
    $donneesExistantes = json_decode($contenu, true) ?? [];
}

// Ajout du message
$donneesExistantes[] = $nouveauMessage;

// Enregistrement dans message.json
$json = json_encode($donneesExistantes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$resultat = @file_put_contents($fichierJson, $json);

if ($resultat !== false) {
    echo json_encode(['success' => true]);
} else {
    $erreur = error_get_last();
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur PHP : ' . ($erreur['message'] ?? 'Inconnue')
    ]);
}