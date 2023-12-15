<?php

// Démarrer une session
session_start();

// Vérifie si l'utilisateur peut accéder à cette page (pour chaque page que l'on veut protéger)
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Connexion à la base de données
require_once '../connexion.php';
$bdd = connectBdd('root', 'root', 'blog_db');

// Sélectionne tous les articles avec leurs catégories
$selectArticles = $bdd->prepare("SELECT articles.id, articles.title, articles.publication_date, GROUP_CONCAT(categories.name SEPARATOR ', ') AS categories FROM articles LEFT JOIN articles_categories ON articles_categories.article_id = articles.id LEFT JOIN categories ON categories.id = articles_categories.category_id 
WHERE user_id = :id GROUP BY articles.id;");
$selectArticles->bindValue(':id', $_SESSION['user']['id']);
$selectArticles->execute();

$articles = $selectArticles->fetchAll();
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
        <h1>Administration</h1>
        <a href="logout.php">Déconnexion</a>

                    <!-- Message de succès -->
                    <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo $_SESSION['success']; 
                        unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Date de publication</th>
                    <th colspan="2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($articles as $article): 
                $_SESSION['articleID'] = "articleID"; 
                ?>
                    <tr>
                        <td><?php echo $article['id'];?></td>
                        <td><?php echo $article['title'];?></td>
                        <td><?php echo $article['categories'];?></td>
                        <td>
                            <?php 
                            // Formatage de la date
                                // On analyse la date que l'on a récupéré
                                $date = DateTime::createFromFormat('Y-m-d H:i:s', $article['publication_date']);
                                // Format de sortie
                                echo $date->format('d.m.Y');
                            ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $article['id'];?>" class="btn btn-outline-success fw-bold">Editer</a>
                        </td>
                        <td>
                            <a href="delete_article.php?id=<?php echo $article['id'];?>" class="btn btn-outline-danger fw-bold" onclick="return confirmSuppression()">Supprimer</a>
                        </td>

                        <!-- Fonction JS pour afficher une confirmation de suppression -->
                        <script>
                        function confirmSuppression() {
                            return confirm("Êtes-vous sûr de vouloir supprimer cet enregistrement ?");
                        }
                        </script>

                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>

    </body>
</html>