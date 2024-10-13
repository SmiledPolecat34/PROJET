<?php
// Démarre une nouvelle session ou reprend une session existante
session_start();

// Détruit toutes les données de session pour l'utilisateur actuel
session_destroy();

// Redirige l'utilisateur vers la page d'accueil (index.php) après la déconnexion
header("Location: index.php");
