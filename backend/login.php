<?php
session_start();
include_once 'db.php';

if (isset($_POST['valider'])) {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $erreur = "";

        // Requête pour sélectionner l'utilisateur
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email AND password = :password");
        $stmt->execute(['email' => $email, 'password' => $password]);
        $user = $stmt->fetch();

        if ($user) {
            // Définir les variables de session
            $_SESSION['user_id'] = $user['id'];  
            $_SESSION['user_type'] = $user['role'];
            $_SESSION['email'] = $user['email'];

            // Rediriger en fonction du type d'utilisateur
            if ($user['role'] == 'admin') {
                header('Location: dashboard_admin.php');
                exit();
            } elseif ($user['role'] == 'enseignant') {
                header('Location: dashboard_enseignant.php');
                exit();
            } elseif ($user['role'] == 'etudiant') {
                header('Location: dashboard_etudiant.php');
                exit();
            } else {
                $erreur = "Type d'utilisateur inconnu.";
            }
        } else {
            $erreur = "Adresse Mail ou Mot de passe incorrects !";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/css/login.css">
</head>
<body>
    <div class="container">
        <h2>Connectez-vous avec votre compte</h2>
        <?php if (isset($erreur) && $erreur != ""): ?>
            <p style="color: red;"><?php echo $erreur; ?></p>
        <?php endif; ?>
        <form action="login.php" method="post">
            <div class="input-box">
                <input type="email" id="email" name="email" placeholder="Email" required>
                <i class="bx bxs-user"></i>
            </div>
            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Mot de passe" required>
                <i class="bx bxs-lock-alt"></i>
            </div>
            <button class="btn" type="submit" name="valider">Se connecter</button>
        </form>
    </div>
</body>
</html>
