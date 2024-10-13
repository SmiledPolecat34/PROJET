<?php
// Inclusion du fichier de navigation pour la page d'accueil
include 'navbarHome.php';
// Démarre une nouvelle session ou reprend une session existante
session_start();
// Inclusion du fichier de connexion à la base de données
include('db_connect.php');

// Vérifie si l'ID du client est défini dans la session, sinon affiche un message d'erreur
if (isset($_SESSION['clientId'])) {
    $clientId = $_SESSION['clientId'];
} else {
    echo "Erreur : clientId non défini.";
    exit; // Termine le script si l'ID du client n'est pas disponible
}

// Vérifie si la méthode de requête est POST (indiquant une soumission de formulaire)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération et conversion du solde en nombre flottant
    $solde = floatval($_POST['solde']);
    // Nettoyage du type de compte
    $typeDeCompte = htmlspecialchars($_POST['typeDeCompte']);

    // Validation du solde pour s'assurer qu'il est dans les limites spécifiées
    if ($solde < 10 || $solde > 2000) {
        $error = "Le solde doit être compris entre 10 et 2000 €.";
    }

    // Liste des types de compte valides
    $validTypes = ['courant', 'epargne', 'entreprise'];
    // Vérification que le type de compte est valide
    if (!in_array($typeDeCompte, $validTypes)) {
        $error = "Le type de compte est invalide.";
    }

    // Si le type de compte est épargne, vérifie le nombre de comptes d'épargne existants
    if ($typeDeCompte === 'epargne') {
        $countQuery = $db->prepare("SELECT COUNT(*) as epargneCount FROM comptebancaire WHERE clientId = ? AND typeDeCompte = 'epargne'");
        $countQuery->execute([$clientId]);
        $countResult = $countQuery->fetch(PDO::FETCH_ASSOC);
        // Si le client a déjà 2 comptes d'épargne, affiche une erreur
        if ($countResult['epargneCount'] >= 2) {
            $error = "Vous ne pouvez pas avoir plus de 2 comptes d'épargne.";
        }
    }

    // Si aucune erreur n'a été détectée, poursuivre la création du compte
    if (!isset($error)) {
        // Fonction pour générer une chaîne de chiffres aléatoires
        function generateRandomNumberString($length) {
            $numbers = '';
            for ($i = 0; $i < $length; $i++) {
                $numbers .= rand(0, 9); // Ajoute un chiffre aléatoire à la chaîne
            }
            return $numbers; // Retourne la chaîne de chiffres
        }

        // Boucle pour générer un IBAN unique
        do {
            $controle = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT); // Génère et formate les chiffres de contrôle
            $ibanNumber = generateRandomNumberString(23); // Génère 23 chiffres aléatoires
            $iban = 'FR' . $controle . $ibanNumber; // Assemble l'IBAN

            // Vérification que l'IBAN n'est pas déjà utilisé
            $query = $db->prepare("SELECT * FROM comptebancaire WHERE numeroCompte = ?");
            $query->execute([$iban]);
        } while ($query->rowCount() > 0); // Continue à générer jusqu'à ce qu'un IBAN unique soit trouvé

        // Date d'ouverture du compte
        $dateOuverture = date('Y-m-d H:i:s');

        // Préparation de la requête pour insérer le compte bancaire dans la base de données
        $insert = $db->prepare("INSERT INTO comptebancaire (clientId, numeroCompte, solde, typeDeCompte, dateOuverture) VALUES (?, ?, ?, ?, ?)");
        // Exécution de la requête d'insertion
        $success = $insert->execute([$clientId, $iban, $solde, $typeDeCompte, $dateOuverture]);

        // Vérification du succès de l'insertion
        if ($success) {
            $message = "Compte créé avec succès ! Votre IBAN est : <strong>" . htmlspecialchars($iban) . "</strong>";
        } else {
            $error = "Une erreur est survenue lors de la création du compte.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Création de Compte Bancaire</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="validation.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Création d'un Compte Bancaire</h2>
        <a href="dashboard.php" class="btn btn-secondary mb-3">Retour au tableau de bord</a>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <!-- Formulaire pour créer un compte bancaire -->
        <form method="POST" action="ajouter_compte.php" onsubmit="return validerCompte()">
            <div class="form-group">
                <label for="solde">Solde</label>
                <input type="number" class="form-control" id="solde" name="solde" placeholder="Solde initial" min="10" max="2000" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="typeDeCompte">Type de compte</label>
                <select id="typeDeCompte" name="typeDeCompte" class="form-control" required>
                    <option value="courant">Courant</option>
                    <option value="epargne">Épargne</option>
                    <option value="entreprise">Entreprise</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Créer compte</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>
