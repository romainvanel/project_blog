<?php

// Démarrer une session
session_start();

// Vérification de l'existence de l'ID de l'article
if (empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

// Connexion à la base de données
require_once './connexion.php';
$bdd = connectBdd('root', 'root', 'blog_db');

// Sélection des informations concernant l'article
$selectArticle = $bdd->prepare("SELECT articles.id, articles.title, articles.content, articles.cover, articles.publication_date, users.name as author, GROUP_CONCAT(categories.name SEPARATOR ', ') AS categories FROM articles INNER JOIN articles_categories ON articles_categories.article_id = articles.id INNER JOIN categories ON categories.id = articles_categories.category_id INNER JOIN users ON users.id = articles.user_id WHERE articles.id = :articleId GROUP BY articles.id ");
$selectArticle->bindValue(':articleId', $_GET['id']);
$selectArticle->execute();

// Récupération des informations concernant l'article
$article = $selectArticle->fetch();

// Vérification de l'existence de l'article
if (!$article) {
    header('Location: index.php');
    exit;
}

// Créer un tableau de catégories en "explosant" la chaine de caractère créée par la requète SQL
$article['categories'] = explode(', ', $article['categories']);

// Sélection des commentaires
$selectComments = $bdd->prepare("SELECT comments.content, comments.comment_date, users.name as author FROM comments INNER JOIN users ON users.id = comments.user_id WHERE article_id = :articleId ORDER BY comments.comment_date DESC");
$selectComments->bindValue(':articleId', $_GET['id']);
$selectComments->execute();

// Récupération des commentaires
$comments = $selectComments->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Article</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <section class="d-flex flex-column w-50 m-auto justify-content-center">
        <article class="border p-3 m-3">
            <!-- Titre, auteur et date de publication de l'article -->
            <div>
                <h1>
                    <?php echo $article['title']; ?>
                </h1>
                <div>
                    <small class="d-flex justify-content-between">
                        <p>
                            Auteur : <?php echo $article['author']; ?>
                        </p>
                        <p>
                            <?php $createdAt = DateTime::createFromFormat('Y-m-d H:i:s', $article['publication_date']);
                            $date = $createdAt->format(("d.m.Y")) ?>
                            Publié le <?php echo $date; ?>
                        </p>
                    </small>
                </div>
            </div>

            <!-- Image de couverture -->
            <div>
                <?php if(file_exists("./public/uploads/{$article['cover']}")): ?>
                    <img src="./public/uploads/<?php echo $article['cover'];?>" class="card-img-top" alt="<?php echo $article['title']?>">
                <?php endif; ?>
            </div>

            <!-- Catégorie de l'article -->
            <div>
                <?php foreach ($article['categories'] as $category) : ?>
                    <a href=""><?php echo $category; ?></a>
                <?php endforeach; ?>
            </div>

            <!-- Contenu tronqué de l'article -->
            <div>
                <p>
                    <?php echo nl2br($article['content']); ?>
                </p>
            </div>

            <!-- lien vers l'article complet -->
            <div class="d-flex justify-content-end">
                <a href="#" class="btn btn-primary">Lire plus</a>
            </div>
        </article>
    </section>

    <section>
        <h2>Commentaires</h2>
        <?php if($comments):
            foreach($comments as $comment): ?>
                <div>
                    <p>
                        <?php $date = DateTime::createFromFormat('Y-m-d H:i:s', $comment['comment_date']);
                        $posteBy = $date->format('d.m.Y');?>
                        <?php echo $comment['author']; ?> le <?php echo $posteBy; ?>
                    </p>
                    <p>
                        <?php echo nl2br($comment['content']); ?>
                    </p>
                </div>
            <?php endforeach; else:?>
                <p>Soyez le premier à réagir à cette article</p>
            <?php endif ?>

            <div id="comments">
                <h3>Poster un commentaire</h3>

                <!-- Message de succès -->
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php 
                            echo $_SESSION['success']; 
                            unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Message d'erreurs' -->
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php 
                            echo $_SESSION['error']; 
                            unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['user'])) : ?>
                    <form action="add_comment.php?id=<? echo $article['id']; ?>" method="post">
                        <label for="comment" class="form-label">Commentaire</label>
                        <textarea name="comment" id="comment" rows="5" class="form-control"></textarea>
                        <button class="btn btn-primary">Poster mon commentaire</button>
                    </form>
                <?php else: ?>
                    <p>Veuillez vous connecter pour poster un commentaire</p>
                <?php endif; ?>
            </div>
    </section>
</body>
</html>