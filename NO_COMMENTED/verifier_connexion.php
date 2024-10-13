<?php
session_start();
include 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}
function validate_input($data) {
    return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
}
$email = validate_input($_POST['email'] ?? '');
$mdp = $_POST['mdp'] ?? '';
if (empty($email) || empty($mdp)) {
    $_SESSION['error'] = "Veuillez remplir tous les champs.";
    header("Location: index.php");
    exit;
}
$query = $db->prepare("SELECT clientId, mdp FROM client WHERE email = ?");
$query->execute([$email]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($user) {
    if (password_verify($mdp, $user['mdp'])) {
        $_SESSION['clientId'] = $user['clientId'];
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['error'] = "Mot de passe incorrect.";
        header("Location: index.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Aucun compte trouvé avec cet email.";
    header("Location: index.php");
    exit;
}
