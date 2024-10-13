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

        $query = $db->prepare("SELECT solde FROM comptebancaire WHERE compteId = ? AND clientId = ?");
        $query->execute([$compteId, $clientId]);
        $compte = $query->fetch(PDO::FETCH_ASSOC);

        if ($compte['solde'] >= $montant) {
            $update = $db->prepare("UPDATE comptebancaire SET solde = solde - ? WHERE compteId = ? AND clientId = ?");
            $update->execute([$montant, $compteId, $clientId]);
            header("Location: operations.php?compteId=" . $compteId);
        } else {
            echo '<div class="alert alert-danger" role="alert">Solde insuffisant.</div>';
        }
    } else {
        echo '<div class="alert alert-warning" role="alert">Le montant doit être positif.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Retrait</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <h2 class="mb-4">Retrait du Compte</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="montant" class="form-label">Montant à retirer</label>
                    <input type="number" class="form-control" id="montant" name="montant" min="1" required>
                </div>
                <button type="submit" class="btn btn-primary">Retirer</button>
                <a href="operations.php?compteId=<?php echo htmlspecialchars($compteId); ?>" class="btn btn-secondary">Retour</a>
            </form>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
<?php include 'footer.php'; ?>

