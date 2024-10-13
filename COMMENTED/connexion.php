<?php
// Inclusion du fichier de navigation pour l'index
include 'navbarIndex.php';
?> 
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> <!-- Lien vers un fichier CSS personnalisé -->
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Connexion</h2>
        <!-- Formulaire de connexion -->
        <form method="POST" action="verifier_connexion.php" class="mt-4">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Email" required> <!-- Champ pour l'email -->
            </div>
            <div class="form-group">
                <label for="mdp">Mot de passe</label>
                <input type="password" id="mdp" name="mdp" class="form-control" placeholder="Mot de passe" required> <!-- Champ pour le mot de passe -->
            </div>
            <button type="submit" class="btn btn-primary btn-block">Se connecter</button> <!-- Bouton de soumission -->
        </form>
    </div>
</body>
</html>
<?php include 'footer.php'; ?>
