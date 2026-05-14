<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'config.php';
session_start();


if (isset($_GET['book_id']) && isset($_SESSION['user_id'])) {
    $event_id = $_GET['book_id'];
    $user_id = $_SESSION['user_id'];

    try {
        // 1. Etkinlik bilgilerini çek
        $stmt = $db->prepare("SELECT capacity, current_participants FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event) {
            // 2. Kontenjan müsait mi?
            if ($event['current_participants'] < $event['capacity']) {
                
                // 3. Rezervasyonu kaydet
                $insert = $db->prepare("INSERT INTO reservations (user_id, event_id, status) VALUES (?, ?, 'confirmed')");
                $insert->execute([$user_id, $event_id]);

                // 4. Katılımcı sayısını güncelle
                $update = $db->prepare("UPDATE events SET current_participants = current_participants + 1 WHERE id = ?");
                $update->execute([$event_id]);

                echo "<script>alert('Rezervasyon Başarılı!'); window.location.href='events.php';</script>";
                exit();
            } else {
                echo "<script>alert('Üzgünüz, kontenjan dolu.'); window.location.href='events.php';</script>";
                exit();
            }
        }
    } catch (PDOException $e) {
        // Hata varsa ekrana yazdırır (500 hatasının nedenini buradan görebilirsin)
        die("Veritabanı hatası: " . $e->getMessage());
    }
}
?>

// --- REZERVASYON MANTIĞI ---
if (isset($_GET['book_id']) && isset($_SESSION['user_id'])) {
    $event_id = $_GET['book_id'];
    $user_id = $_SESSION['user_id'];

    // Veritabanı sütun isimlerini İngilizce (capacity, current_participants) olarak kullanıyoruz
    $query = $db->prepare("SELECT capacity, current_participants FROM events WHERE id = ?");
    $query->execute([$event_id]);
    $event = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($event) {
        if ($event['current_participants'] < $event['capacity']) {
            // Rezervasyon ekle
            $insert = $db->prepare("INSERT INTO reservations (user_id, event_id, status) VALUES (?, ?, 'confirmed')");
            $insert->execute([$user_id, $event_id]);
            
            // Katılımcı sayısını artır
            $update = $db->prepare("UPDATE events SET current_participants = current_participants + 1 WHERE id = ?");
            $update->execute([$event_id]);
            
            echo "<script>alert('Rezervasyon başarılı!'); window.location.href='events.php';</script>";
        } else {
            echo "<script>alert('Üzgünüz, kontenjan dolu.'); window.location.href='events.php';</script>";
        }
    }
    exit(); // İşlem bittiğinde sayfanın geri kalanını yükleme
}

// --- ETKİNLİKLERİ ÇEKME ---
$all_events = $db->query("SELECT * FROM events ORDER BY event_date ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Etkinlikler - KTÜ Galeri</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dark-mode">
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">🎨 KTÜ Galeri</a>
            <ul class="nav-links">
                <li><a href="index.php">Ana Sayfa</a></li>
                <li><a href="events.php" style="color: #f1c40f;">Etkinlikler</a></li>
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

    <main class="container" style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
        <div class="artwork-grid">
            <?php foreach($all_events as $e): ?>
                <div class="card">
                    <div class="card-content" style="padding: 25px;">
                        <h3><?php echo htmlspecialchars($e['title']); ?></h3>
                        <div style="margin: 15px 0; color: #ccc;">
                            <p>📅 Tarih: <?php echo date("d.m.Y", strtotime($e['event_date'])); ?></p>
                            <p>⏰ Saat: <?php echo $e['event_time']; ?></p>
                            <p>👥 Kontenjan: <?php echo $e['current_participants'] . " / " . $e['capacity']; ?></p>
                        </div>
                        <p class="price"><?php echo number_format($e['price'], 2, ',', '.'); ?> TL</p>
                        <div class="card-buttons">
                            <a href="events.php?book_id=<?php echo $e['id']; ?>" class="btn-incele" style="width:100%; text-align:center;">Rezervasyon Yap</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>