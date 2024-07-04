<?php
session_start();
include_once 'db.php';

// Vérifier si l'utilisateur est connecté et est un enseignant
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'enseignant') {
    header('Location: login.php');
    exit();
}

$enseignant_id = $_SESSION['user_id'];

// Requête pour récupérer le nom de l'enseignant
$sql = "SELECT nom, prenom FROM enseignants WHERE id = :enseignant_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['enseignant_id' => $enseignant_id]);
$enseignant = $stmt->fetch(PDO::FETCH_ASSOC);
$enseignant_nom = $enseignant['prenom'] . ' ' . $enseignant['nom'];

// Requête pour récupérer les cours et les classes de l'enseignant
$sql = "SELECT c.id as classe_id, c.nom as classe_nom, co.id as cours_id, co.nom as cours_nom 
        FROM classes c
        JOIN cours_classes cc ON c.id = cc.classe_id
        JOIN cours co ON co.id = cc.cours_id
        JOIN cours_enseignants ce ON ce.cours_id = co.id
        WHERE ce.enseignant_id = :enseignant_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['enseignant_id' => $enseignant_id]);
$enseignant_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$classes = [];
foreach ($enseignant_data as $data) {
    $classes[$data['classe_id']]['nom'] = $data['classe_nom'];
    $classes[$data['classe_id']]['cours'][$data['cours_id']] = $data['cours_nom'];
}

// Requête pour récupérer les étudiants et leurs notes
$etudiants = [];
foreach ($classes as $classe_id => $classe_data) {
    foreach ($classe_data['cours'] as $cours_id => $cours_nom) {
        $sql = "SELECT e.id as etudiant_id, e.nom as etudiant_nom, e.prenom as etudiant_prenom, n.note 
                FROM etudiants e
                JOIN cours_etudiants ce ON e.id = ce.etudiant_id
                JOIN notes n ON n.etudiant_id = e.id AND n.cours_id = ce.cours_id
                WHERE ce.cours_id = :cours_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cours_id' => $cours_id]);
        $etudiants[$classe_id][$cours_id] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Enseignant</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1, h2, h3 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .teacher-name {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
            font-size: 1.5em;
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
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 10px 20px;
            margin-top: 20px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .form-inline {
            display: flex;
            flex-direction: row;
            align-items: center;
            margin-top: 10px;
        }
        .form-inline label, .form-inline input {
            margin-right: 10px;
        }
        .form-inline input[type="number"] {
            width: 80px;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .table-responsive {
            overflow-x: auto;
        }
    </style>
    <script>
        function updateGrade(event) {
            event.preventDefault();
            const form = event.target;
            const etudiant_id = form.etudiant_id.value;
            const cours_id = form.cours_id.value;
            const note = form.note.value;

            fetch('update_grade.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    etudiant_id: etudiant_id,
                    cours_id: cours_id,
                    note: note
                })
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    const noteCell = form.closest('tr').querySelector('.note');
                    noteCell.textContent = note;
                    alert('Note mise à jour avec succès');
                } else {
                    alert('Erreur lors de la mise à jour de la note');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Dashboard Enseignant</h1>
        <div class="teacher-name">Bienvenue, <?php echo htmlspecialchars($enseignant_nom); ?> !</div>
        <?php foreach ($classes as $classe_id => $classe_data): ?>
            <h2>Classe: <?php echo htmlspecialchars($classe_data['nom']); ?></h2>
            <?php foreach ($classe_data['cours'] as $cours_id => $cours_nom): ?>
                <h3>Cours: <?php echo htmlspecialchars($cours_nom); ?></h3>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Note</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($etudiants[$classe_id][$cours_id])): ?>
                                <?php foreach ($etudiants[$classe_id][$cours_id] as $etudiant): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($etudiant['etudiant_nom']); ?></td>
                                        <td><?php echo htmlspecialchars($etudiant['etudiant_prenom']); ?></td>
                                        <td class="note"><?php echo htmlspecialchars($etudiant['note']); ?></td>
                                        <td>
                                            <form onsubmit="updateGrade(event)" class="form-inline">
                                                <input type="hidden" name="etudiant_id" value="<?php echo $etudiant['etudiant_id']; ?>">
                                                <input type="hidden" name="cours_id" value="<?php echo $cours_id; ?>">
                                                <input type="number" name="note" value="<?php echo $etudiant['note']; ?>" min="0" max="20" required>
                                                <button type="submit" class="btn btn-success">Mettre à jour</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">Aucun étudiant inscrit dans ce cours.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <a href="logout.php" class="btn">Déconnexion</a>
    </div>
</body>
</html>
