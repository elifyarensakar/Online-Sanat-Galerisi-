<?php 
// 1. Dosya adın baglanti.php olduğu için burayı güncelledik
include 'baglanti.php'; 

// 2. Tüm eserleri (7 tane) görebilmek için limiti artırdık
// Not: baglanti.php içinde $baglan değişkenini kullanıyorsan 
// PDO yerine mysqli sorgusu yazman gerekebilir. Eğer hata alırsan haber ver.
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
    <h1>Sanat Galerimize Hoş Geldiniz</h1>

    <h2>Öne Çıkan Eserler</h2>
    <div class="artwork-grid">
        <?php while($item = mysqli_fetch_assoc($artworks)): ?>
            <div class="card">
                <img src="<?php echo $item['image_url']; ?>" width="200" style="height: auto; border-radius: 8px;">
                <h3><?php echo $item['title']; ?></h3>
                <p><?php echo number_format($item['price'], 2, ',', '.'); ?> TL</p>
                <a href="detay.php?id=<?php echo $item['id']; ?>">İncele</a>
            </div>
        <?php endwhile; ?>
    </div>

    <h2>Yaklaşan Etkinlikler</h2>
    <ul>
        <?php while($event = mysqli_fetch_assoc($events)): ?>
            <li>
                <strong><?php echo $event['title']; ?></strong> - 
                Tarih: <?php echo date("d.m.Y", strtotime($event['event_date'])); ?> 
                <a href="etkinlik_detay.php?id=<?php echo $event['id']; ?>">Rezervasyon Yap</a>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>