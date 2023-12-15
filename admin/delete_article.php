<?php

// Supprimer un article :

// Créer un fichier « delete_article.php ». Faire bien attention a ce qu’on ne puisse pas supprimer un article ne nous appartenant pas et avant la suppression, avoir une demande de confirmation et après la suppression, revenir sur le tableau avec un message de succès

// Démarrer une session
session_start();

// Vérifie si l'utilisateur peut accéder à cette page (pour chaque page que l'on veut protéger)
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Vérifie si le paramètre "id" est présent et/ou non vide
if (empty($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

// Connexion à la base de données
require_once '../connexion.php';
$bdd = connectBdd('root', 'root', 'blog_db');

// Récupération de l'ID de l'article
$articleId = $_GET['id'];

// Sélection des informations concernant l'article
$selectArticle = $bdd->prepare("SELECT * FROM articles WHERE id = :id");
$selectArticle->bindValue(':id', $articleId);
$selectArticle->execute();

$article = $selectArticle->fetch();

echo '<pre>';
var_dump($article);
echo '</pre>';

// Si aucun article n'existe avec cet ID, redirection vers le dashboard.php et vérifier que l'article sélectionné appartient bien à l'utilisateur connecté
if (!$article || $article['user_id'] !== $_SESSION['user']['id']) {
    header('Location: dashboard.php');
    exit;
}

// Désactivez temporairement la vérification des clés étrangères pour supprimer l'article
$bdd->exec("SET foreign_key_checks = 0");

// Suppression des articles 
$deleteArticle = $bdd->prepare("DELETE FROM articles WHERE id = :id");
$deleteArticle->bindValue(':id', $articleId);
$deleteArticle->execute();

// Réactivez la vérification des clés étrangères après la suppression
$bdd->exec("SET foreign_key_checks = 1");

// Renvoyer sur la page dashboard
header('Location: dashboard.php');
exit; 

