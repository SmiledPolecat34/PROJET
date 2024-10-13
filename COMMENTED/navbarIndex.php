<nav class="navbar navbar-expand-lg navbar-light bg-light"> <!-- Début de la barre de navigation -->
    <a class="navbar-brand" href="index.php">
        <img src="IMAGES/LOGO/logo.webp" alt="Logo de La Banque Française" class="logo"> <!-- Logo de la banque -->
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"> <!-- Bouton pour les petits écrans -->
        <span class="navbar-toggler-icon"></span> <!-- Icône pour le bouton de menu -->
    </button>
    <div class="collapse navbar-collapse" id="navbarNav"> <!-- Conteneur pour les éléments de navigation -->
        <ul class="navbar-nav ml-auto"> <!-- Liste de navigation alignée à droite -->
            <?php if (!isset($_SESSION['clientId'])): ?> <!-- Vérifie si l'utilisateur n'est pas connecté -->
                <li class="nav-item">
                    <a class="nav-link" href="inscription.php">Inscription</a> <!-- Lien vers la page d'inscription -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="connexion.php">Connexion</a> <!-- Lien vers la page de connexion -->
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
