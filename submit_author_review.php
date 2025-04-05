<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author_id = (int)$_POST['author_id'];
    $user_name = htmlspecialchars(trim($_POST['name']));
    $comment = htmlspecialchars(trim($_POST['comment']));

    $stmt = $pdo->prepare("INSERT INTO author_reviews (author_id, user_name, comment) VALUES (?, ?, ?)");
    $stmt->execute([$author_id, $user_name, $comment]);

    header("Location: author.php?id=$author_id&comment_success=1");
    exit;
}
?>