<?php
// Inclusion de la barre de navigation pour la page de transferts reçus
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

// Préparation d'une requête pour récupérer les transferts reçus par le client
$query = $db->prepare("
    SELECT transferts.*, 
           emetteur.prenom AS emetteurPrenom, 
           emetteur.nom AS emetteurNom
    FROM transferts
    JOIN client AS emetteur ON transferts.emetteurId = emetteur.clientId
    WHERE transferts.destinataireId IN (
        SELECT beneficiaireId FROM beneficiaires WHERE clientId = ?
    )
    ORDER BY transferts.dateTransfert DESC
");
$query->execute([$clientId]); // Exécution de la requête avec l'ID du client
$transfertsReçus = $query->fetchAll(PDO::FETCH_ASSOC); // Récupération de tous les transferts reçus

// Calcul du montant total reçu
$totalRecu = 0;
foreach ($transfertsReçus as $transfert) {
    $totalRecu += $transfert['montant']; // Somme des montants des transferts
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> <!-- Définit l'encodage de caractères en UTF-8 -->
    <title>Transferts Reçus</title> <!-- Titre de la page -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Lien vers Bootstrap -->
</head>
<body>
    <div class="container mt-5"> <!-- Conteneur principal pour le contenu de la page -->
        <h2 class="mb-4">Transferts Reçus</h2> <!-- Titre principal pour les transferts reçus -->
        <a href="dashboard.php" class="btn btn-primary mb-3">Retour au tableau de bord</a> <!-- Lien pour retourner au tableau de bord -->

        <h4>Résumé</h4>
        <p>Montant total reçu : <strong><?php echo htmlspecialchars(number_format($totalRecu, 2)); ?> €</strong></p> <!-- Affichage du montant total reçu -->

        <?php if (count($transfertsReçus) === 0): ?> <!-- Vérifie si aucun transfert reçu -->
            <div class="alert alert-info">Aucun transfert reçu.</div> <!-- Message informatif si aucun transfert n'est trouvé -->
        <?php else: ?>
            <table class="table table-bordered table-striped"> <!-- Table pour afficher les transferts reçus -->
                <thead class="thead-dark">
                    <tr>
                        <th>Date</th>
                        <th>Montant (€)</th>
                        <th>Émetteur</th>
                        <th>Motif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transfertsReçus as $transfert): ?> <!-- Boucle pour afficher chaque transfert reçu -->
                        <tr>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($transfert['dateTransfert']))); ?></td> <!-- Date du transfert formatée -->
                            <td><?php echo htmlspecialchars(number_format($transfert['montant'], 2)); ?></td> <!-- Montant du transfert formaté -->
                            <td><?php echo htmlspecialchars($transfert['emetteurPrenom'] . ' ' . $transfert['emetteurNom']); ?></td> <!-- Nom de l'émetteur -->
                            <td><?php echo htmlspecialchars($transfert['motif']); ?></td> <!-- Motif du transfert -->
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
