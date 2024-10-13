<?php
// Démarre ou reprend une session existante
session_start();
// Inclusion de la barre de navigation pour la page d'inscription
include 'navbarIndex.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head> 
    <meta charset="UTF-8"> <!-- Définit l'encodage de caractères en UTF-8 -->
    <title>Inscription</title> <!-- Titre de la page -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> <!-- Lien vers Bootstrap pour le style -->
    <link rel="stylesheet" href="style.css"> <!-- Lien vers le fichier CSS personnalisé -->
    <script src="validation.js"></script> <!-- Lien vers un fichier JavaScript pour la validation du formulaire -->
</head>
<body>
    <div class="container mt-5"> <!-- Conteneur principal pour le contenu de la page -->
        <h2 class="mb-4">Inscription</h2> <!-- Titre principal pour le formulaire d'inscription -->
        
        <?php
        // Vérifie si un message d'erreur est présent dans la session et l'affiche
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']); // Supprime le message d'erreur après affichage
        }
        // Vérifie si un message de succès est présent dans la session et l'affiche
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']); // Supprime le message de succès après affichage
        }
        ?>
        
        <!-- Formulaire d'inscription -->
        <form method="POST" action="ajouter_client.php" onsubmit="return validerInscription()">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required> <!-- Champ pour le nom -->
            </div>
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" required> <!-- Champ pour le prénom -->
            </div>
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" class="form-control" id="telephone" name="telephone" placeholder="Téléphone"> <!-- Champ pour le téléphone -->
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required> <!-- Champ pour l'email -->
            </div>
            <div class="form-group">
                <label for="mdp">Mot de passe</label>
                <input type="password" class="form-control" id="mdp" name="mdp" placeholder="Mot de passe" required> <!-- Champ pour le mot de passe -->
            </div>
            <button type="submit" class="btn btn-primary">S’inscrire</button> <!-- Bouton pour soumettre le formulaire -->
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> <!-- Script pour jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script> <!-- Script pour Popper.js -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> <!-- Script pour Bootstrap -->
</body>
</html>
<?php include 'footer.php'; ?> <!-- Inclusion du pied de page -->
