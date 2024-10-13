<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php"><img src="IMAGES/LOGO/logo.webp" alt="Logo de La Banque Française" class="logo"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <?php if (!isset($_SESSION['clientId'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="inscription.php">Inscription</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="connexion.php">Connexion</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
