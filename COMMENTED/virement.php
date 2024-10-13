<?php
// Inclusion de la barre de navigation pour la page de virement
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
$compteId = $_GET['compteId'] ?? null; // Récupération de l'ID du compte

// Création d'un token CSRF pour sécuriser le formulaire
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Génère un token aléatoire
}

// Préparation d'une requête pour récupérer les comptes émetteurs du client
$emetteurQuery = $db->prepare("SELECT compteId, numeroCompte, typeDeCompte, solde FROM comptebancaire WHERE clientId = ?");
$emetteurQuery->execute([$clientId]);
$emetteurComptes = $emetteurQuery->fetchAll(PDO::FETCH_ASSOC); // Récupération des comptes émetteurs

// Préparation d'une requête pour récupérer les bénéficiaires du client
$beneficiairesQuery = $db->prepare("
    SELECT b.beneficiaireId, b.nom, b.prenom, cb.numeroCompte AS beneficiaireNumeroCompte, cb.typeDeCompte AS typeBeneficiaire
    FROM beneficiaires b
    JOIN comptebancaire cb ON b.beneficiaireCompteId = cb.compteId
    WHERE b.clientId = ?
");
$beneficiairesQuery->execute([$clientId]);
$beneficiaires = $beneficiairesQuery->fetchAll(PDO::FETCH_ASSOC); // Récupération des bénéficiaires

// Préparation d'une requête pour récupérer tous les comptes du client
$propriosComptesQuery = $db->prepare("SELECT compteId, numeroCompte, typeDeCompte, solde FROM comptebancaire WHERE clientId = ?");
$propriosComptesQuery->execute([$clientId]);
$propriosComptes = $propriosComptesQuery->fetchAll(PDO::FETCH_ASSOC); // Récupération des comptes du client

// Compte le nombre de comptes propres et de bénéficiaires
$nbPropriosComptes = count($propriosComptes);
$nbBeneficiaires = count($beneficiaires);

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validation du token CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Erreur de validation du formulaire.");
    }

    // Récupération des données du formulaire
    $emetteurCompteId = intval($_POST['emetteurCompteId']); // ID du compte émetteur
    $numeroCompteDestinataire = strtoupper(trim($_POST['numeroCompteDestinataire'])); // IBAN du destinataire
    $montant = floatval($_POST['montant']); // Montant du virement
    $motif = htmlspecialchars(trim($_POST['motif'])); // Motif du virement

    // Vérifie si le montant est positif
    if ($montant <= 0) {
        $error = "Le montant doit être supérieur à 0.";
    }

    // Vérification du solde du compte émetteur
    $emetteurValidationQuery = $db->prepare("SELECT solde FROM comptebancaire WHERE compteId = ? AND clientId = ?");
    $emetteurValidationQuery->execute([$emetteurCompteId, $clientId]);
    if ($emetteurValidationQuery->rowCount() === 0) {
        $error = "Compte émetteur invalide.";
    } else {
        $soldeEmetteur = $emetteurValidationQuery->fetch(PDO::FETCH_ASSOC)['solde'];
    }

    // Vérification de la validité du compte destinataire
    $destinataireQuery = $db->prepare("
        SELECT c.clientId, c.numeroCompte, c.compteId
        FROM comptebancaire c
        WHERE c.numeroCompte = ?
          AND (c.clientId = ? OR c.compteId IN (
              SELECT beneficiaireCompteId FROM beneficiaires WHERE clientId = ?
          ))
    ");
    $destinataireQuery->execute([$numeroCompteDestinataire, $clientId, $clientId]);
    if ($destinataireQuery->rowCount() === 0) {
        $error = "Aucun compte valide trouvé avec cet IBAN.";
    } else {
        $destinataire = $destinataireQuery->fetch(PDO::FETCH_ASSOC);
        $destinataireClientId = $destinataire['clientId'];
        $destinataireCompteId = $destinataire['compteId'];
    }

    // Vérification du solde suffisant
    if (!isset($error)) {
        if ($soldeEmetteur < $montant) {
            $error = "Solde insuffisant.";
        }
    }

    // Exécution de la transaction
    if (!isset($error)) {
        try {
            $db->beginTransaction(); // Démarre la transaction

            // Mise à jour du solde du compte émetteur
            $debit = $db->prepare("UPDATE comptebancaire SET solde = solde - ? WHERE compteId = ?");
            $debit->execute([$montant, $emetteurCompteId]);

            // Mise à jour du solde du compte destinataire
            $credit = $db->prepare("UPDATE comptebancaire SET solde = solde + ? WHERE compteId = ?");
            $credit->execute([$montant, $destinataireCompteId]);

            // Insertion de l'opération dans la table des transferts
            $insert = $db->prepare("
                INSERT INTO transferts (emetteurId, destinataireId, emetteurCompteId, beneficiaireCompteId, montant, motif, dateTransfert)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");

            // Vérifie si le destinataire est un bénéficiaire ou un autre client
            if ($destinataireClientId != $clientId) {
                // Récupération de l'ID du bénéficiaire
                $beneficiaireIdQuery = $db->prepare("
                    SELECT beneficiaireId 
                    FROM beneficiaires 
                    WHERE clientId = ? 
                      AND beneficiaireCompteId = ?
                ");
                $beneficiaireIdQuery->execute([$clientId, $destinataireCompteId]);
                if ($beneficiaireIdQuery->rowCount() > 0) {
                    $beneficiaire = $beneficiaireIdQuery->fetch(PDO::FETCH_ASSOC);
                    $beneficiaireId = $beneficiaire['beneficiaireId'];
                } else {
                    $beneficiaireId = NULL;
                }
            } else {
                $beneficiaireId = NULL; // Si c'est un transfert vers son propre compte
            }

            // Exécution de l'insertion
            $insert->execute([
                $clientId,
                $destinataireClientId,
                $emetteurCompteId,
                $destinataireCompteId,
                $montant,
                $motif
            ]);

            $db->commit(); // Valide la transaction
            $message = "Virement effectué avec succès."; // Message de succès
        } catch (Exception $e) {
            $db->rollBack(); // Annule la transaction en cas d'erreur
            $error = "Une erreur est survenue lors du virement : " . $e->getMessage(); // Message d'erreur
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Effectuer un Virement</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="validation.js"></script> <!-- Script de validation JavaScript -->
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Effectuer un Virement</h2>
        <a href="dashboard.php" class="btn btn-primary mb-3">Retour au tableau de bord</a>
        <a href="liste_beneficiaires.php" class="btn btn-secondary mb-3">Voir la Liste des Bénéficiaires</a>
        <?php if (isset($error)): ?> <!-- Affichage d'un message d'erreur si présent -->
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($message)): ?> <!-- Affichage d'un message de succès si présent -->
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php
            // Vérification du nombre de comptes
            $nbComptes = count($emetteurComptes);
            if ($nbComptes < 1) {
                echo '<div class="alert alert-warning">Vous devez avoir au moins un compte pour effectuer un virement.</div>'; // Alerte si aucun compte émetteur
            }
        ?>
        <?php if ($nbComptes >= 1): ?> <!-- Affichage du formulaire si au moins un compte émetteur est disponible -->
            <form method="POST" action="virement.php" onsubmit="return validerVirement()"> <!-- Formulaire de virement -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"> <!-- Champ caché pour le token CSRF -->
                <div class="form-group">
                    <label for="emetteurCompteId">Compte Émetteur</label>
                    <select id="emetteurCompteId" name="emetteurCompteId" required class="form-control"> <!-- Sélecteur pour le compte émetteur -->
                        <option value="" disabled selected>Sélectionnez un compte émetteur</option>
                        <?php foreach ($emetteurComptes as $compte): ?>
                            <option value="<?php echo htmlspecialchars($compte['compteId']); ?>">
                                <?php echo htmlspecialchars($compte['typeDeCompte'] . " - " . $compte['numeroCompte'] . " (Solde: " . number_format($compte['solde'], 2) . " €)"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="numeroCompteDestinataire">Destinataire</label>
                    <?php
                        // Vérification des comptes et des bénéficiaires
                        $hasBeneficiaires = $nbBeneficiaires > 0;
                        $hasPropriosComptes = $nbPropriosComptes > 1;
                    ?>
                    <select id="numeroCompteDestinataire" name="numeroCompteDestinataire" required class="form-control"> <!-- Sélecteur pour le destinataire -->
                        <option value="" disabled selected>Sélectionnez un destinataire</option>
                        <?php if ($hasPropriosComptes): ?>
                            <optgroup label="Vos propres comptes">
                                <?php foreach ($propriosComptes as $compte): ?>
                                    <option value="<?php echo htmlspecialchars($compte['numeroCompte']); ?>">
                                        <?php echo htmlspecialchars($compte['typeDeCompte'] . " - " . $compte['numeroCompte'] . " (Solde: " . number_format($compte['solde'], 2) . " €)"); ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php elseif ($nbPropriosComptes == 1): ?>
                            <optgroup label="Vos propres comptes">
                                <option disabled>Vous avez seulement un compte. Ajoutez-en un pour effectuer des virements internes.</option>
                            </optgroup>
                        <?php else: ?>
                            <optgroup label="Vos propres comptes">
                                <option disabled>Aucun compte disponible</option>
                            </optgroup>
                        <?php endif; ?>

                        <?php if ($hasBeneficiaires): ?>
                            <optgroup label="Bénéficiaires">
                                <?php foreach ($beneficiaires as $beneficiaire): ?>
                                    <option value="<?php echo htmlspecialchars($beneficiaire['beneficiaireNumeroCompte']); ?>">
                                        <?php echo htmlspecialchars($beneficiaire['nom'] . ' ' . $beneficiaire['prenom'] . ' - ' . $beneficiaire['beneficiaireNumeroCompte']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php else: ?>
                            <optgroup label="Bénéficiaires">
                                <option disabled>Aucun bénéficiaire ajouté. Ajoutez-en un pour effectuer des virements vers des tiers.</option>
                            </optgroup>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="montant">Montant (€)</label>
                    <input type="number" id="montant" name="montant" placeholder="Montant" min="0.01" step="0.01" required class="form-control"> <!-- Champ pour le montant -->
                </div>
                <div class="form-group">
                    <label for="motif">Motif</label>
                    <input type="text" id="motif" name="motif" placeholder="Motif du virement" required class="form-control"> <!-- Champ pour le motif -->
                </div>
                <button type="submit" class="btn btn-success">Effectuer Virement</button> <!-- Bouton pour soumettre le virement -->
                <a href="operations.php?compteId=<?php echo htmlspecialchars($compteId); ?>" class="btn btn-secondary">Retour</a> <!-- Lien pour revenir aux opérations -->
            </form>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> <!-- Script pour jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script> <!-- Script pour Bootstrap -->
</body>
</html>
<?php include 'footer.php'; ?> <!-- Inclusion du pied de page -->
