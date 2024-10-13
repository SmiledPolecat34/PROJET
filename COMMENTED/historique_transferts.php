<?php
// Inclusion de la barre de navigation pour la page d'accueil
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

// Préparation d'une requête pour récupérer l'historique des transferts
$query = $db->prepare("
    SELECT transferts.*, 
           beneficiaire.nom AS beneficiaireNom, 
           beneficiaire.prenom AS beneficiairePrenom
    FROM transferts
    JOIN client AS beneficiaire ON transferts.destinataireId = beneficiaire.clientId
    WHERE transferts.emetteurId = ?
    ORDER BY transferts.dateTransfert DESC
");
// Exécution de la requête avec l'ID du client
$query->execute([$clientId]);
// Récupération de tous les transferts effectués par le client
$transferts = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des Transferts</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Lien vers Bootstrap -->
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Historique des Transferts</h2>
        <a href="dashboard.php" class="btn btn-primary mb-3">Retour au tableau de bord</a> <!-- Lien vers le tableau de bord -->
        <?php if (count($transferts) === 0): ?> <!-- Vérifie s'il y a des transferts -->
            <div class="alert alert-info">Aucun transfert effectué.</div> <!-- Message si aucun transfert n'est trouvé -->
        <?php else: ?>
            <table class="table table-bordered table-striped"> <!-- Table pour afficher l'historique des transferts -->
                <thead class="thead-dark">
                    <tr>
                        <th>Date</th>
                        <th>Montant (€)</th>
                        <th>Bénéficiaire</th>
                        <th>Motif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transferts as $transfert): ?> <!-- Boucle pour afficher chaque transfert -->
                        <tr>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($transfert['dateTransfert']))); ?></td> <!-- Affichage de la date formatée -->
                            <td><?php echo htmlspecialchars(number_format($transfert['montant'], 2)); ?></td> <!-- Affichage du montant formaté -->
                            <td><?php echo htmlspecialchars($transfert['beneficiairePrenom'] . ' ' . $transfert['beneficiaireNom']); ?></td> <!-- Affichage du nom du bénéficiaire -->
                            <td><?php echo htmlspecialchars($transfert['motif']); ?></td> <!-- Affichage du motif du transfert -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> <!-- Script pour jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script> <!-- Script pour Bootstrap -->
</body>
</html>
<?php include 'footer.php'; ?> <!-- Inclusion du pied de page -->
