<?php

require __DIR__ . '/vendor/autoload.php';

$envPath = __DIR__ . '/.env';

if (!file_exists($envPath)) {
    die("Le fichier .env n'existe pas à l'emplacement : " . $envPath);
}

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

try {

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
