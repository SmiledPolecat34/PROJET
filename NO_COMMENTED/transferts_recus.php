<?php
include 'navbarHome.php';
session_start();
include('db_connect.php');

if (!isset($_SESSION['clientId'])) {
    header("Location: connexion.php");
    exit;
}

$clientId = $_SESSION['clientId'];

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
$query->execute([$clientId]);
$transfertsReçus = $query->fetchAll(PDO::FETCH_ASSOC);

$totalRecu = 0;
foreach ($transfertsReçus as $transfert) {
    $totalRecu += $transfert['montant'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Transferts Reçus</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Transferts Reçus</h2>
        <a href="dashboard.php" class="btn btn-primary mb-3">Retour au tableau de bord</a>

        <h4>Résumé</h4>
        <p>Montant total reçu : <strong><?php echo htmlspecialchars(number_format($totalRecu, 2)); ?> €</strong></p>

        <?php if (count($transfertsReçus) === 0): ?>
            <div class="alert alert-info">Aucun transfert reçu.</div>
        <?php else: ?>
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Date</th>
                        <th>Montant (€)</th>
                        <th>Émetteur</th>
                        <th>Motif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transfertsReçus as $transfert): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($transfert['dateTransfert']))); ?></td>
                            <td><?php echo htmlspecialchars(number_format($transfert['montant'], 2)); ?></td>
                            <td><?php echo htmlspecialchars($transfert['emetteurPrenom'] . ' ' . $transfert['emetteurNom']); ?></td>
                            <td><?php echo htmlspecialchars($transfert['motif']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>
