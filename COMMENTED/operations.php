<?php
// Inclusion de la barre de navigation pour la page de gestion de compte
include 'navbarHome.php';
// Démarre ou reprend une session existante
session_start();
// Inclusion du fichier de connexion à la base de données
include('db_connect.php');

// Vérifie si l'ID du client est défini dans la session
if (!isset($_SESSION['clientId'])) {
    // Si l'utilisateur n'est pas connecté, redirige vers la page de connexion
    header("Location: connexion.php");
    exit; // Termine le script après la redirection
}

// Récupération de l'ID du client depuis la session
$clientId = $_SESSION['clientId'];
// Récupération de l'ID du compte à partir des paramètres de l'URL, avec une valeur par défaut de null
$compteId = $_GET['compteId'] ?? null;

// Vérifie si l'ID du compte est défini
if ($compteId === null) {
    echo "Erreur : compteId non défini."; // Affiche un message d'erreur si compteId est manquant
    exit; // Termine le script
}

// Prépare une requête pour récupérer les détails du compte
$query = $db->prepare("SELECT * FROM comptebancaire WHERE compteId = ? AND clientId = ?");
$query->execute([$compteId, $clientId]); // Exécute la requête avec les paramètres fournis
$compte = $query->fetch(PDO::FETCH_ASSOC); // Récupère le compte sous forme de tableau associatif

// Vérifie si le compte existe
if (!$compte) {
    echo "Compte non trouvé ou vous n'avez pas les droits d'accès."; // Message d'erreur si le compte n'existe pas
    exit; // Termine le script
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> <!-- Définit l'encodage de caractères en UTF-8 -->
    <title>Gestion du Compte</title> <!-- Titre de la page -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet"> <!-- Lien vers Bootstrap -->
</head>
<body>
    <div class="container mt-5"> <!-- Conteneur principal pour le contenu de la page -->
        <h2 class="mb-4">Gestion du Compte <?php echo htmlspecialchars($compte['numeroCompte']); ?></h2> <!-- Titre pour la gestion du compte -->
        <div class="card mb-4"> <!-- Card Bootstrap pour afficher les détails du compte -->
            <div class="card-body">
                <p class="card-text"><strong>Type de compte:</strong> <?php echo htmlspecialchars($compte['typeDeCompte']); ?></p> <!-- Affichage du type de compte -->
                <p class="card-text"><strong>Solde actuel:</strong> <?php echo htmlspecialchars(number_format($compte['solde'], 2)); ?> €</p> <!-- Affichage du solde actuel formaté -->
            </div>
        </div>
        <div role="group" aria-label="Opérations"> <!-- Regroupe les boutons d'opérations -->
            <a href="depot.php?compteId=<?php echo $compteId; ?>" class="btn btn-success">Dépôt</a> <!-- Lien vers la page de dépôt -->
            <a href="retrait.php?compteId=<?php echo $compteId; ?>" class="btn btn-warning">Retrait</a> <!-- Lien vers la page de retrait -->
            <a href="virement.php?compteId=<?php echo $compteId; ?>" class="btn btn-info">Virement</a> <!-- Lien vers la page de virement -->
            <a href="dashboard.php" class="btn btn-secondary">Retour au tableau de bord</a> <!-- Lien vers le tableau de bord -->
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script> <!-- Script pour jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script> <!-- Script pour Popper.js -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> <!-- Script pour Bootstrap -->
</body>
</html>
<?php include 'footer.php'; ?> <!-- Inclusion du pied de page -->
