<?php 

/**
 * update_article.php
 * Mise à jour d'un article en BDD
 */

 /**
  * 1. Seule une personne connectée peut y accéder
  * 2. Vérifier si la méthode du formulaire est bien "POST"
  * 3. Connexion à la base de données
  * 4. Récupérer et nettoyer les données
  * 5. Mise à jour du titre et du contenu de l'article dans la table "articles"
  * 6. Redirection vers le formulaire d'édition avec un message de succès
  */

// Démarrer une session
session_start();

// Charger les dépendances PHP
require_once '../vendor/autoload.php';

// Vérifie si l'utilisateur peut accéder à cette page (pour chaque page que l'on veut protéger)
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Vérification qu'il s'agit bien de la méthode POST
if($_SERVER['REQUEST_METHOD'] === "POST"){

    // Connexion à la base de données
    require_once '../connexion.php';
    $bdd = connectBdd('root', 'root', 'blog_db');

    // Nettoyer les données issues du formulaire
    $title = htmlspecialchars(strip_tags($_POST['title']));
    $content = htmlspecialchars(strip_tags($_POST['content']));
    $categories = array_map('strip_tags', $_POST['categories']);
    
    // Retirer les espaces en début et fin de chaine
    $title = trim($title);
    $content = trim($content);

    // Selection de l'ID
    $articleId = $_GET['id'];  

    // Vérifier si le formulaire est complet
    if (!empty($title) && !empty($content)) {

        // Sélectionner le nom de l'image actuellement en BDD
        $selectCoverQuery = $bdd->prepare("SELECT cover FROM articles WHERE id = :id");
        $selectCoverQuery->bindValue(':id', $articleId);
        $selectCoverQuery->execute();

        // Récupération de la valeur de la colonne et stockage de l'info dans une variable
        $cover = $selectCoverQuery->fetchColumn();

        // Vérifie si un upload doit être fait
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            
            $maxSize = 1 * 1024 * 1024;

            // Tableau contenant les extensions et les types MIMES autorisés
            $typeImage = [
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'webp' => 'image/webp'
            ];

            // Extraction de l'extension de l'image
            $extension = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));

            // Vérifier si le fichier est une image autorisée
            if (array_key_exists($extension, $typeImage) && in_array($_FILES['cover']['type'], $typeImage)) {

                //Vérifier si le poids est correct
                if ($_FILES['cover']['size'] <= $maxSize) {

                    // Supprime l'ancienne image
                    if (file_exists("../public/uploads/$cover")) {
                        // Supprime l'image à l'endroit indiqué
                        unlink("../public/uploads/$cover");
                    }

                    // Renomme le nom de l'image
                    $slugify = new \Cocur\Slugify\Slugify();
                    $newName = $slugify->slugify("$title-$articleId");
                    $cover = "$newName.$extension";

                    // Télécharge la nouvelle image sous le nouveau nom
                    move_uploaded_file(
                        $_FILES['cover']['tmp_name'],
                        "../public/uploads/$cover"
                    );

                } else {
                    $_SESSION['error'] = "Le poids de l'image ne doit pas excéder 1Mo";
                    header("Location: edit.php?id=$articleId");
                    exit;
                }
            } else {
                $_SESSION['error'] = "Votre fichier n'est pas une image autorisée";
                header("Location: edit.php?id=$articleId");
            }
        }
        
        // Mise à jour du titre et du contenu dans la base de données
        $updateTitle = $bdd->prepare("UPDATE articles SET title = :title, content = :content, cover = :cover WHERE id = :id");
        $updateTitle->bindValue(':title', $title);
        $updateTitle->bindValue(':content', $content);        
        $updateTitle->bindValue(':cover', $cover);        
        $updateTitle->bindValue(':id', $articleId);        
        $updateTitle->execute();

        // Mise à jour des catégories liées à l'article
        $deleteCategories = $bdd->prepare("DELETE FROM articles_categories WHERE article_id = :id");
        $deleteCategories->bindValue(':id', $articleId);
        $deleteCategories->execute();

        $insertCategoryQuery = $bdd->prepare("INSERT INTO articles_categories (article_id, category_id) VALUES (:article_id, :category_id)");

        foreach($categories as $category) {
            $insertCategoryQuery->bindValue(':article_id', $articleId);
            $insertCategoryQuery->bindValue(':category_id', $category);
            $insertCategoryQuery->execute();
        }

        //Message de succés
        $_SESSION['success'] = 'Les modifications ont bien été prise en compte';

    } else {
        $_SESSION['error'] = 'Tous les champs sont obligatoires';
    }

    // Redirection vers le formulaire d'édition
    header("Location: edit.php?id=$articleId");
    exit;

} else {
    header('Location: dashboard.php');
    exit;    
}
