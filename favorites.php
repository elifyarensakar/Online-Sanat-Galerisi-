<?php 
session_start();
include 'baglanti.php'; // Veritabanı bağlantısı

// GÜVENLİK: Giriş yapmamış kullanıcıyı login sayfasına yönlendirir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/**
 * SQL JOIN Sorgusu: 
 * Artworks ve Favorites tablolarını birleştirerek 
 * sadece mevcut kullanıcının id'si ile eşleşen eserleri getirir.
 */
$sorgu = mysqli_query($baglan, "
    SELECT artworks.* FROM artworks 
    JOIN favorites ON artworks.id = favorites.artwork_id 
    WHERE favorites.user_id = '$user_id'
");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Favorilerim - KTÜ Sanat Galerisi</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">🎨 KTÜ Galeri</a>
            <ul class="nav-links">
                <li><a href="index.php">Ana Sayfa</a></li>
                <li><a href="events.php">Etkinlikler</a></li>
                <?php if(isset($_SESSION['full_name'])): ?>
                    <li class="user-info"><span><?php echo $_SESSION['full_name']; ?></span></li>
                    <li><a href="logout.php" class="btn-logout">Çıkış</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="container" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">
        <h2 style="text-align: center; margin-bottom: 40px; color: #fff;">Beğendiğim Eserler</h2>
        
        <div class="artwork-grid">
            <?php if(mysqli_num_rows($sorgu) > 0): ?>
                <?php while($eser = mysqli_fetch_assoc($sorgu)): ?>
                    <div class="card">
                        <img src="<?php echo $eser['image_url']; ?>" alt="<?php echo $eser['title']; ?>">
                        
                        <div class="card-content">
                            <h3><?php echo $eser['title']; ?></h3>
                            <p class="price"><?php echo number_format($eser['price'], 2, ',', '.'); ?> TL</p>
                            
                            <div class="card-buttons" style="display: flex; justify-content: center; gap: 15px; margin-top: 15px;">
                                <a href="detay.php?id=<?php echo $eser['id']; ?>" class="btn-incele">İncele</a>
                                
                                <button onclick="favoriCikar(<?php echo $eser['id']; ?>)" 
                                        style="background: #e74c3c; color:white; border:none; padding:10px 20px; border-radius:25px; cursor:pointer; font-weight:bold; transition: 0.3s;">
                                    ❌ Çıkar
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; grid-column: 1 / -1; padding: 50px; background: #1e1e1e; border-radius: 15px; border: 1px dashed #444;">
                    <p style="color: #ccc; font-size: 1.1rem;">Henüz favori listeniz boş.</p>
                    <a href="index.php" style="color: #f1c40f; text-decoration: none; font-weight: bold; display: inline-block; margin-top: 15px;">Galeriyi Keşfetmeye Başla →</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
    function favoriCikar(artworkId) {
        if(confirm('Bu eseri favorilerinizden çıkarmak istediğinize emin misiniz?')) {
            // Arka planda favori_islem.php dosyasına veri gönderiyoruz
            fetch('favori_islem.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'artwork_id=' + artworkId
            })
            .then(response => response.text())
            .then(mesaj => {
                // İşlem bitince sayfayı yenile ki liste güncellensin
                location.reload(); 
            });
        }
    }
    </script>

</body>
</html>