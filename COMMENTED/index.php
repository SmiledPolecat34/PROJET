<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>La Banque Française</title>
    <link rel="stylesheet" href="style.css"> <!-- Lien vers le fichier CSS pour le style -->
</head>
<body>
    <div class="container"> <!-- Conteneur principal pour le contenu de la page -->
        <img src="IMAGES/LOGO/logo.webp" alt="Logo de La Banque Française" class="logo"> <!-- Affichage du logo de la banque -->
        <h1>Bienvenue chez La Banque Française</h1> <!-- Titre principal de la page -->
        <div>
            <a href="inscription.php" class="button btn-register">Créer un compte</a> <!-- Lien vers la page d'inscription -->
            <a href="connexion.php" class="button btn-login">Accéder à mon espace</a> <!-- Lien vers la page de connexion -->
        </div>
        <h2>Nos Types de Comptes</h2> <!-- Sous-titre pour présenter les types de comptes -->
        <p class="text-left"><strong>Compte Courant :</strong> Destiné à la gestion quotidienne de vos finances, le compte courant vous permet d'effectuer des dépôts, des retraits et des virements facilement. Il offre également des services bancaires en ligne et une carte de débit.</p>
        <p class="text-left"><strong>Compte Épargne :</strong> Idéal pour épargner sur le long terme, ce compte vous permet de mettre de l'argent de côté tout en bénéficiant d'un taux d'intérêt attractif. Il est parfait pour préparer vos projets futurs ou constituer un fonds de sécurité.</p>
        <p class="text-left"><strong>Compte Entreprise :</strong> Conçu spécialement pour les besoins des entreprises, ce compte facilite la gestion des transactions commerciales, le suivi des dépenses et des revenus, ainsi que la préparation des états financiers.</p>
        <p class="text-left"><strong>Nombre de Comptes Autorisés :</strong> Chaque client peut détenir jusqu'à 2 comptes épargne et un nombre illimité de comptes entreprise et courant. Cette flexibilité vous permet de gérer efficacement vos finances personnelles et professionnelles.</p>
    </div>
</body>
</html>
<?php include 'footer.php'; ?> <!-- Inclusion du pied de page -->
