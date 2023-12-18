<?php

// Connexion à la base de données
require_once './connexion.php';
$bdd = connectBdd('root', 'root', 'blog_db');

// Sélection des informations concernant l'article
$selectArticles = $bdd->query("SELECT articles.title, articles.content, articles.cover, articles.publication_date, users.name as author, GROUP_CONCAT(categories.name SEPARATOR ', ') AS categories FROM articles LEFT JOIN articles_categories ON articles_categories.article_id = articles.id LEFT JOIN categories ON categories.id = articles_categories.category_id LEFT JOIN users ON users.id = articles.user_id GROUP BY articles.id ORDER BY articles.publication_date DESC");
$selectArticles->execute();

$articles = $selectArticles->fetchAll();

// Créer un tableau de catégories en "explosant" la chaine de caractère créée par la requète SQL

$groupedArticles = [];
foreach ($articles as $key => $article) {
    $groupedArticles[$key] = $article;
    $groupedArticles[$key]['categories'] = explode(', ', $article['categories']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Administration</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <div class="d-flex flex-column w-50 m-auto justify-content-center">
        <?php foreach($groupedArticles as $article): ?>
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
                        <img src="<?php echo "./public/uploads/{$article['cover']}";?>" class="card-img-top" alt="<?php echo $article['title']?>">
                    <?php endif; ?>
                </div>

                <!-- Catégorie de l'article -->
                <div>
                    <ul class="d-flex gap-3 list-unstyled">
                        <?php foreach ($article['categories'] as $category) : ?>
                            <li>
                                <a href=""><?php echo $category; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Contenu tronqué de l'article -->
                <div>
                    <p>
                        <?php echo mb_strimwidth($article['content'], 0, 100, '...'); ?>
                    </p>
                </div>

                <!-- lien vers l'article complet -->
                <div class="d-flex justify-content-end">
                    <a href="#" class="btn btn-primary">Lire plus</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</body>
</html>