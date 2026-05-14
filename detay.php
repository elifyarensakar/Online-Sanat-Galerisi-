<?php
session_start();
include 'baglanti.php'; 

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($baglan, $_GET['id']);

// 1. Görüntülenme sayısını artır (Ödev İstatistik Maddesi 16 için)
mysqli_query($baglan, "UPDATE artworks SET view_count = view_count + 1 WHERE id = '$id'");

// 2. Eser ve Sanatçı Bilgilerini Çek (Gereksinim 1)
$query = mysqli_query($baglan, "
    SELECT artworks.*, users.full_name as artist_name 
    FROM artworks 
    JOIN users ON artworks.artist_id = users.id 
    WHERE artworks.id = '$id'
");
$artwork = mysqli_fetch_assoc($query);

if (!$artwork) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($artwork['title'] ?? 'Eser Detayı'); ?> - KTÜ Galeri</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dark-mode">

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">🎨 KTÜ Galeri</a>
            <ul class="nav-links">
                <li><a href="index.php">Ana Sayfa</a></li>
                <li><a href="events.php">Etkinlikler</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Profilim</a></li>
                    <li class="user-info"><span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span></li>
                    <li><a href="logout.php" class="btn-logout">Çıkış</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-login">Giriş Yap</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="container" style="max-width: 1200px; margin: 50px auto; padding: 0 20px;">
        <div class="artwork-detail" style="display: flex; gap: 50px; flex-wrap: wrap; align-items: flex-start;">
            
            <div class="detail-image" style="flex: 1.2; min-width: 300px; background: #1a1a1a; padding: 20px; border-radius: 15px; border: 1px solid #333; position: relative;">
                <?php if(($artwork['status'] ?? '') == 'sold'): ?>
                    <div style="position: absolute; top: 40px; right: 40px; background: #e74c3c; color: white; padding: 10px 25px; border-radius: 5px; font-weight: bold; transform: rotate(15deg); box-shadow: 0 5px 15px rgba(0,0,0,0.5); z-index: 10; border: 2px solid white;">
                        SATILDI
                    </div>
                <?php endif; ?>
                <img src="<?php echo $artwork['image_url']; ?>" alt="<?php echo htmlspecialchars($artwork['title'] ?? ''); ?>" 
                     style="width: 100%; height: auto; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); filter: <?php echo (($artwork['status'] ?? '') == 'sold') ? 'grayscale(40%)' : 'none'; ?>;">
            </div>

            <div class="detail-content" style="flex: 1; min-width: 300px;">
                <h1 style="color: #f1c40f; margin-bottom: 5px; font-size: 2.5rem;"><?php echo htmlspecialchars($artwork['title'] ?? ''); ?></h1>
                <p style="font-size: 1.3rem; color: #ccc; margin-bottom: 25px; font-style: italic;">
                    Sanatçı: <strong style="color: #fff; font-style: normal;"><?php echo htmlspecialchars($artwork['artist_name'] ?? 'Bilinmiyor'); ?></strong>
                </p>
                
                <div style="background: #1e1e1e; padding: 25px; border-radius: 12px; margin-bottom: 25px; border-left: 5px solid #f1c40f; line-height: 1.8;">
                    <h3 style="color: #fff; margin-bottom: 15px; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px;">Eser Hakkında</h3>
                    <p style="color: #bbb;">
                        <?php echo nl2br(htmlspecialchars($artwork['description'] ?? 'Açıklama bulunmamaktadır.')); ?>
                    </p>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: rgba(46, 204, 113, 0.1); padding: 20px; border-radius: 12px; border: 1px solid rgba(46, 204, 113, 0.3);">
                    <div class="price-tag" style="font-size: 2.2rem; color: #2ecc71; font-weight: bold;">
                        <?php echo number_format($artwork['price'] ?? 0, 2, ',', '.'); ?> TL
                    </div>
                    
                    <?php if(($artwork['status'] ?? '') == 'sold'): ?>
                        <span style="color: #e74c3c; font-weight: bold; font-size: 1.2rem;">✖ Bu Eser Satıldı</span>
                    <?php else: ?>
                        <a href="odeme.php?artwork_id=<?php echo $artwork['id']; ?>" 
                           style="background: #27ae60; color: white; text-decoration: none; padding: 15px 35px; border-radius: 30px; font-weight: bold; font-size: 1.1rem; transition: 0.3s; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);">
                           🛒 Hemen Satın Al
                        </a>
                    <?php endif; ?>
                </div>

                <div class="comment-form-container" style="margin-bottom: 30px;">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <h4 style="color: #f1c40f; margin-bottom: 15px;">✍️ Eser Hakkında Yorum Yap</h4>
                        <form action="yorum_ekle.php" method="POST" style="background: #1e1e1e; padding: 20px; border-radius: 12px; border: 1px solid #333;">
                            <input type="hidden" name="artwork_id" value="<?php echo $id; ?>">
                            <div style="margin-bottom: 15px;">
                                <label style="color: #ccc; display:block; margin-bottom:5px;">Puanınız:</label>
                                <select name="rating" required style="padding: 8px; background: #2c2c2c; color: white; border: 1px solid #444; border-radius: 5px;">
                                    <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                    <option value="4">⭐⭐⭐⭐ (4)</option>
                                    <option value="3">⭐⭐⭐ (3)</option>
                                    <option value="2">⭐⭐ (2)</option>
                                    <option value="1">⭐ (1)</option>
                                </select>
                            </div>
                            <textarea name="comment_text" required placeholder="Düşüncelerinizi paylaşın..." style="width: 100%; height: 80px; background: #2c2c2c; color: white; border: 1px solid #444; border-radius: 8px; padding: 10px; margin-bottom: 10px; box-sizing: border-box;"></textarea>
                            <button type="submit" style="background: #f1c40f; color: black; border: none; padding: 10px 25px; border-radius: 20px; font-weight: bold; cursor: pointer;">Gönder</button>
                        </form>
                    <?php else: ?>
                        <div style="background: #1e1e1e; padding: 15px; border-radius: 10px; border: 1px solid #e74c3c; color: #e74c3c;">
                            ⚠️ Yorum yapabilmek için <a href="login.php" style="color: #f1c40f; font-weight: bold;">giriş yapmalısınız</a>.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="comments-section" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #333;">
                    <h3 style="color: #f1c40f; margin-bottom: 15px;">💬 Kullanıcı Yorumları</h3>
                    <?php
                    $sorgu_yorumlar = mysqli_query($baglan, "
                        SELECT comments.*, users.full_name 
                        FROM comments 
                        JOIN users ON comments.user_id = users.id 
                        WHERE item_id = '$id' AND item_type = 'artwork'
                        ORDER BY created_at DESC
                    ");
                    if (mysqli_num_rows($sorgu_yorumlar) > 0):
                        while ($yorum = mysqli_fetch_assoc($sorgu_yorumlar)): ?>
                            <div style="background: #1a1a1a; padding: 15px; border-radius: 10px; margin-bottom: 10px; border: 1px solid #222;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <strong style="color: #fff;"><?php echo htmlspecialchars($yorum['full_name']); ?></strong>
                                    <span style="color: #f1c40f;"><?php echo str_repeat('★', (int)$yorum['rating']); ?></span>
                                </div>
                                <p style="color: #999; font-size: 0.9rem; margin-top: 8px;"><?php echo htmlspecialchars($yorum['comment_text']); ?></p>
                            </div>
                        <?php endwhile;
                    else: ?>
                        <p style="color: #666; font-style: italic;">Henüz yorum yapılmamış. İlk yorumu sen yap!</p>
                    <?php endif; ?>
                </div>

                <div class="action-buttons" style="display: flex; gap: 20px; align-items: center; margin-top: 30px;">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <button onclick="favoriIslem(<?php echo $artwork['id']; ?>)" 
                                style="background: #e74c3c; color: white; border: none; padding: 12px 25px; border-radius: 30px; cursor: pointer; font-weight: bold; display: flex; align-items: center; gap: 8px;">
                            ❤️ Favorilere Ekle / Çıkar
                        </button>
                    <?php endif; ?>
                    <a href="index.php" style="color: #888; text-decoration: none; font-size: 0.95rem;">← Galeriye Dön</a>
                </div>
            </div>
        </div>
    </main>

    <script>
    function favoriIslem(artworkId) {
        fetch('favori_islem.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'artwork_id=' + artworkId
        })
        .then(response => response.text())
        .then(mesaj => {
            alert(mesaj);
        });
    }
    </script>

</body>
</html>