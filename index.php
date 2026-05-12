<?php 
session_start(); 
include 'baglanti.php'; 

// Eserleri ve Etkinlikleri çekelim
$artworks = mysqli_query($baglan, "SELECT * FROM artworks LIMIT 10");
$events = mysqli_query($baglan, "SELECT * FROM events LIMIT 5");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sanat Galerisi & Atölye</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">🎨 KTÜ Galeri</a>
            <ul class="nav-links">
                <li><a href="index.php">Ana Sayfa</a></li>
                <li><a href="events.php">Etkinlikler</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="favorites.php">Favorilerim</a></li>
                    <li class="user-info">Hoş geldin, <span><?php echo $_SESSION['full_name']; ?></span></li>
                    <li><a href="logout.php" class="btn-logout">Çıkış</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-login">Giriş Yap</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <header style="text-align: center; margin-bottom: 40px;">
        <h1>Sanat Galerimize Hoş Geldiniz</h1>
    </header>

    <main class="container">
        <h2>Öne Çıkan Eserler</h2>
        <div class="artwork-grid">
            <?php while($item = mysqli_fetch_assoc($artworks)): ?>
                <div class="card">
                    <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['title']; ?>">
                    <div class="card-content">
                        <h3><?php echo $item['title']; ?></h3>
                        <p class="price"><?php echo number_format($item['price'], 2, ',', '.'); ?> TL</p>
                        
                        <div class="card-buttons" style="display: flex; justify-content: center; gap: 10px; margin-top: 10px;">
                            <a href="detay.php?id=<?php echo $item['id']; ?>" class="btn-incele">İncele</a>
                            
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <button class="btn-fav" 
                                        onclick="favoriIslem(<?php echo $item['id']; ?>)" 
                                        style="background: none; border: 1px solid #e74c3c; color: #e74c3c; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; transition: 0.3s;">
                                    ❤️
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <section class="events-section">
            <h2>Yaklaşan Etkinlikler</h2>
            <ul class="event-list">
                <?php while($event = mysqli_fetch_assoc($events)): ?>
                    <li>
                        <div class="event-info">
                            <strong><?php echo $event['title']; ?></strong> 
                            <span>📅 <?php echo date("d.m.Y", strtotime($event['event_date'])); ?></span>
                        </div>
                        <a href="etkinlik_detay.php?id=<?php echo $event['id']; ?>" class="btn-rezervasyon">Rezervasyon Yap</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>
    </main>

    <script>
    function favoriIslem(artworkId) {
        // AJAX ile sayfayı yenilemeden favori ekle/çıkar yapıyoruz
        fetch('favori_islem.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'artwork_id=' + artworkId
        })
        .then(response => response.text())
        .then(mesaj => {
            alert(mesaj); // "Favorilere eklendi" veya "Favorilerden çıkarıldı"
        });
    }
    </script>

</body>
</html>