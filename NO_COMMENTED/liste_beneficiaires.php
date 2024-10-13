<?php
include 'navbarHome.php';
session_start();
include('db_connect.php');

if (!isset($_SESSION['clientId'])) {
    header("Location: connexion.php");
    exit;
}

$clientId = $_SESSION['clientId'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Erreur de validation du formulaire.");
    }

    if (isset($_POST['modifier'])) {
    
        $beneficiaireId = intval($_POST['beneficiaireId']);
        $nom = htmlspecialchars(trim($_POST['nom']));
        $prenom = htmlspecialchars(trim($_POST['prenom']));

        $update = $db->prepare("UPDATE beneficiaires SET nom = ?, prenom = ? WHERE beneficiaireId = ? AND clientId = ?");
        $success = $update->execute([$nom, $prenom, $beneficiaireId, $clientId]);

        if ($success) {
            $message = "Bénéficiaire modifié avec succès.";
        } else {
            $error = "Une erreur est survenue lors de la modification du bénéficiaire.";
        }
    }

    if (isset($_POST['supprimer'])) {
    
        $beneficiaireId = intval($_POST['beneficiaireId']);

        $delete = $db->prepare("DELETE FROM beneficiaires WHERE beneficiaireId = ? AND clientId = ?");
        $success = $delete->execute([$beneficiaireId, $clientId]);

        if ($success) {
            $message = "Bénéficiaire supprimé avec succès.";
        } else {
            $error = "Une erreur est survenue lors de la suppression du bénéficiaire.";
        }
    }
}

$beneficiairesQuery = $db->prepare("
    SELECT b.beneficiaireId, b.nom, b.prenom, cb.numeroCompte AS beneficiaireNumeroCompte, cb.typeDeCompte AS typeBeneficiaire
    FROM beneficiaires b
    JOIN comptebancaire cb ON b.beneficiaireCompteId = cb.compteId
    WHERE b.clientId = ?
    ORDER BY b.nom, b.prenom ASC
");
$beneficiairesQuery->execute([$clientId]);
$beneficiaires = $beneficiairesQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Bénéficiaires</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function confirmerSuppression(nom, prenom) {
            return confirm("Êtes-vous sûr de vouloir supprimer le bénéficiaire " + nom + " " + prenom + " ?");
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Liste des Bénéficiaires</h2>
        <a href="dashboard.php" class="btn btn-primary mb-3">Retour au tableau de bord</a>
        <a href="ajouter_beneficiaire.php" class="btn btn-success mb-3">Ajouter un Bénéficiaire</a>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if (count($beneficiaires) === 0): ?>
            <div class="alert alert-info">Vous n'avez aucun bénéficiaire ajouté.</div>
        <?php else: ?>
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Type de Compte</th>
                        <th>IBAN</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($beneficiaires as $beneficiaire): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($beneficiaire['nom']); ?></td>
                            <td><?php echo htmlspecialchars($beneficiaire['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($beneficiaire['typeBeneficiaire']); ?></td>
                            <td><?php echo htmlspecialchars($beneficiaire['beneficiaireNumeroCompte']); ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modifierModal<?php echo $beneficiaire['beneficiaireId']; ?>">
                                    Modifier
                                </button>
                                <form method="POST" action="liste_beneficiaires.php" style="display:inline;" onsubmit="return confirmerSuppression('<?php echo htmlspecialchars($beneficiaire['nom']); ?>', '<?php echo htmlspecialchars($beneficiaire['prenom']); ?>');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="beneficiaireId" value="<?php echo $beneficiaire['beneficiaireId']; ?>">
                                    <button type="submit" name="supprimer" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                                <div class="modal fade" id="modifierModal<?php echo $beneficiaire['beneficiaireId']; ?>" tabindex="-1" aria-labelledby="modifierModalLabel<?php echo $beneficiaire['beneficiaireId']; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="liste_beneficiaires.php">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modifierModalLabel<?php echo $beneficiaire['beneficiaireId']; ?>">Modifier Bénéficiaire</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="beneficiaireId" value="<?php echo $beneficiaire['beneficiaireId']; ?>">
                                                    <div class="form-group">
                                                        <label for="nom<?php echo $beneficiaire['beneficiaireId']; ?>">Nom</label>
                                                        <input type="text" id="nom<?php echo $beneficiaire['beneficiaireId']; ?>" name="nom" value="<?php echo htmlspecialchars($beneficiaire['nom']); ?>" required class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="prenom<?php echo $beneficiaire['beneficiaireId']; ?>">Prénom</label>
                                                        <input type="text" id="prenom<?php echo $beneficiaire['beneficiaireId']; ?>" name="prenom" value="<?php echo htmlspecialchars($beneficiaire['prenom']); ?>" required class="form-control">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                                    <button type="submit" name="modifier" class="btn btn-warning">Modifier</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
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
