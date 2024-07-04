<?php
include_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $etudiant_id = $_POST['etudiant_id'];
    $cours_id = $_POST['cours_id'];
    $note = $_POST['note'];

    // Update or insert the grade
    $sql = "INSERT INTO notes (etudiant_id, cours_id, note) VALUES (:etudiant_id, :cours_id, :note)
            ON DUPLICATE KEY UPDATE note = :note";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute(['etudiant_id' => $etudiant_id, 'cours_id' => $cours_id, 'note' => $note])) {
        echo 'success';
    } else {
        echo 'error';
    }
}

