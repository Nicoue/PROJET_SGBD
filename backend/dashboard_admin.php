<?php
include_once 'db.php';

// Initialisation des variables pour la modification
$editUser = null;
$prenom = '';
$nom = '';
$email = '';
$role = '';
$userId = null;
$errorMsg = '';

// Ajouter ou modifier un utilisateur
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        // Ajouter un nouvel utilisateur
        $prenom = $_POST['prenom'];
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        // Vérifier si l'email existe déjà
        $query = "SELECT COUNT(*) AS count FROM utilisateurs WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['count'] > 0) {
            //Si L'email existe déjà, afficher un message d'erreur
            $errorMsg = 'Erreur : Cet email est déjà utilisé.';
        } else {
            // Insérer l'utilisateur dans la base de données
            $query = "INSERT INTO utilisateurs (password, role, nom, prenom, email) VALUES (:password, :role, :nom, :prenom, :email)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':password' => $password, ':role' => $role, ':nom' => $nom, ':prenom' => $prenom, ':email' => $email]);
            
            // Réinitialiser les champs du formulaire après l'ajout
            $prenom = '';
            $nom = '';
            $email = '';
            $role = '';

        }
    } elseif (isset($_POST['edit_user'])) {
        // Modifier un utilisateur existant
        $userId = $_POST['user_id'];
        $prenom = $_POST['prenom'];
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        // Vérifier si l'email existe déjà pour un autre utilisateur
        $query = "SELECT COUNT(*) AS count FROM utilisateurs WHERE email = :email AND id != :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':email' => $email, ':id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['count'] > 0) {
            // L'email existe déjà pour un autre utilisateur, afficher un message d'erreur
            $errorMsg = 'Erreur : Cet email est déjà utilisé par un autre utilisateur.';
        } else {
            // Mettre à jour l'utilisateur dans la base de données
            $query = "UPDATE utilisateurs SET password = :password, role = :role, nom = :nom, prenom = :prenom WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':prenom' => $prenom, ':nom' => $nom, ':email' => $email, ':role' => $role, ':id' => $userId]);

            // Réinitialiser les variables après la modification
            $userId = null;
            $prenom = '';
            $nom = '';
            $email = '';
            $role = '';
        }
    }
}

// Récupérer l'utilisateur à modifier
if (isset($_GET['edit'])) {
    $userId = $_GET['edit'];
    $query = "SELECT * FROM utilisateur WHERE ID = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $userId]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($editUser) {
        $prenom = $editUser['prenom'];
        $nom = $editUser['nom'];
        $email = $editUser['email'];
        $role = $editUser['role'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les utilisateurs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f0f0;
        }

        .user_container {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            margin: 50px auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .user_container h2 {
            color: #ecf0f1;
            text-align: center;
            margin-bottom: 20px;
        }

        .input-box label {
            color: #ecf0f1;
        }

        .input-box input,
        .input-box select {
            background-color: #34495e;
            border: none;
            padding: 10px;
            border-radius: 5px;
            color: #ecf0f1;
            width: 100%;
        }

        .btn {
            background-color: #2eb0f0;
            color: #fff;
            border: none;
            cursor: pointer;
            text-align: center;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
            width: 100%;
        }

        .btn:hover {
            background-color: #1a90c4;
        }

        .text-center a {
            background-color: #2eb0f0;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .text-center a:hover {
            background-color: #1a90c4;
        }
        .btn1 {
            padding: 10px 20px;
            margin-top: 20px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
    </style>
</head>
<body>
    <div class="user_container">
        <h2>Ajouter un utilisateur</h2>

        <?php if (!empty($errorMsg)): ?>
            <div style="color: red; text-align: center; margin-bottom: 10px;"><?php echo $errorMsg; ?></div>
        <?php endif; ?>

        <!-- Formulaire d'ajout ou de modification d'utilisateur -->
        <form id="user-form" action="dashboard_admin.php" method="post">
            <?php if ($editUser): ?>
                <input type="hidden" name="edit_user" value="1">
                <input type="hidden" name="user_id" value="<?php echo $editUser['ID']; ?>">
            <?php else: ?>
                <input type="hidden" name="add_user" value="1">
            <?php endif; ?>

            <div class="input-box">
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" placeholder="Prénom" required value="<?php echo htmlspecialchars($prenom); ?>">
            </div>

            <div class="input-box">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" placeholder="Nom" required value="<?php echo htmlspecialchars($nom); ?>">
            </div>

            <div class="input-box">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($email); ?>">
            </div>

            <?php if (!$editUser): ?>
                <div class="input-box">
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" placeholder="Mot de passe" required>
                </div>
            <?php endif; ?>

            <div class="input-box">
                <label for="role">Role :</label>
                <select id="role" name="role" required>
                    <option value="admin" <?php echo $role == 'admin' ? 'selected' : ''; ?>>admin</option>
                    <option value="enseignant" <?php echo $role == 'enseignant' ? 'selected' : ''; ?>>enseignant</option>
                    <option value="etudiant" <?php echo $role == 'etudiant' ? 'selected' : ''; ?>>etudiant</option>
                </select>
            </div>

            <button type="submit" class="btn"><?php echo $editUser ? 'Modifier l\'utilisateur' : 'Ajouter l\'utilisateur'; ?></button>
        </form>
        <p class="text-center"><a href="list_users.php" class="btn">Voir la liste des utilisateurs</a></p>
        <a href="logout.php" class="btn1">Déconnexion</a>
    </div>
</body>
</html>