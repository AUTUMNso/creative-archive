<?php include 'includes/header.php'; ?>

<section class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">Добро пожаловать в творческое сообщество</h1>
        <p class="hero-subtitle">Откройте для себя лучшие работы современных художников и дизайнеров</p>
        <a href="gallery.php" class="btn">Смотреть все работы</a>
    </div>
</section>

<section class="main-container">
    <h2 class="section-title">Популярные авторы</h2>
    <div class="grid grid-columns">
        <?php
        include 'includes/db.php';
        $stmt = $pdo->query("SELECT * FROM authors ORDER BY RAND() LIMIT 4");
        while ($author = $stmt->fetch()) {
            echo '<div class="card">
                <div class="card-image-wrapper">
                    <img src="images/avatars/'.$author['avatar'].'" 
                         class="card-image"
                         alt="'.$author['name'].'">
                </div>
                <div class="card-content">
                    <h3>'.$author['name'].'</h3>
                    <div class="author-info">
                        <a href="author.php?id='.$author['id'].'" class="btn">Профиль →</a>
                    </div>
                </div>
            </div>';
        }
        ?>
    </div>

    <h2 class="section-title">Свежие работы</h2>
    <div class="grid grid-columns">
        <?php
        $stmt = $pdo->query("SELECT p.*, a.name AS author_name, a.avatar AS author_avatar 
                            FROM projects p
                            JOIN authors a ON p.author_id = a.id
                            ORDER BY p.created_at DESC LIMIT 6");
        while ($project = $stmt->fetch()) {
            echo '<div class="card">
                <div class="card-image-wrapper">
                    <img src="images/uploads/'.$project['image'].'" 
                         class="card-image"
                         alt="'.$project['title'].'">
                </div>
                <div class="card-content">
                    <h3>'.$project['title'].'</h3>
                    <div class="card-author">
                        <img src="images/avatars/'.$project['author_avatar'].'" 
                             class="author-mini-avatar">
                        <a href="author.php?id='.$project['author_id'].'" 
                           class="author-name">'.$project['author_name'].'</a>
                    </div>
                </div>
            </div>';
        }
        ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>