<?php
include 'config.php';
session_start();

// Rezervasyon İşlemi (Mevcut mantığın korunması)
if (isset($_GET['book_id']) && isset($_SESSION['user_id'])) {
    $event_id = $_GET['book_id'];
    $user_id = $_SESSION['user_id'];

    $event = $db->query("SELECT capacity, current_participants FROM events WHERE id = $event_id")->fetch();
    
    if ($event['current_participants'] < $event['capacity']) {
        $db->prepare("INSERT INTO reservations (user_id, event_id, status) VALUES (?, ?, 'confirmed')")->execute([$user_id, $event_id]);
        $db->query("UPDATE events SET current_participants = current_participants + 1 WHERE id = $event_id");
        echo "<script>alert('Rezervasyon başarılı!'); window.location.href='events.php';</script>";
    } else {
        echo "<script>alert('Maalesef kontenjan dolu.');</script>";
    }
}

$all_events = $db->query("SELECT * FROM events")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Etkinlikler - KTÜ Sanat Galerisi</title>
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

    <main class="container" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">
        <h2 style="text-align: center; margin-bottom: 40px; color: #fff;">Yaklaşan Etkinlikler ve Atölyeler</h2>
        
        <div class="artwork-grid">
            <?php foreach($all_events as $e): ?>
                <div class="card">
                    <div class="card-content" style="padding: 30px;">
                        <h3 style="color: #f1c40f;"><?php echo htmlspecialchars($e['title']); ?></h3>
                        
                        <div style="margin: 20px 0; color: #ccc; font-size: 0.9rem;">
                            <p>📅 Tarih: <strong><?php echo date("d.m.Y", strtotime($e['event_date'])); ?></strong></p>
                            <p>⏰ Saat: <strong><?php echo $e['event_time']; ?></strong></p>
                            <p>👥 Kontenjan: <strong><?php echo $e['current_participants'] . " / " . $e['capacity']; ?></strong></p>
                        </div>

                        <p class="price" style="font-size: 1.4rem;"><?php echo number_format($e['price'], 2, ',', '.'); ?> TL</p>
                        
                        <div class="card-buttons" style="margin-top: 20px;">
                            <?php if($e['current_participants'] < $e['capacity']): ?>
                                <a href="events.php?book_id=<?php echo $e['id']; ?>" class="btn-incele" style="width: 100%; text-align: center; border-radius: 5px;">
                                    Rezervasyon Yap
                                </a>
                            <?php else: ?>
                                <button disabled style="width: 100%; background: #444; border: none; padding: 10px; color: #888; cursor: not-allowed; border-radius: 5px;">
                                    Kontenjan Dolu
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

</body>
</html>