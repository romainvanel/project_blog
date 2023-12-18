<?php

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
    $cover = $_FILES['cover'];
    
    // Retirer les espaces en début et fin de chaine
    $title = trim($title);
    $content = trim($content);

    // Vérifier si le formulaire est complet
    if (!empty($title) && !empty($content) && !empty($_POST['categories']) && (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK)) {

        // Nettoyage des catégories selectionnées
        $categories = array_map('strip_tags', $_POST['categories']);

        // Définir le poids max de l'image
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

                // Insérer en BDD les données
                $queryNewArticle = $bdd->prepare("INSERT INTO articles (title, content, cover, publication_date, user_id) VALUES (:title, :content, :cover, :publication_date, :user_id)");
                $queryNewArticle->bindValue(':title', $title);
                $queryNewArticle->bindValue(':content', $content);        
                $queryNewArticle->bindValue(':cover', $cover['name']);
                // Récupère la date et l'heure du jour : new DateTime('now') OU new DateTime()        
                $queryNewArticle->bindValue(':publication_date', (new DateTime('now'))->format('Y-m-d H:i:s'));        
                $queryNewArticle->bindValue(':user_id', $_SESSION['user']['id']);        
                $queryNewArticle->execute();

                // Récupérer l'ID de l'article nouvellement crée
                $articleId = $bdd->lastInsertId();


                // Renomme le nom de l'image
                $slugify = new \Cocur\Slugify\Slugify();
                $newName = $slugify->slugify("$title-$articleId");
                $cover = "$newName.$extension";   
                
                // Télécharge la nouvelle image sous le nouveau nom
                move_uploaded_file(
                    $_FILES['cover']['tmp_name'],
                    "../public/uploads/$cover"
                );   
                
                /**
                 * Met à jour le nom de l'image dans la BDD
                 * On met à jour le nom de l'image après une insertion, car notre image contient l'ID de l'article que l'on ne puet pas connaitre au moment de l'insertionn plus haut, donc cela doit se faire en 2 temps
                 */
                $queryUpdateCover = $bdd->prepare("UPDATE articles SET cover = :cover WHERE id = :id");
                $queryUpdateCover->bindValue('cover', $cover);
                $queryUpdateCover->bindValue('id', $articleId);
                $queryUpdateCover->execute();

                // Insertion dans la table de relation "articles_categories"
                $queryInsertRelationCategory = $bdd->prepare("INSERT INTO articles_categories (article_id, category_id) VALUES (:article_id, :category_id)");

                foreach($categories as $category) {
                    $queryInsertRelationCategory->bindValue(':article_id', $articleId);
                    $queryInsertRelationCategory->bindValue(':category_id', $category);
                    $queryInsertRelationCategory->execute();
                }


            } else {
                $_SESSION['error'] = "Le poids de l'image ne doit pas excéder 1Mo";
                header("Location: add.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Votre fichier n'est pas une image autorisée";
            header("Location: add.php");
        }

        //Message de succés
        $_SESSION['success'] = 'Les modifications ont bien été prise en compte';

    } else {
        $_SESSION['error'] = 'Tous les champs sont obligatoires';
        header("Location: add.php");
        exit;

    }
}

header('Location: dashboard.php');
exit;    
