<?php
session_start();
include_once 'db.php';

// Vérifier si l'utilisateur est connecté et est un étudiant
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'etudiant') {
    header('Location: login.php');
    exit();
}

$etudiant_id = $_SESSION['user_id'];

// Requête pour récupérer le nom de l'étudiant
$sql = "SELECT nom, prenom FROM etudiants WHERE id = :etudiant_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['etudiant_id' => $etudiant_id]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
$etudiant_nom = $etudiant['prenom'] . ' ' . $etudiant['nom'];

// Requête pour récupérer les cours et les notes de l'étudiant
$sql = "SELECT c.nom as cours_nom, n.note 
        FROM cours c
        JOIN cours_etudiants ce ON c.id = ce.cours_id
        JOIN notes n ON n.cours_id = ce.cours_id AND n.etudiant_id = ce.etudiant_id
        WHERE ce.etudiant_id = :etudiant_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['etudiant_id' => $etudiant_id]);
$cours_notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Étudiant</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../frontend/css/dashboard_etudiant.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Tableau de Bord Étudiant</h1>
        <div class="student-name text-center mt-4">Bienvenue, <?php echo htmlspecialchars($etudiant_nom); ?> !</div>
        <div class="table-responsive mt-5">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Matière</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cours_notes as $cours_note): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cours_note['cours_nom']); ?></td>
                            <td><?php echo htmlspecialchars($cours_note['note']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="text-center mt-4">
            <a href="logout.php" class="btn btn-danger">Déconnexion</a>
        </div>
    </div>
</body>
</html>
