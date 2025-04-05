<?php 
include 'includes/db.php';
$author_id = $_GET['id'] ?? 0;
$author = $pdo->prepare("SELECT * FROM authors WHERE id = ?");
$author->execute([$author_id]);
$author = $author->fetch();

include 'includes/header.php'; 
?>

<section class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <img src="images/avatars/<?= $author['avatar'] ?>" alt="<?= $author['name'] ?>">
        </div>
        
        <div class="profile-info">
            <h1 class="profile-name"><?= $author['name'] ?></h1>
            
            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-label">количество работ:</span>
                    <span class="stat-count"><?= get_works_count($author_id) ?></span>
                </div>
            </div>
            
            <div class="profile-bio">
                <?= nl2br($author['bio']) ?>
            </div>
        </div>
    </div>

    <!-- Форма связи с автором (существующая) -->
    <div class="contact-author-form">
        <h3 class="form-title">Связаться с автором</h3>
        <form action="submit_contact_author.php" method="POST">
            <input type="hidden" name="author_id" value="<?= $author_id ?>">
            
            <div class="input-group">
                <input type="text" name="name" 
                       class="input-field" 
                       placeholder="Ваше имя" required>
            </div>
            
            <div class="input-group">
                <input type="email" name="email" 
                       class="input-field" 
                       placeholder="Ваш Email" required>
            </div>
            
            <div class="input-group">
                <textarea name="message" 
                          class="input-field" 
                          rows="4"
                          placeholder="Ваше сообщение..." 
                          style="resize: vertical;"
                          required></textarea>
            </div>
            
            <button type="submit" class="btn">Отправить сообщение</button>
        </form>
    </div>

    <!-- author.php (после секции с работами автора) -->
    <div class="category-filters flex-center">
        <?php
        $current_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
        $categories = $pdo->query("SELECT * FROM categories");
        foreach ($categories as $category) {
            echo '<a href="author.php?id='.$author_id.'&category='.$category['id'].'#gallery" 
                    class="nav-link '.($current_category == $category['id'] ? 'active' : '').'">
                    '.$category['name'].'
                  </a>';
        }
        ?>
        <a href="author.php?id=<?= $author_id ?>#gallery" 
           class="nav-link <?= !$current_category ? 'active' : '' ?>">
            Все категории
        </a>
    </div>

    <!-- Галерея работ -->
    <div id="gallery" class="grid grid-columns">
        <?php
        $sql = "SELECT * FROM projects WHERE author_id = :author_id";
        if ($current_category > 0) {
            $sql .= " AND category_id = :category_id";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':author_id', $author_id, PDO::PARAM_INT);
        
        if ($current_category > 0) {
            $stmt->bindValue(':category_id', $current_category, PDO::PARAM_INT);
        }

        $stmt->execute();

        while ($project = $stmt->fetch()) {
            echo '<div class="card">
            <div class="card-image-wrapper">
                <img src="images/uploads/'.$project['image'].'" 
                     class="card-image"
                     alt="'.$project['title'].'">
            </div>
            <div class="card-content">
                <h3>'.$project['title'].'</h3>
                ' . ($project['description'] ? '<p class="project-description">'.$project['description'].'</p>' : '') . '
            </div>
        </div>';
    }
    
    // Если работ нет
    if ($stmt->rowCount() === 0) {
        echo '<p class="no-results">Нет работ в выбранной категории</p>';
    }
    ?>
</div>

    <div id="imageModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="expandedImg">
    </div>
    <script>
    // Открытие изображения
document.querySelectorAll('.card-image').forEach(img => {
    img.onclick = function() {
        document.getElementById('expandedImg').src = this.src;
        document.getElementById('imageModal').style.display = "block";
    }
});

// Закрытие модального окна
document.querySelector('.close').onclick = function() {
    document.getElementById('imageModal').style.display = "none";
}

// Закрытие при клике вне изображения
window.onclick = function(event) {
    if (event.target == document.getElementById('imageModal')) {
        document.getElementById('imageModal').style.display = "none";
    }
}
</script>

    <!-- Новая форма для комментариев -->
    <div class="comment-form">
        <h3 class="form-title">Оставить комментарий</h3>
        <form action="submit_author_review.php" method="POST">
            <input type="hidden" name="author_id" value="<?= $author_id ?>">
            
            <div class="input-group">
                <input type="text" name="name" 
                       class="input-field" 
                       placeholder="Ваше имя" required>
            </div>
            
            <div class="input-group">
                <textarea name="comment" 
                          class="input-field" 
                          rows="4"
                          placeholder="Ваш комментарий..."
                          style="resize: vertical;"
                          required></textarea>
            </div>
            
            <button type="submit" class="btn">Отправить комментарий</button>
        </form>
    </div>

    <!-- Список комментариев -->
    <div class="author-reviews">
        <h3 class="section-title">Комментарии</h3>
        <?php
        $reviews = $pdo->prepare("SELECT * FROM author_reviews WHERE author_id = ? ORDER BY created_at DESC");
        $reviews->execute([$author_id]);
        while ($review = $reviews->fetch()) {
            echo '<div class="review-card">
                    <div class="review-header">
                        <h4 class="review-author">'.$review['user_name'].'</h4>
                        <span class="review-date">'.date('d.m.Y H:i', strtotime($review['created_at'])).'</span>
                    </div>
                    <p class="review-text">'.nl2br(htmlspecialchars($review['comment'])).'</p>
                </div>';
        }
        ?>
    </div>

    <!-- Всплывающее уведомление -->
    <div id="notification" class="notification-hidden"></div>

    <!-- Скрипты -->
    <script>
        // Открытие/закрытие модального окна изображений
        document.querySelectorAll('.card-image').forEach(img => {
            img.onclick = function() {
                document.getElementById('expandedImg').src = this.src;
                document.getElementById('imageModal').style.display = "block";
            }
        });

        document.querySelector('.close').onclick = function() {
            document.getElementById('imageModal').style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('imageModal')) {
                document.getElementById('imageModal').style.display = "none";
            }
        }

        // Всплывающие уведомления
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('comment_success')) {
            showNotification('Комментарий успешно отправлен! 🎉');
        }
        if (urlParams.has('message_success')) {
            showNotification('Сообщение автору отправлено! ✉️');
        }

        function showNotification(text) {
            const notification = document.getElementById('notification');
            notification.textContent = text;
            notification.classList.add('notification-visible');
            
            setTimeout(() => {
                notification.classList.remove('notification-visible');
            }, 3000);
        }
    </script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // AJAX-фильтрация
    document.querySelectorAll('.category-filters a').forEach(link => {
        link.addEventListener('click', async (e) => {
            e.preventDefault(); // Отменяем стандартное поведение

            try {
                // Отправляем запрос
                const response = await fetch(link.href);
                if (!response.ok) throw new Error('Ошибка загрузки');
                
                // Парсим HTML-ответ
                const html = await response.text();
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, 'text/html');
                
                // Обновляем только галерею
                document.getElementById('gallery').innerHTML = 
                    newDoc.getElementById('gallery').innerHTML;
                
                // Обновляем активный фильтр
                document.querySelectorAll('.nav-link').forEach(item => {
                    item.classList.remove('active');
                });
                link.classList.add('active');
                
                // Обновляем URL без перезагрузки
                window.history.pushState({}, '', link.href);
                
                // Запускаем скрипты для новых изображений
                initImageModals();
                
            } catch (error) {
                console.error('Ошибка:', error);
                document.getElementById('gallery').innerHTML = 
                    '<p class="error">Ошибка загрузки данных</p>';
            }
        });
    });

    // Инициализация модальных окон
    function initImageModals() {
        document.querySelectorAll('.card-image').forEach(img => {
            img.onclick = function() {
                document.getElementById('expandedImg').src = this.src;
                document.getElementById('imageModal').style.display = "block";
            }
        });
    }

        // ... остальные существующие обработчики ...
    });
    </script>

</section>

<?php 
include 'includes/footer.php'; 
?>