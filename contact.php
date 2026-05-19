<?php
// contact.php

// On indique au navigateur qu'on répond au format JSON
header('Content-Type: application/json');

// Chemin vers le fichier JSON dans notre dossier sécurisé
$fichier_json = 'data/messages.json';

// Récupération des informations envoyées en JavaScript par le navigateur
$donnees_recues = json_decode(file_get_contents('php://input'), true);

if ($donnees_recues) {
    // Extraction et nettoyage rapide des champs
    $nom = $donnees_recues['nom'] ?? '';
    $email = $donnees_recues['email'] ?? '';
    $sujet = $donnees_recues['sujet'] ?? '';
    $message = $donnees_recues['message'] ?? '';

    // Si un des champs essentiels est vide, on rejette
    if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
        echo json_encode(["success" => false, "error" => "Champs incomplets."]);
        exit;
    }

    // Lecture de l'historique des messages s'il existe déjà
    $messages_existants = [];
    if (file_exists($fichier_json)) {
        $contenu_actuel = file_get_contents($fichier_json);
        $messages_existants = json_decode($contenu_actuel, true) ?? [];
    }

    // Préparation du nouveau bloc de message
    $nouveau_message = [
        "id" => time(),
        "nom" => htmlspecialchars($nom),
        "email" => htmlspecialchars($email),
        "sujet" => htmlspecialchars($sujet),
        "message" => htmlspecialchars($message),
        "date" => date('Y-m-d H:i:s')
    ];

    // On l'ajoute à la liste existante
    $messages_existants[] = $nouveau_message;

    // Enregistrement dans data/messages.json
    if (file_put_contents($fichier_json, json_encode($messages_existants, JSON_PRETTY_PRINT))) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Droits d'écriture insuffisants dans le dossier data."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Aucune donnée valide reçue."]);
}
?>