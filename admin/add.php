<?php

// Ajout d’un nouvel article :

// Ajouter un bouton sur la liste des articles redirigeant vers un formulaire similaire à l’édition. Tous les champs sont obligatoires et au moins une catégorie doit être sélectionnées.

// En cas d’erreur, afficher un message sur le formulaire, en cas de succès, redirigé vers la liste des articles avec un message.

// Réordrer les articles dans la listes des articles en les triant par date : du plus récents au plus vieux

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

// Sélection de toutes les catégories
$selectCategories = $bdd->query("SELECT * FROM categories");
$categories = $selectCategories->fetchALL();

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Administration - Edition</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    </head>
    <body>
        <div class="container-fluid text-center">
            <h1 class="p-3">Edition</h1>

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

            <div class="w-50 m-auto border rounded p-3">
                <form action="add_article.php" method="post" enctype="multipart/form-data" class="d-flex flex-column w-100">
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="title" name="title">
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Contenu</label>
                        <textarea class="form-control" id="content" name="content" rows="20"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="cover" class="form-label">Image de couverture</label>
                        <input class="form-control" type="file" id="cover" name="cover">
                    </div>
                    <div class="mb-3">
                        <label for="categories" class="form-label">State</label>
                        <select class="form-select" multiple aria-label="Multiple select example" id="categories" name="categories[]">
                        <?php foreach($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo $category['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-primary">Editer</button>

                </form>
            </div>
        </div>
    </body>
</html>