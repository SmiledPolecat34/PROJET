<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: inscription.php");
    exit;
}

function validate_input($data) {
    return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
}

$nom = validate_input($_POST['nom'] ?? '');
$prenom = validate_input($_POST['prenom'] ?? '');
$telephone = validate_input($_POST['telephone'] ?? '');
$email = validate_input($_POST['email'] ?? '');
$mdp = $_POST['mdp'] ?? '';
$typeDeCompte = validate_input($_POST['typeDeCompte'] ?? '');

if (empty($nom) || empty($prenom) || empty($email) || empty($mdp) || empty($typeDeCompte)) {
    $_SESSION['error'] = "Veuillez remplir tous les champs obligatoires.";
    header("Location: inscription.php");
    exit;
}

$checkEmail = $db->prepare("SELECT clientId FROM client WHERE email = ?");
$checkEmail->execute([$email]);
if ($checkEmail->rowCount() > 0) {
    $_SESSION['error'] = "Cet email est déjà utilisé.";
    header("Location: inscription.php");
    exit;
}

$hashedPassword = password_hash($mdp, PASSWORD_DEFAULT);

function generateIban() {
    $countryCode = 'FR';
    $checkDigits = '00';
    $digits = '';


    for ($i = 0; $i < 25; $i++) {
        $digits .= rand(0, 9);
    }

    return $countryCode . $checkDigits . $digits;
}

try {

    $db->beginTransaction();


    $insertClient = $db->prepare("INSERT INTO client (nom, prenom, telephone, email, mdp) VALUES (?, ?, ?, ?, ?)");
    $insertClient->execute([$nom, $prenom, $telephone, $email, $hashedPassword]);


    $clientId = $db->lastInsertId();


    $iban = generateIban();


    $insertCompte = $db->prepare("INSERT INTO comptebancaire (clientId, numeroCompte, solde, typeDeCompte) VALUES (?, ?, ?, ?)");
    $insertCompte->execute([$clientId, $iban, 0.00, $typeDeCompte]);


    $db->commit();

    $_SESSION['success'] = "Inscription réussie. Vous pouvez maintenant vous connecter.";
    header("Location: inscription.php");
    exit;
} catch (PDOException $e) {

    $db->rollBack();
    $_SESSION['error'] = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
    header("Location: inscription.php");
    exit;
}
