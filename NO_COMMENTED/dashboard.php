<?php
include 'navbarHome.php';
session_start();
include('db_connect.php');

if (!isset($_SESSION['clientId'])) {
    header("Location: connexion.php");
    exit;
}

$clientId = $_SESSION['clientId'];

$query = $db->prepare("SELECT * FROM client WHERE clientId = ?");
$query->execute([$clientId]);
$client = $query->fetch(PDO::FETCH_ASSOC);

$query = $db->prepare("SELECT * FROM comptebancaire WHERE clientId = ?");
$query->execute([$clientId]);
$comptes = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Bienvenue, <?php echo htmlspecialchars($client['prenom']); ?>!</h2>
        <div class="d-flex justify-content-between">
            <a href="deconnexion.php" class="btn btn-danger mr-2">Déconnexion</a>
            <a href="historique_transferts.php" class="btn btn-info mr-2">Historique des Transferts</a>
            <a href="transferts_recus.php" class="btn btn-warning mr-2">Transferts Reçus</a>
            <a href="liste_beneficiaires.php" class="btn btn-secondary">Voir la Liste des Bénéficiaires</a>
        </div>

        <h3 class="mt-4">Vos Comptes Bancaires</h3>
        <ul class="list-group mb-4">
            <?php foreach ($comptes as $compte): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <?php echo htmlspecialchars(ucfirst($compte['typeDeCompte'])); ?> - IBAN: <?php echo htmlspecialchars($compte['numeroCompte']); ?> - Solde: <?php echo htmlspecialchars(number_format($compte['solde'], 2)); ?> €
                    </div>
                    <a href="operations.php?compteId=<?php echo htmlspecialchars($compte['compteId']); ?>" class="btn btn-primary">Gérer</a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="mt-3">
            <a href="ajouter_compte.php" class="btn btn-success">Ajouter un nouveau compte</a>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>
