<?php
// Démarre une nouvelle session ou reprend une session existante
session_start();
// Inclusion du fichier de connexion à la base de données
include 'db_connect.php';

// Vérifie si la méthode de requête est POST, sinon redirige vers la page d'inscription
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: inscription.php");
    exit;
}

// Fonction pour valider et nettoyer les entrées utilisateur
function validate_input($data) {
    return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8')); // Nettoie les données pour prévenir les attaques XSS
}

// Récupération et validation des données du formulaire
$nom = validate_input($_POST['nom'] ?? ''); // Nom du client
$prenom = validate_input($_POST['prenom'] ?? ''); // Prénom du client
$telephone = validate_input($_POST['telephone'] ?? ''); // Numéro de téléphone
$email = validate_input($_POST['email'] ?? ''); // Adresse email
$mdp = $_POST['mdp'] ?? ''; // Mot de passe (non nettoyé pour permettre le hachage)
$typeDeCompte = validate_input($_POST['typeDeCompte'] ?? ''); // Type de compte

// Vérifie si tous les champs obligatoires sont remplis
if (empty($nom) || empty($prenom) || empty($email) || empty($mdp) || empty($typeDeCompte)) {
    $_SESSION['error'] = "Veuillez remplir tous les champs obligatoires.";
    header("Location: inscription.php"); // Redirige avec un message d'erreur
    exit;
}

// Vérifie si l'email est déjà utilisé dans la base de données
$checkEmail = $db->prepare("SELECT clientId FROM client WHERE email = ?");
$checkEmail->execute([$email]);
if ($checkEmail->rowCount() > 0) {
    $_SESSION['error'] = "Cet email est déjà utilisé.";
    header("Location: inscription.php"); // Redirige avec un message d'erreur
    exit;
}

// Hachage du mot de passe pour le stocker en toute sécurité
$hashedPassword = password_hash($mdp, PASSWORD_DEFAULT);

// Fonction pour générer un IBAN fictif
function generateIban() {
    $countryCode = 'FR'; // Code du pays
    $checkDigits = '00'; // Chiffres de contrôle, fixés ici pour la simplicité
    $digits = '';

    // Génération de 25 chiffres aléatoires
    for ($i = 0; $i < 25; $i++) {
        $digits .= rand(0, 9);
    }

    return $countryCode . $checkDigits . $digits; // Retourne l'IBAN complet
}

try {
    // Démarre une transaction pour assurer l'intégrité des données
    $db->beginTransaction();

    // Préparation de la requête pour insérer le client dans la base de données
    $insertClient = $db->prepare("INSERT INTO client (nom, prenom, telephone, email, mdp) VALUES (?, ?, ?, ?, ?)");
    $insertClient->execute([$nom, $prenom, $telephone, $email, $hashedPassword]); // Exécution de l'insertion

    // Récupère l'ID du client inséré
    $clientId = $db->lastInsertId();

    // Génération d'un nouvel IBAN
    $iban = generateIban();

    // Préparation de la requête pour insérer le compte bancaire dans la base de données
    $insertCompte = $db->prepare("INSERT INTO comptebancaire (clientId, numeroCompte, solde, typeDeCompte) VALUES (?, ?, ?, ?)");
    $insertCompte->execute([$clientId, $iban, 0.00, $typeDeCompte]); // Exécution de l'insertion

    // Valide la transaction
    $db->commit();

    // Message de succès pour l'utilisateur
    $_SESSION['success'] = "Inscription réussie. Vous pouvez maintenant vous connecter.";
    header("Location: inscription.php"); // Redirige vers la page d'inscription
    exit;
} catch (PDOException $e) {
    // En cas d'erreur, annule la transaction
    $db->rollBack();
    $_SESSION['error'] = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
    header("Location: inscription.php"); // Redirige avec un message d'erreur
    exit;
}
