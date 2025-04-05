<?php 
include 'includes/db.php';
$current_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
?>

<?php include 'includes/header.php'; ?>

<section class="main-container">
    <div class="category-filters flex-center">
        <?php
        $stmt = $pdo->query("SELECT * FROM categories");
        while ($category = $stmt->fetch()) {
            echo '<a href="gallery.php?category='.$category['id'].'" 
                    class="nav-link '.($current_category == $category['id'] ? 'active' : '').'">
                    '.$category['name'].'
                  </a>';
        }
        ?>
        <a href="gallery.php" 
           class="nav-link <?= !$current_category ? 'active' : '' ?>">
            Все категории
        </a>
    </div>
    </div>

    <div class="grid grid-columns">
        <?php
        $sql = "SELECT p.*, a.name AS author_name, a.avatar AS author_avatar 
                FROM projects p
                JOIN authors a ON p.author_id = a.id";

        if ($current_category) {
            $sql .= " WHERE p.category_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$current_category]);
        } else {
            $stmt = $pdo->query($sql);
        }

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