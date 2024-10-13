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

// Récupération de l'ID du client et de l'ID du compte à partir des paramètres de l'URL
$clientId = $_SESSION['clientId'];
$compteId = $_GET['compteId'];

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération et conversion du montant déposé en float
    $montant = floatval($_POST['montant']);
    
    // Vérifie si le montant est positif
    if ($montant > 0) {
        // Prépare la requête pour mettre à jour le solde du compte bancaire
        $update = $db->prepare("UPDATE comptebancaire SET solde = solde + ? WHERE compteId = ? AND clientId = ?");
        // Exécute la requête avec les valeurs fournies
        $update->execute([$montant, $compteId, $clientId]);
        
        // Redirige l'utilisateur vers la page des opérations pour ce compte
        header("Location: operations.php?compteId=" . $compteId);
        exit; // Termine le script après la redirection
    } else {
        // Si le montant n'est pas valide, stocke un message d'erreur
        $error = "Le montant doit être positif.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dépôt</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet"> <!-- Lien vers Bootstrap -->
</head>
<body>
    <div class="container mt-5">
        <h2>Dépôt sur le Compte</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert"> <!-- Affichage d'un message d'erreur -->
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <!-- Formulaire pour le dépôt d'argent -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="montant">Montant à déposer</label>
                <input type="number" class="form-control" id="montant" name="montant" min="1" step="0.01" required> <!-- Champ pour le montant -->
            </div>
            <button type="submit" class="btn btn-primary">Déposer</button> <!-- Bouton pour soumettre le dépôt -->
            <a href="operations.php?compteId=<?php echo htmlspecialchars($compteId); ?>" class="btn btn-secondary">Retour</a> <!-- Lien pour revenir à la page des opérations -->
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>
