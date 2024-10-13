<?php
include 'navbarHome.php';
session_start();
include('db_connect.php');

if (!isset($_SESSION['clientId'])) {
    header("Location: connexion.php");
    exit;
}

$clientId = $_SESSION['clientId'];
$compteId = $_GET['compteId'] ?? null;

if ($compteId === null) {
    echo "Erreur : compteId non défini.";
    exit;
}

$query = $db->prepare("SELECT * FROM comptebancaire WHERE compteId = ? AND clientId = ?");
$query->execute([$compteId, $clientId]);
$compte = $query->fetch(PDO::FETCH_ASSOC);

if (!$compte) {
    echo "Compte non trouvé ou vous n'avez pas les droits d'accès.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion du Compte</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Gestion du Compte <?php echo htmlspecialchars($compte['numeroCompte']); ?></h2>
        <div class="card mb-4">
            <div class="card-body">
                <p class="card-text"><strong>Type de compte:</strong> <?php echo htmlspecialchars($compte['typeDeCompte']); ?></p>
                <p class="card-text"><strong>Solde actuel:</strong> <?php echo htmlspecialchars(number_format($compte['solde'], 2)); ?> €</p>
            </div>
        </div>
        <div role="group" aria-label="Opérations">
            <a href="depot.php?compteId=<?php echo $compteId; ?>" class="btn btn-success">Dépôt</a>
            <a href="retrait.php?compteId=<?php echo $compteId; ?>" class="btn btn-warning">Retrait</a>
            <a href="virement.php?compteId=<?php echo $compteId; ?>" class="btn btn-info">Virement</a>
            <a href="dashboard.php" class="btn btn-secondary">Retour au tableau de bord</a>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>
