<?php

error_reporting(E_ALL);
session_start();

// Connexion à la base de données
$host = 'localhost';
$dbname = 'to-do_list';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "<script>alert('Erreur de connexion à la base de données.');</script>";
    exit();
}


