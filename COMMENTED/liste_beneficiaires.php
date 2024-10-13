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

// Vérification et création d'un token CSRF pour sécuriser le formulaire
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validation du token CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Erreur de validation du formulaire.");
    }

    // Traitement de la modification d'un bénéficiaire
    if (isset($_POST['modifier'])) {
        $beneficiaireId = intval($_POST['beneficiaireId']); // ID du bénéficiaire
        $nom = htmlspecialchars(trim($_POST['nom'])); // Nom du bénéficiaire
        $prenom = htmlspecialchars(trim($_POST['prenom'])); // Prénom du bénéficiaire

        // Préparation de la requête pour mettre à jour les informations du bénéficiaire
        $update = $db->prepare("UPDATE beneficiaires SET nom = ?, prenom = ? WHERE beneficiaireId = ? AND clientId = ?");
        $success = $update->execute([$nom, $prenom, $beneficiaireId, $clientId]);

        if ($success) {
            $message = "Bénéficiaire modifié avec succès.";
        } else {
            $error = "Une erreur est survenue lors de la modification du bénéficiaire.";
        }
    }

    // Traitement de la suppression d'un bénéficiaire
    if (isset($_POST['supprimer'])) {
        $beneficiaireId = intval($_POST['beneficiaireId']); // ID du bénéficiaire

        // Préparation de la requête pour supprimer le bénéficiaire
        $delete = $db->prepare("DELETE FROM beneficiaires WHERE beneficiaireId = ? AND clientId = ?");
        $success = $delete->execute([$beneficiaireId, $clientId]);

        if ($success) {
            $message = "Bénéficiaire supprimé avec succès.";
        } else {
            $error = "Une erreur est survenue lors de la suppression du bénéficiaire.";
        }
    }
}

// Récupération de la liste des bénéficiaires associés au client
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
        // Fonction de confirmation avant la suppression d'un bénéficiaire
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
        
        <?php if (isset($error)): ?> <!-- Affichage d'un message d'erreur si présent -->
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($message)): ?> <!-- Affichage d'un message de succès si présent -->
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if (count($beneficiaires) === 0): ?> <!-- Message si aucun bénéficiaire n'est trouvé -->
            <div class="alert alert-info">Vous n'avez aucun bénéficiaire ajouté.</div>
        <?php else: ?>
            <table class="table table-bordered table-striped"> <!-- Table pour afficher les bénéficiaires -->
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
                    <?php foreach ($beneficiaires as $beneficiaire): ?> <!-- Boucle pour afficher chaque bénéficiaire -->
                        <tr>
                            <td><?php echo htmlspecialchars($beneficiaire['nom']); ?></td>
                            <td><?php echo htmlspecialchars($beneficiaire['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($beneficiaire['typeBeneficiaire']); ?></td>
                            <td><?php echo htmlspecialchars($beneficiaire['beneficiaireNumeroCompte']); ?></td>
                            <td>
                                <!-- Bouton pour ouvrir la modal de modification -->
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modifierModal<?php echo $beneficiaire['beneficiaireId']; ?>">
                                    Modifier
                                </button>
                                <!-- Formulaire pour supprimer le bénéficiaire -->
                                <form method="POST" action="liste_beneficiaires.php" style="display:inline;" onsubmit="return confirmerSuppression('<?php echo htmlspecialchars($beneficiaire['nom']); ?>', '<?php echo htmlspecialchars($beneficiaire['prenom']); ?>');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="beneficiaireId" value="<?php echo $beneficiaire['beneficiaireId']; ?>">
                                    <button type="submit" name="supprimer" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                                <!-- Modal pour modifier le bénéficiaire -->
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> <!-- Script pour jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script> <!-- Script pour Bootstrap -->
</body>
</html>
<?php include 'footer.php'; ?> <!-- Inclusion du pied de page -->
