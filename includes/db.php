<?php
$host = '127.0.0.1'; // Лучше использовать IP вместо localhost
$port = '3307';      // Ваш нестандартный порт
$dbname = 'creative_archive';
$username = 'root';
$password = '';      // Пустой пароль для XAMPP
function get_works_count($author_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE author_id = ?");
    $stmt->execute([$author_id]);
    return $stmt->fetchColumn();
}

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", 
        $username, 
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage() . 
        " (Проверьте host: $host, port: $port)");
}
?>

