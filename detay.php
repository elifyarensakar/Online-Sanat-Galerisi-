<?php
session_start();
include 'baglanti.php'; // Veritabanı bağlantısı

// Sayfaya bir ID gelip gelmediğini kontrol edelim
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($baglan, $_GET['id']);

// 1. Görüntülenme sayısını artır (İstatistik gereksinimi için)
mysqli_query($baglan, "UPDATE artworks SET view_count = view_count + 1 WHERE id = $id");

// 2. Eser bilgilerini ve sanatçı ismini çekelim
$query = mysqli_query($baglan, "
    SELECT artworks.*, users.full_name as artist_name 
    FROM artworks 
    JOIN users ON artworks.artist_id = users.id 
    WHERE artworks.id = $id
");
$artwork = mysqli_fetch_assoc($query);

// Eser bulunamadıysa ana sayfaya dön
if (!$artwork) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $artwork['title']; ?> - KTÜ Sanat Galerisi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dark-mode">

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">🎨 KTÜ Galeri</a>
            <ul class="nav-links">
                <li><a href="index.php">Ana Sayfa</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="favorites.php">Favorilerim</a></li>
                    <li class="user-info"><span><?php echo $_SESSION['full_name']; ?></span></li>
                    <li><a href="logout.php" class="btn-logout">Çıkış</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-login">Giriş Yap</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="container" style="max-width: 1200px; margin: 50px auto; padding: 20px;">
        <div class="artwork-detail" style="display: flex; gap: 50px; flex-wrap: wrap;">
            
            <div class="detail-image" style="flex: 1; min-width: 300px; background: #1a1a1a; padding: 20px; border-radius: 15px; border: 1px solid #333;">
                <img src="<?php echo $artwork['image_url']; ?>" alt="<?php echo $artwork['title']; ?>" 
                     style="width: 100%; height: auto; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            </div>

            <div class="detail-content" style="flex: 1; min-width: 300px;">
                <h1 style="color: #f1c40f; margin-bottom: 10px;"><?php echo $artwork['title']; ?></h1>
                <p style="font-size: 1.2rem; color: #ccc; margin-bottom: 20px;">
                    Sanatçı: <strong style="color: #fff;"><?php echo $artwork['artist_name']; ?></strong>
                </p>
                
                <div style="background: #1e1e1e; padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #f1c40f;">
                    <h3 style="color: #fff; margin-bottom: 10px;">Eser Hakkında</h3>
                    <p style="line-height: 1.8; color: #bbb;">
                        <?php echo nl2br($artwork['description']); ?>
                    </p>
                </div>

                <div class="price-tag" style="font-size: 2rem; color: #2ecc71; font-weight: bold; margin-bottom: 30px;">
                    <?php echo number_format($artwork['price'], 2, ',', '.'); ?> TL
                </div>

                <div class="action-buttons" style="display: flex; gap: 20px;">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <button onclick="favoriIslem(<?php echo $artwork['id']; ?>)" class="btn-fav-large" 
                                style="background: #e74c3c; color: white; border: none; padding: 15px 30px; border-radius: 30px; cursor: pointer; font-weight: bold;">
                            ❤️ Favorilere Ekle / Çıkar
                        </button>
                    <?php endif; ?>
                    
                    <a href="index.php" style="color: #ccc; text-decoration: none; align-self: center;">← Galeriye Dön</a>
                </div>

                <div class="stats" style="margin-top: 40px; color: #666; font-size: 0.9rem;">
                    👁️ Bu eser toplam <strong><?php echo $artwork['view_count']; ?></strong> kez görüntülendi.
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