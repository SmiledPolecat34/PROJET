<?php
// Inclusion du fichier de navigation pour la page d'accueil
include 'navbarHome.php';
// Démarrage de la session pour gérer les données utilisateur
session_start();
// Inclusion du fichier de connexion à la base de données
include('db_connect.php');

// Vérifie si l'ID du client est défini dans la session, sinon redirige vers la page de connexion
if (!isset($_SESSION['clientId'])) {
    header("Location: connexion.php");
    exit;
}

// Récupération de l'ID du client depuis la session
$clientId = $_SESSION['clientId'];

// Génération d'un token CSRF pour sécuriser le formulaire si ce n'est pas déjà fait
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Vérification si la méthode de requête est POST (indiquant une soumission de formulaire)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Validation du token CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Erreur de validation du formulaire.");
    }

    // Récupération et nettoyage des données du formulaire
    $nom = htmlspecialchars(trim($_POST['nom'])); // Nom du bénéficiaire
    $prenom = htmlspecialchars(trim($_POST['prenom'])); // Prénom du bénéficiaire
    $numeroCompte = strtoupper(trim($_POST['numeroCompte'])); // Numéro de compte en majuscules

    // Validation du format de l'IBAN
    if (!preg_match('/^FR\d{25}$/', $numeroCompte)) {
        $error = "IBAN invalide. Assurez-vous qu'il commence par 'FR' suivi de 25 chiffres.";
    } else {
        // Préparation d'une requête pour vérifier si le bénéficiaire existe déjà
        $query = $db->prepare("
            SELECT b.*
            FROM beneficiaires b
            JOIN comptebancaire c ON b.beneficiaireCompteId = c.compteId
            WHERE b.clientId = ? 
              AND c.numeroCompte = ?
        ");
        $query->execute([$clientId, $numeroCompte]);

        // Vérification si le bénéficiaire est déjà ajouté
        if ($query->rowCount() > 0) {
            $error = "Ce bénéficiaire est déjà ajouté.";
        } else {
            // Préparation d'une requête pour vérifier si le compte existe
            $compteQuery = $db->prepare("SELECT compteId FROM comptebancaire WHERE numeroCompte = ?");
            $compteQuery->execute([$numeroCompte]);

            // Vérification si aucun compte bancaire n'est trouvé
            if ($compteQuery->rowCount() === 0) {
                $error = "Aucun compte bancaire trouvé avec cet IBAN.";
            } else {
                $compte = $compteQuery->fetch(PDO::FETCH_ASSOC);
                $beneficiaireCompteId = $compte['compteId'];
                
                // Préparation de l'insertion du bénéficiaire dans la base de données
                $insert = $db->prepare("
                    INSERT INTO beneficiaires (clientId, beneficiaireCompteId, nom, prenom) 
                    VALUES (?, ?, ?, ?)
                ");
                // Exécution de la requête d'insertion
                $success = $insert->execute([$clientId, $beneficiaireCompteId, $nom, $prenom]);

                // Vérification du succès de l'insertion
                if ($success) {
                    $message = "Bénéficiaire ajouté avec succès.";
                } else {
                    $error = "Une erreur est survenue lors de l'ajout du bénéficiaire.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Bénéficiaire</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="validation.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Ajouter un Bénéficiaire</h2>
        <a href="dashboard.php" class="btn btn-primary mb-3">Retour au tableau de bord</a>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <!-- Formulaire pour ajouter un bénéficiaire -->
        <form method="POST" action="ajouter_beneficiaire.php" onsubmit="return validerBeneficiaire()">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-group">
                <label for="nom">Nom du Bénéficiaire</label>
                <input type="text" id="nom" name="nom" placeholder="Nom" required class="form-control">
            </div>
            <div class="form-group">
                <label for="prenom">Prénom du Bénéficiaire</label>
                <input type="text" id="prenom" name="prenom" placeholder="Prénom" required class="form-control">
            </div>
            <div class="form-group">
                <label for="numeroCompte">IBAN du Bénéficiaire</label>
                <input type="text" id="numeroCompte" name="numeroCompte" placeholder="FR7612345678901234567890123" pattern="FR\d{25}" title="Format IBAN français : FR suivi de 25 chiffres" required class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Ajouter Bénéficiaire</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>
