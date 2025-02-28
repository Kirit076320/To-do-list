<?php
session_start();
require_once(__DIR__ . '/bd.php');

if (empty($_SESSION['user_id'])) {
    header('Location: ../pages/connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['list_name'])) {
    // ... traitement existant ...

    // Redirection après création
    header('Location: ../index.php');
    exit;
}