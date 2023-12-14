<?php

// Démarrer une session
session_start();

// Vérifie si l'utilisateur peut accéder à cette page (pour chaque page que l'on veut protéger)
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Administration</title>
    </head>
    <body>
        <h1>Administration</h1>
        <a href="logout.php">Déconnexion</a>
    </body>
</html>