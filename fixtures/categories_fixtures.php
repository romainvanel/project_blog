<?php

/**
 * categories_fixtures.php
 */

// Chargement des dépendances Composer
require_once '../vendor/autoload.php';

// Connexion à la base de données
require_once '../connexion.php';
$bdd = connectBdd('root', 'root', 'blog_db');

// Utilisation de la bibliothèque Faker
$faker = Faker\Factory::create();

// Préparation de la requête d'insertion d'utilisation
$insertCategory = $bdd->prepare("INSERT INTO categories (name) VALUES (:name)");

// Générer categories
for ($i = 0; $i < 10; $i++) {
    $insertCategory->bindValue(':name', $faker->word);
    $insertCategory->execute();
}