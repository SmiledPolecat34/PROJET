<?php
session_start();
include 'navbarIndex.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head> 
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="validation.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Inscription</h2>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        ?>
        <form method="POST" action="ajouter_client.php" onsubmit="return validerInscription()">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" required>
            </div>
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" class="form-control" id="telephone" name="telephone" placeholder="Téléphone">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="mdp">Mot de passe</label>
                <input type="password" class="form-control" id="mdp" name="mdp" placeholder="Mot de passe" required>
            </div>
            <button type="submit" class="btn btn-primary">S’inscrire</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>
