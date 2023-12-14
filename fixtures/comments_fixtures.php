<?php

/**
 * comments_fixtures.php
 */

// Chargement des dépendances Composer
require_once '../vendor/autoload.php';

// Connexion à la base de données
require_once '../connexion.php';
$bdd = connectBdd('root', 'root', 'blog_db');

// Utilisation de la bibliothèque Faker
$faker = Faker\Factory::create();

// Préparation de la requête d'insertion d'utilisation
$insertComment = $bdd->prepare("INSERT INTO comments (content, comment_date, user_id, article_id) VALUES (:content, :comment_date, :user_id, :article_id)");

// Sélectionne tous les utilisateurs
$selectUsers = $bdd->query("SELECT id FROM users");
$users = $selectUsers->fetchAll();

// Sélectionne tous les articles
$selectArticles = $bdd->query("SELECT id, publication_date FROM articles");
$articles = $selectArticles->fetchAll();

// Générer 200 commentaires
for ($i = 0; $i < 200; $i++) {
    // Sélectionner un utilisateur aléatoirement
    $user = $faker->randomElement($users);

    // Sélecctionner un article aléatoirement
    $article = $faker->randomElement($articles);

    // Génére une date entre la date de création de l'article et aujourd'hui
    $date = $faker->dateTimeBetween($article['publication_date'])->format('Y-m-d H:i:s');

    $insertComment->bindValue(':content', $faker->text);
    $insertComment->bindValue(':comment_date', $date);
    $insertComment->bindValue(':user_id', $user['id']);
    $insertComment->bindValue(':article_id', $article['id']);
    $insertComment->execute();
}