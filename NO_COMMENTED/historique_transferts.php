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
           beneficiaire.nom AS beneficiaireNom, 
           beneficiaire.prenom AS beneficiairePrenom
    FROM transferts
    JOIN client AS beneficiaire ON transferts.destinataireId = beneficiaire.clientId
    WHERE transferts.emetteurId = ?
    ORDER BY transferts.dateTransfert DESC
");
$query->execute([$clientId]);
$transferts = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des Transferts</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Historique des Transferts</h2>
        <a href="dashboard.php" class="btn btn-primary mb-3">Retour au tableau de bord</a>
        <?php if (count($transferts) === 0): ?>
            <div class="alert alert-info">Aucun transfert effectué.</div>
        <?php else: ?>
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Date</th>
                        <th>Montant (€)</th>
                        <th>Bénéficiaire</th>
                        <th>Motif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transferts as $transfert): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($transfert['dateTransfert']))); ?></td>
                            <td><?php echo htmlspecialchars(number_format($transfert['montant'], 2)); ?></td>
                            <td><?php echo htmlspecialchars($transfert['beneficiairePrenom'] . ' ' . $transfert['beneficiaireNom']); ?></td>
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
