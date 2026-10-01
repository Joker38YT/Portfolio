<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./contact.html');
    exit();
}

$cooldown = 30;
if (isset($_SESSION['dernier_envoi']) && (time() - $_SESSION['dernier_envoi']) < $cooldown) {
    $erreur = "Veuillez attendre quelques secondes avant de renvoyer un message.";
} else {
    $nom     = trim(strip_tags($_POST["nom"] ?? ''));
    $email   = trim(strip_tags($_POST["email"] ?? ''));
    $sujet   = trim(strip_tags($_POST["sujet"] ?? ''));
    $message = trim(strip_tags($_POST["message"] ?? ''));

    if  (mb_strlen($message) > 10000) {
        $erreur = "Votre message est trop long (10000 caractères max).";
    } else {
        $jsonPath = "./data/data.json";
        
        
        $jsonData = file_exists($jsonPath) ? file_get_contents($jsonPath) : '[]';
        $fichierDB = json_decode($jsonData, true);

        $nouveauMessage = [
            "date"    => date('Y-m-d H:i:s'),
            "nom"     => $nom,
            "email"   => $email,
            "sujet"   => $sujet,
            "message" => $message,
            "ip"      => $_SERVER['REMOTE_ADDR'] ?? 'Inconnu'
        ];

        $fichierDB[] = $nouveauMessage;

        $dbFinal = json_encode($fichierDB, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
        file_put_contents($jsonPath, $dbFinal, LOCK_EX);

        $_SESSION['dernier_envoi'] = time();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="refresh" content="2; URL=./contact.html">
    <title>Portfolio – Quentin Boussaguet</title>
    <link rel="stylesheet" href="./css/base.css" />
    <link rel="stylesheet" href="./css/contact.css" />
    <script src="./script/commun.js" defer></script>
</head>

<body>

    <header class="barre-haut">
    </header>

    <main class="ecran">
        <div class="contenu-ecran">
            <span class="nav-barre">
                <ul>
                    <li><a href="accueil.html">Accueil</a></li>
                    <li><a href="a_propos.html">à propos de moi</a></li>
                    <li><a href="competence.html">Compétences</a></li>
                    <li><a href="experience.html">Expériences</a></li>
                    <li><a href="projet.html">Projets Personnels</a></li>
                    <li><a class="selectionner" href="contact.html">Contact</a></li>
                </ul>
            </span>

            <div class="form-confirm">
                <?php if (isset($erreur)): ?>
                    <p style="color: #fff;"><?php echo $erreur; ?></p>
                <?php else: ?>
                    <p>MESSAGE ENVOYÉ !</p>
                <?php endif; ?>
            </div>        

        </div>
    </main>

    <footer class="panneau-controle">
        <div class="joystick joystick-bleue">
            <div class="boule boule-bleue"></div>
            <div class="tige"></div>
            <div class="socle"></div>
        </div>
        
        <div class="boutons">
            <div class="rangee-boutons">
                <div class="bouton bouton-bleu bouton-grand"></div>
                <div class="bouton bouton-bleu bouton-grand"></div>
                <div class="bouton bouton-bleu bouton-grand"></div>
            </div>
            <div class="rangee-boutons">
                <div class="bouton bouton-bleu bouton-petit"></div>
                <div class="bouton bouton-bleu bouton-petit"></div>
                <div class="bouton bouton-bleu bouton-petit"></div>
            </div>
        </div>

        <div class="panneau-centre">
            <div class="score">1UP &nbsp;·&nbsp; HI &nbsp;·&nbsp; 2UP<br />02 &nbsp; 02184 &nbsp; 08</div>
            <div class="inserer-piece">INSERT COIN</div>
            <div class="fente-piece"></div>
        </div>

        <div class="boutons">
            <div class="rangee-boutons">
                <div class="bouton bouton-rouge bouton-grand"></div>
                <div class="bouton bouton-rouge bouton-grand"></div>
                <div class="bouton bouton-rouge bouton-grand"></div>
            </div>
            <div class="rangee-boutons">
                <div class="bouton bouton-rouge bouton-petit"></div>
                <div class="bouton bouton-rouge bouton-petit"></div>
                <div class="bouton bouton-rouge bouton-petit"></div>
            </div>
        </div>

        <div class="joystick joystick-rouge">
            <div class="boule boule-rouge"></div>
            <div class="tige"></div>
            <div class="socle"></div>
        </div>
    </footer>

</body>

</html>