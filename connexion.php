<?php

/**
 * Connexion à la base de donées
 */

function connectBdd(
    string $user, // Nom d'utilisateur pour se connecter à la BDD
    string $password, // MOt de passe pour se connecter à la BDD
    string $database, // Nom de la base de données
    string $host = 'localhost' // Localisation de la BDD , argument non obligatoire
    ) 
{
/**
 * on utlisera la classe PHP "PDO" (PHP Data Objects)
 */
try {
    $bdd = new PDO(
        "mysql:host=$host;dbname=$database",
        $user,
        $password,
        [
            // Gestion des erreurs SQL
            PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
            // Gestion des jeux de caractères
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            // Choix du retour des résultats
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );    
} catch(Exception $exception) {
    throw new Exception("La connection à la base de donnée à échouée : {$exception->getMessage()}");
}

return $bdd;
}
