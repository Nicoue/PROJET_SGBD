<?php
session_start();
include_once 'db.php';

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Vérifier si l'ID de l'utilisateur à modifier est fourni
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Requête pour récupérer les informations de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "Utilisateur non trouvé.";
        exit();
    }
} else {
    echo "ID de l'utilisateur non fourni.";
    exit();
}

// Mettre à jour les informations de l'utilisateur
if (isset($_POST['update'])) {
    $role = $_POST['role'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($password) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET  role = :role, nom = :nom, prenom = :prenom, email = :email, password = :password WHERE id = :id");
        $stmt->execute([ 'role' => $role, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'password' => $password, 'id' => $userId]);
    } else {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET  role = :role, nom = :nom, prenom = :prenom, email = :email WHERE id = :id");
        $stmt->execute([ 'role' => $role, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'id' => $userId]);
    }

    // Redirection vers la liste des utilisateurs
    header('Location: list_users.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Utilisateur</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            color: #333;
        }
        input, select {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Modifier Utilisateur</h1>
        <form action="edit_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" method="post">
            <label for="role">Rôle:</label>
            <select name="role" id="role" required>
                <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Administrateur</option>
                <option value="enseignant" <?php if ($user['role'] == 'enseignant') echo 'selected'; ?>>Enseignant</option>
                <option value="etudiant" <?php if ($user['role'] == 'etudiant') echo 'selected'; ?>>Étudiant</option>
            </select>

            <label for="nom">Nom:</label>
            <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>

            <label for="prenom">Prénom:</label>
            <input type="text" name="prenom" id="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="password">Mot de passe (laissez vide pour ne pas changer):</label>
            <input type="password" name="password" id="password">

            <button type="submit" name="update">Mettre à jour</button>
        </form>
    </div>
</body>
</html>
