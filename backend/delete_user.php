<?php
session_start();
include_once 'db.php';

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Vérifier si l'ID de l'utilisateur à supprimer est fourni
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Récupérer le rôle de l'utilisateur
    $stmt = $pdo->prepare("SELECT role FROM utilisateurs WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();

    if ($user) {
        // Début de la transaction
        $pdo->beginTransaction();

        try {
            // Supprimer les entrées associées dans les tables enfants
            switch ($user['role']) {
                case 'enseignant':
                    $stmt = $pdo->prepare("DELETE FROM enseignants WHERE id = :id");
                    $stmt->execute(['id' => $userId]);
                    break;
                case 'etudiant':
                    $stmt = $pdo->prepare("DELETE FROM etudiants WHERE id = :id");
                    $stmt->execute(['id' => $userId]);
                    break;
                case 'admin':
                    $stmt = $pdo->prepare("DELETE FROM admins WHERE id = :id");
                    $stmt->execute(['id' => $userId]);
                    break;
            }

            // Supprimer l'utilisateur
            $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = :id");
            $stmt->execute(['id' => $userId]);

            // Valider la transaction
            $pdo->commit();

            // Redirection vers la liste des utilisateurs
            header('Location: list_users.php');
            exit();
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $pdo->rollBack();
            echo "Erreur lors de la suppression de l'utilisateur : " . $e->getMessage();
        }
    } else {
        echo "Utilisateur non trouvé.";
    }
} else {
    echo "ID de l'utilisateur non fourni.";
}

