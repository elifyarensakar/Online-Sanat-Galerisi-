<?php 
ob_start(); // Çıktı tamponlamayı başlat
session_start();
include 'baglanti.php'; 

// --- 1. REZERVASYON İŞLEMİ ---
if (isset($_GET['book_id']) && isset($_SESSION['user_id'])) {
    $event_id = (int)$_GET['book_id'];
    $user_id = $_SESSION['user_id'];

    // ÖNEMLİ KONTROL: Session'daki user_id gerçekten veritabanında var mı?
    // Foreign Key hatasını önlemek için bu kontrol kritiktir.
    $user_var_mi = mysqli_query($baglan, "SELECT id FROM users WHERE id = '$user_id'");
    
    if (mysqli_num_rows($user_var_mi) > 0) {
        
        // MÜKERRER KAYIT KONTROLÜ
        $kontrol = mysqli_query($baglan, "SELECT * FROM reservations WHERE user_id = '$user_id' AND event_id = '$event_id'");
        
        if (mysqli_num_rows($kontrol) > 0) {
            echo "<script>alert('Bu etkinliğe zaten kayıtlısınız!'); window.location.href='events.php';</script>";
            exit();
        }

        $sorgu_etkinlik = mysqli_query($baglan, "SELECT * FROM events WHERE id = '$event_id'");
        $etkinlik = mysqli_fetch_assoc($sorgu_etkinlik);

        if ($etkinlik && $etkinlik['price'] == 0) {
            if ($etkinlik['current_participants'] < $etkinlik['capacity']) {
                // Kayıt işlemi
                $ekle = mysqli_query($baglan, "INSERT INTO reservations (user_id, event_id, status) VALUES ('$user_id', '$event_id', 'confirmed')");
                
                if ($ekle) {
                    mysqli_query($baglan, "UPDATE events SET current_participants = current_participants + 1 WHERE id = '$event_id'");
                    echo "<script>alert('Kaydınız başarıyla oluşturuldu!'); window.location.href='events.php';</script>";
                    exit();
                } else {
                    // Eğer hala hata alırsan hatayı burada görelim
                    echo "Veritabanı Hatası: " . mysqli_error($baglan);
                    exit();
                }
            }
        }
    } else {
        // Eğer kullanıcı veritabanında yoksa session'ı temizle ve girişe yönlendir
        session_destroy();
        echo "<script>alert('Oturum hatası! Lütfen tekrar giriş yapın.'); window.location.href='login.php';</script>";
        exit();
    }
}

// --- 2. TÜM ETKİNLİKLERİ LİSTELEME ---
$tum_etkinlikler = mysqli_query($baglan, "SELECT * FROM events ORDER BY event_date ASC");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Etkinlikler - KTÜ Galeri</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .btn-registered {
            width: 100%;
            padding: 12px;
            background: rgba(39, 174, 96, 0.1);
            color: #2ecc71;
            border: 1px solid #2ecc71;
            border-radius: 8px;
            cursor: not-allowed;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
    </style>
</head>
<body class="dark-mode">

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">🎨 KTÜ Galeri</a>
            <ul class="nav-links">
                <li><a href="index.php">Ana Sayfa</a></li>
                <li><a href="events.php" style="color: #f1c40f;">Etkinlikler</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Profilim</a></li>
                    <li class="user-info">Hoş geldin, <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span></li>
                    <li><a href="logout.php" class="btn-logout">Çıkış</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-login">Giriş Yap</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="container" style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
        <h1 style="text-align: center; color: #fff; margin-bottom: 30px;">Yaklaşan Etkinlikler</h1>
        
        <div class="artwork-grid">
            <?php while($e = mysqli_fetch_assoc($tum_etkinlikler)): ?>
                <?php 
                    $kayitli_mi = false;
                    if(isset($_SESSION['user_id'])) {
                        $u_id = $_SESSION['user_id'];
                        $e_id = $e['id'];
                        $check = mysqli_query($baglan, "SELECT * FROM reservations WHERE user_id = '$u_id' AND event_id = '$e_id'");
                        if($check && mysqli_num_rows($check) > 0) $kayitli_mi = true;
                    }
                ?>
                <div class="card">
                    <div class="card-content" style="padding: 25px;">
                        <h3 style="color: #f1c40f;"><?php echo htmlspecialchars($e['title']); ?></h3>
                        
                        <div style="margin: 15px 0; color: #ccc; font-size: 0.95rem; line-height: 1.6;">
                            <p>📅 <strong>Tarih:</strong> <?php echo date("d.m.Y", strtotime($e['event_date'])); ?></p>
                            <p>⏰ <strong>Saat:</strong> <?php echo htmlspecialchars($e['event_time']); ?></p>
                            <p>📍 <strong>Konum:</strong> <?php echo htmlspecialchars($e['location'] ?? 'KTÜ Galeri Salonu'); ?></p>
                            <p>👥 <strong>Kontenjan:</strong> <?php echo $e['current_participants'] . " / " . $e['capacity']; ?></p>
                        </div>
                        
                        <p class="price" style="margin-bottom: 20px;">
                            <?php echo ($e['price'] == 0) ? "Ücretsiz" : number_format($e['price'], 2, ',', '.') . " TL"; ?>
                        </p>
                        
                        <div class="card-buttons">
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <?php if($kayitli_mi): ?>
                                    <div class="btn-registered">
                                        <span>✅ Kayıtlısınız</span>
                                    </div>
                                <?php else: ?>
                                    <?php if($e['price'] > 0): ?>
                                        <a href="odeme.php?event_id=<?php echo $e['id']; ?>" class="btn-incele" 
                                           style="width:100%; text-align:center; background-color: #f1c40f; border-color: #f1c40f; color: #121212; font-weight: bold;">
                                           💳 Ödeme Yap & Katıl
                                        </a>
                                    <?php else: ?>
                                        <a href="events.php?book_id=<?php echo $e['id']; ?>" class="btn-incele" 
                                           style="width:100%; text-align:center; background-color: #27ae60; border-color: #27ae60; color: white;">
                                           ✅ Ücretsiz Kaydol
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="login.php" class="btn-incele" style="width:100%; text-align:center;">Giriş Yaparak Katıl</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
</body>
</html>
<?php 
ob_end_flush(); 
?>