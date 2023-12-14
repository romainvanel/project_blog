<?php
// Démarrage de la session
session_start();

// Redirige l'utilisateur sur dashboard si il est déjà connecté
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Connexion à l'administration</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    </head>
    <body>
        <div class="container-fluid text-center">
            <h1 class="p-3">Connexion à l'administration</h1>
            <div class="w-50 m-auto border rounded p-3">
                <form action="login.php" method="post" class="d-flex flex-column w-100">
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse Email</label>
                        <input type="email" class="form-control" id="email" aria-describedby="emailHelp" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <button class="btn btn-primary">Se connecter</button>

                    <!-- Mes erreurs -->
                    <?php
                        if (isset($_SESSION['error'])):
                    ?>
                    <div class="alert alert-danger mt-3">
                        <?php echo $_SESSION['error']; ?>
                    </div>
                    <?php
                        unset($_SESSION['error']);
                        endif;
                    ?>  

                </form>
            </div>
        </div>
    </body>
</html>