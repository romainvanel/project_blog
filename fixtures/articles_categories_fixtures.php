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
$insertValues = $bdd->prepare("INSERT INTO articles_categories (article_id, category_id) VALUES (:article_id, :category_id)");

// Sélectionne tous les articles
$selectArticles = $bdd->query("SELECT id FROM articles");
$articles = $selectArticles->fetchAll();

// Sélectionne tous les utilisateurs
$selectCategories = $bdd->query("SELECT id FROM categories");
$categories = $selectCategories->fetchAll();


// Générer valeurs
foreach ($articles as $article) {

    // Génère un nombre d'itération aléatoire pour la boucle for()
    $iteration = rand(1, 4);

    for ($j = 0; $j < $iteration; $j++) {
        $category = $faker->randomElement($categories);

        $insertValues->bindValue(':article_id', $article['id']);
        $insertValues->bindValue(':category_id', $category['id']);
        $insertValues->execute();
    }
}