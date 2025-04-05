<?php 
include 'includes/db.php';
include 'includes/header.php'; 
?>

<section class="main-container">
    <h1 class="section-title">Все авторы</h1>
    
    <div class="grid grid-columns">
        <?php
        $stmt = $pdo->query("
            SELECT authors.*, 
            (SELECT COUNT(*) FROM projects WHERE author_id = authors.id) as works_count 
            FROM authors
        ");
        while ($author = $stmt->fetch()) {
            echo '<div class="card">
                <div class="card-image-wrapper">
                    <img src="images/avatars/'.$author['avatar'].'" 
                         class="card-image"
                         alt="'.$author['name'].'">
                </div>
                <div class="card-content">
                    <h3>'.$author['name'].'</h3>
                    <div class="author-stats">
                        <span> Количество работ: '.$author['works_count'].'</span>
                    </div>
                    <div class="author-info">
                        <a href="author.php?id='.$author['id'].'" class="btn">Профиль →</a>
                    </div>
                </div>
            </div>';
        }
        ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>