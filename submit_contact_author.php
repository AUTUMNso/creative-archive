<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author_id = (int)$_POST['author_id'];
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    $stmt = $pdo->prepare("
        INSERT INTO author_messages 
        (author_id, name, email, message) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$author_id, $name, $email, $message]);

    header("Location: author.php?id=$author_id&message_success=1");
    exit;
}
?>