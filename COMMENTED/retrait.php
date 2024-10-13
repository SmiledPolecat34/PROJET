<?php
// Inclusion de la barre de navigation pour la page de retrait
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

// Récupération de l'ID du client et de l'ID du compte à partir des paramètres d'URL
$clientId = $_SESSION['clientId'];
$compteId = $_GET['compteId']; // Récupération de l'ID du compte à retirer

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération du montant à retirer et conversion en float
    $montant = floatval($_POST['montant']);
    
    // Vérifie si le montant est positif
    if ($montant > 0) {
        // Prépare une requête pour récupérer le solde du compte
        $query = $db->prepare("SELECT solde FROM comptebancaire WHERE compteId = ? AND clientId = ?");
        $query->execute([$compteId, $clientId]);
        $compte = $query->fetch(PDO::FETCH_ASSOC); // Récupère le compte

        // Vérifie si le solde est suffisant pour effectuer le retrait
        if ($compte['solde'] >= $montant) {
            // Prépare et exécute la requête pour mettre à jour le solde du compte
            $update = $db->prepare("UPDATE comptebancaire SET solde = solde - ? WHERE compteId = ? AND clientId = ?");
            $update->execute([$montant, $compteId, $clientId]);
            // Redirige vers la page des opérations après le retrait
            header("Location: operations.php?compteId=" . $compteId);
        } else {
            // Affiche un message d'erreur si le solde est insuffisant
            echo '<div class="alert alert-danger" role="alert">Solde insuffisant.</div>';
        }
    } else {
        // Affiche un message d'avertissement si le montant est négatif
        echo '<div class="alert alert-warning" role="alert">Le montant doit être positif.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> <!-- Définit l'encodage de caractères en UTF-8 -->
    <title>Retrait</title> <!-- Titre de la page -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Lien vers Bootstrap -->
</head>
<body>
    <div class="container mt-5"> <!-- Conteneur principal pour le contenu de la page -->
        <h2 class="mb-4">Retrait du Compte</h2> <!-- Titre principal pour la page de retrait -->
        <form method="POST" action=""> <!-- Formulaire pour soumettre le retrait -->
            <div class="mb-3">
                <label for="montant" class="form-label">Montant à retirer</label> <!-- Étiquette pour le champ de montant -->
                <input type="number" class="form-control" id="montant" name="montant" min="1" required> <!-- Champ pour le montant -->
            </div>
            <button type="submit" class="btn btn-primary">Retirer</button> <!-- Bouton pour soumettre le retrait -->
            <a href="operations.php?compteId=<?php echo htmlspecialchars($compteId); ?>" class="btn btn-secondary">Retour</a> <!-- Lien pour revenir à la page des opérations -->
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!-- Script pour Bootstrap -->
</body>
</html>
<?php include 'footer.php'; ?> <!-- Inclusion du pied de page -->
