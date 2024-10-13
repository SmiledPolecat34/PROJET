<?php

// Dotenv
require __DIR__ . '/vendor/autoload.php';

// Chemin vers le fichier .env (ajuster le chemin)
$envPath = __DIR__ . '/.env'; // Changez ce chemin

if (!file_exists($envPath)) {
    die("Le fichier .env n'existe pas à l'emplacement : " . $envPath);
}

// Chargement des variables d'environnement depuis le fichier .env
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

try {
    // Utilisation des variables d'environnement pour la connexion à la base de données
    $db = new PDO(
        'mysql:host=' . $_ENV['DB_HOST'] . 
        ';dbname=' . $_ENV['DB_NAME'] . 
        ';charset=' . $_ENV['DB_CHARSET'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD']
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
