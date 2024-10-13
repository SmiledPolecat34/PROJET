<?php
// Inclusion du fichier de navigation pour la page d'accueil
include 'navbarHome.php';
// Démarre une nouvelle session ou reprend une session existante
session_start();
// Inclusion du fichier de connexion à la base de données
include('db_connect.php');

// Vérifie si l'ID du client est défini dans la session, sinon redirige vers la page de connexion
if (!isset($_SESSION['clientId'])) {
    header("Location: connexion.php");
    exit; // Terminer le script si l'utilisateur n'est pas connecté
}

// Récupération de l'ID du client depuis la session
$clientId = $_SESSION['clientId'];

// Préparation et exécution d'une requête pour récupérer les informations du client
$query = $db->prepare("SELECT * FROM client WHERE clientId = ?");
$query->execute([$clientId]);
$client = $query->fetch(PDO::FETCH_ASSOC);

// Préparation et exécution d'une requête pour récupérer les comptes bancaires du client
$query = $db->prepare("SELECT * FROM comptebancaire WHERE clientId = ?");
$query->execute([$clientId]);
$comptes = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet"> <!-- Lien vers Bootstrap -->
</head>
<body>
    <div class="container mt-5">
        <h2>Bienvenue, <?php echo htmlspecialchars($client['prenom']); ?>!</h2> <!-- Affiche le prénom du client -->
        <div class="d-flex justify-content-between"> <!-- Utilisation de Bootstrap pour aligner les boutons -->
            <a href="deconnexion.php" class="btn btn-danger mr-2">Déconnexion</a> <!-- Lien pour se déconnecter -->
            <a href="historique_transferts.php" class="btn btn-info mr-2">Historique des Transferts</a> <!-- Lien vers l'historique des transferts -->
            <a href="transferts_recus.php" class="btn btn-warning mr-2">Transferts Reçus</a> <!-- Lien vers les transferts reçus -->
            <a href="liste_beneficiaires.php" class="btn btn-secondary">Voir la Liste des Bénéficiaires</a> <!-- Lien vers la liste des bénéficiaires -->
        </div>

        <h3 class="mt-4">Vos Comptes Bancaires</h3>
        <ul class="list-group mb-4"> <!-- Liste des comptes bancaires -->
            <?php foreach ($comptes as $compte): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center"> <!-- Élément de la liste pour chaque compte -->
                    <div>
                        <?php echo htmlspecialchars(ucfirst($compte['typeDeCompte'])); ?> - IBAN: <?php echo htmlspecialchars($compte['numeroCompte']); ?> - Solde: <?php echo htmlspecialchars(number_format($compte['solde'], 2)); ?> € <!-- Affiche les détails du compte -->
                    </div>
                    <a href="operations.php?compteId=<?php echo htmlspecialchars($compte['compteId']); ?>" class="btn btn-primary">Gérer</a> <!-- Lien pour gérer le compte -->
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="mt-3">
            <a href="ajouter_compte.php" class="btn btn-success">Ajouter un nouveau compte</a> <!-- Lien pour ajouter un compte -->
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script> <!-- Scripts Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>
