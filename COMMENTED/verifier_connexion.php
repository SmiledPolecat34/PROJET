<?php
// Démarre ou reprend une session existante
session_start();
// Inclusion du fichier de connexion à la base de données
include 'db_connect.php';

// Vérifie si la requête HTTP est une soumission de formulaire POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Si ce n'est pas une soumission POST, redirige vers la page d'accueil
    header("Location: index.php");
    exit; // Termine le script
}

// Fonction pour valider et échapper les entrées utilisateur
function validate_input($data) {
    return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8')); // Retire les espaces et échappe les caractères spéciaux
}

// Récupération et validation des données du formulaire
$email = validate_input($_POST['email'] ?? ''); // Récupère l'email
$mdp = $_POST['mdp'] ?? ''; // Récupère le mot de passe

// Vérifie si les champs email ou mot de passe sont vides
if (empty($email) || empty($mdp)) {
    $_SESSION['error'] = "Veuillez remplir tous les champs."; // Stocke un message d'erreur dans la session
    header("Location: index.php"); // Redirige vers la page d'accueil
    exit; // Termine le script
}

// Préparation d'une requête pour récupérer les informations de l'utilisateur dans la base de données
$query = $db->prepare("SELECT clientId, mdp FROM client WHERE email = ?");
$query->execute([$email]); // Exécution de la requête avec l'email fourni
$user = $query->fetch(PDO::FETCH_ASSOC); // Récupère les informations de l'utilisateur

// Vérifie si l'utilisateur existe
if ($user) {
    // Vérifie si le mot de passe fourni correspond au mot de passe stocké dans la base de données
    if (password_verify($mdp, $user['mdp'])) {
        $_SESSION['clientId'] = $user['clientId']; // Stocke l'ID du client dans la session
        header("Location: dashboard.php"); // Redirige vers le tableau de bord
        exit; // Termine le script
    } else {
        $_SESSION['error'] = "Mot de passe incorrect."; // Stocke un message d'erreur si le mot de passe est incorrect
        header("Location: index.php"); // Redirige vers la page d'accueil
        exit; // Termine le script
    }
} else {
    $_SESSION['error'] = "Aucun compte trouvé avec cet email."; // Stocke un message d'erreur si aucun compte n'est trouvé
    header("Location: index.php"); // Redirige vers la page d'accueil
    exit; // Termine le script
}
