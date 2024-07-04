<?php
session_start();
include_once 'db.php';

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Requête pour récupérer tous les utilisateurs
$sql = "SELECT * FROM utilisateurs";
$result = $pdo->query($sql);

$users = [];
if ($result->rowCount() > 0) {
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $users[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Utilisateurs</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f9;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            color: #fff;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-edit {
            background-color: #4CAF50;
        }
        .btn-edit:hover {
            background-color: #45a049;
        }
        .btn-delete {
            background-color: #f44336;
        }
        .btn-delete:hover {
            background-color: #da190b;
        }
        .btn-custom {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            text-align: center;
            border: none;
            border-radius: 5px;
            display: inline-block;
            transition: background-color 0.3s;
            margin: 20px 0;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Liste des Utilisateurs</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['nom']); ?></td>
                        <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td class="action-buttons">
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-edit">Modifier</a>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="dashboard_admin.php" class="btn-custom">Ajouter un utilisateur</a>
    </div>
</body>
</html>
