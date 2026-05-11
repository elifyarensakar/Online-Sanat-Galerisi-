<?php 
include 'config.php'; // Bağlantıyı dahil ettik

// Eserleri çekelim
$artworks = $db->query("SELECT * FROM artworks LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);

// Etkinlikleri çekelim
$events = $db->query("SELECT * FROM events LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sanat Galerisi & Atölye</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>
    <h1>Sanat Galerimize Hoş Geldiniz</h1>

    <h2>Öne Çıkan Eserler</h2>
    <div class="artwork-grid">
        <?php foreach($artworks as $item): ?>
            <div class="card">
                <img src="uploads/<?php echo $item['image_url']; ?>" width="200">
                <h3><?php echo $item['title']; ?></h3>
                <p><?php echo $item['price']; ?> TL</p>
                <a href="detay.php?id=<?php echo $item['id']; ?>">İncele</a>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Yaklaşan Etkinlikler</h2>
    <ul>
        <?php foreach($events as $event): ?>
            <li>
                <strong><?php echo $event['title']; ?></strong> - 
                Tarih: <?php echo $event['event_date']; ?> 
                <a href="etkinlik_detay.php?id=<?php echo $event['id']; ?>">Rezervasyon Yap</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>