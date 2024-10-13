<?php
include 'navbarHome.php';
session_start();
include('db_connect.php');

if (!isset($_SESSION['clientId'])) {
    header("Location: connexion.php");
    exit;
}

$clientId = $_SESSION['clientId'];
$compteId = $_GET['compteId'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $montant = floatval($_POST['montant']);
    if ($montant > 0) {
        $update = $db->prepare("UPDATE comptebancaire SET solde = solde + ? WHERE compteId = ? AND clientId = ?");
        $update->execute([$montant, $compteId, $clientId]);
        header("Location: operations.php?compteId=" . $compteId);
        exit;
    } else {
        $error = "Le montant doit être positif.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dépôt</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Dépôt sur le Compte</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="montant">Montant à déposer</label>
                <input type="number" class="form-control" id="montant" name="montant" min="1" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary">Déposer</button>
            <a href="operations.php?compteId=<?php echo htmlspecialchars($compteId); ?>" class="btn btn-secondary">Retour</a>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>

