<?php

/**
 * articles_fixtures.php
 */

// Chargement des dépendances Composer
require_once '../vendor/autoload.php';

// Connexion à la base de données
require_once '../connexion.php';
$bdd = connectBdd('root', 'root', 'blog_db');

// Utilisation de la bibliothèque Faker
$faker = Faker\Factory::create();

// Préparation de la requête d'insertion d'utilisation
$insertArticle = $bdd->prepare("INSERT INTO articles (title, content, cover, publication_date, user_id) VALUES (:title, :content, :cover, :publication_date, :user_id)");

// Sélectionne tous les utilisateurs
$query = $bdd->query("SELECT id FROM users");
$users = $query->fetchAll();


// Générer 50 articles
for ($i = 0; $i < 50; $i++) {
    // Sélectionner un utilisateur aléatoirement
    $user = $faker->randomElement($users);

    // Génère une date entre, il y a deux ans et aujourd'hui
    $date = $faker->dateTimeBetween('-2years')->format('Y-m-d H:i:s');


    $insertArticle->bindValue(':title', $faker->sentence);
    $insertArticle->bindValue(':content', $faker->paragraphs(6, true));
    $insertArticle->bindValue(':cover', $faker->imageURL);
    $insertArticle->bindValue(':publication_date', $date);
    $insertArticle->bindValue(':user_id', $user['id']);
    $insertArticle->execute();
}