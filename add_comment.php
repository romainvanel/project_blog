<?php

// Démarrer une session
session_start();


// Vérifie si l'utilisateur peut accéder à cette page (pour chaque page que l'on veut protéger)
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Vérifie si le paramètre "id" est présent et/ou non vide
if (empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

// Connexion à la base de données
require_once './connexion.php';
$bdd = connectBdd('root', 'root', 'blog_db');

// Vérifie si un article existe sous cet ID
$query = $bdd->prepare("SELECT id FROM articles WHERE id = :id");
$query->bindValue('id', $_GET['id']);
$query->execute();

$article = $query->fetch();

// Si l'article n'existe pas, redirection vers la page d'accueil
if (!$article) {
    header('Location: index.php');
    exit;
}

// Nettoyer les données issues du formulaire
$comments = htmlspecialchars(strip_tags($_POST['comment']));

// Retirer les espaces en début et fin de chaine
$comments = trim($comments);

// Vérification qu'il s'agit bien de la méthode POST
if($_SERVER['REQUEST_METHOD'] === "POST"){

    if (!empty($comments) ) {

        // Insertion du commentaire en BDD
        $inserComment = $bdd->prepare("INSERT INTO comments (content, comment_date, user_id, article_id) VALUES (:content, :comment_date, :user_id, :article_id)");
        $inserComment->bindValue(":content", $comments);
        $inserComment->bindValue(":comment_date", (new DateTime())->format('Y-m-d H:i:s'));
        $inserComment->bindValue(":user_id", $_SESSION['user']['id']);
        $inserComment->bindValue(":article_id", $_GET['id']);
        $inserComment->execute();

        $_SESSION['success'] = 'Votre commentaire à été correctement publié';

    } else {
        $_SESSION['error'] = "Veuillez écrire un commentaire";
    }

} else {
    header("Location: index.php");
    exit;
}

header("Location: article.php?id={$_GET['id']}#comments");
exit;
