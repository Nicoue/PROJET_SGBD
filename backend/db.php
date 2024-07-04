<?php

$host = 'localhost';
$db = 'gestion_notes';
$user = 'root';
$pass = ''; 

try {

    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {

    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
