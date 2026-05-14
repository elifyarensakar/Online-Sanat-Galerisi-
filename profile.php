<?php
ob_start();
session_start();
include 'baglanti.php';

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Kullanıcı Bilgilerini Çek
$sorgu_user = mysqli_query($baglan, "SELECT full_name, email FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($sorgu_user);

// 2. Katılınan Etkinlikleri Çek
$sorgu_etkinlikler = mysqli_query($baglan, "
    SELECT e.* FROM events e 
    JOIN reservations r ON e.id = r.event_id 
    WHERE r.user_id = '$user_id' 
    ORDER BY e.event_date DESC
");

// 3. Satın Alınan Eserleri Çek
$sorgu_eserler = mysqli_query($baglan, "
    SELECT a.* FROM artworks a 
    JOIN orders o ON a.id = o.artwork_id 
    WHERE o.user_id = '$user_id' 
    ORDER BY o.order_date DESC
");

// 4. Destek Taleplerini Çek
$sorgu_destek = mysqli_query($baglan, "SELECT * FROM support_tickets WHERE user_id = '$user_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profilim - KTÜ Galeri</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dark-mode">

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">🎨 KTÜ Galeri</a>
            <ul class="nav-links">
                <li><a href="index.php">Ana Sayfa</a></li>
                <li><a href="events.php">Etkinlikler</a></li>
                <li><a href="profile.php" style="color: #f1c40f;">Profilim</a></li>
                <li><a href="logout.php" class="btn-logout">Çıkış</a></li>
            </ul>
        </div>
    </nav>

    <main class="container" style="max-width: 900px; margin: 50px auto; padding: 0 20px;">
        
        <div class="profile-header" style="text-align: center; margin-bottom: 40px; background: #1e1e1e; padding: 30px; border-radius: 15px; border: 1px solid #333;">
            <div class="profile-avatar" style="font-size: 50px; margin-bottom: 15px;">👤</div>
            <h2 style="color: #f1c40f; margin: 0;"><?php echo htmlspecialchars($user['full_name'] ?? 'Kullanıcı'); ?></h2>
            <p style="color: #888; margin: 5px 0 0 0;"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
        </div>

        <div style="display: grid; gap: 40px;">

            <section>
                <h3 style="color: #fff; border-bottom: 2px solid #f1c40f; padding-bottom: 10px; margin-bottom: 20px;">🎫 Mevcut Kuponlarım</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                    <?php
                    $sorgu_kuponlar = mysqli_query($baglan, "SELECT * FROM coupons WHERE is_active = 1 AND expiry_date >= CURDATE()");
                    if (mysqli_num_rows($sorgu_kuponlar) > 0):
                        while($kupon = mysqli_fetch_assoc($sorgu_kuponlar)): ?>
                            <div style="background: linear-gradient(135deg, #2c3e50, #000); padding: 20px; border-radius: 12px; border: 1px dashed #f1c40f; text-align: center;">
                                <div style="font-size: 0.7rem; color: #aaa; text-transform: uppercase;">İndirim: %<?php echo (int)$kupon['discount_rate']; ?></div>
                                <div style="font-size: 1.2rem; color: #f1c40f; font-weight: bold; margin: 10px 0;"><?php echo htmlspecialchars($kupon['code']); ?></div>
                                <div style="font-size: 0.65rem; color: #888;">Geçerlilik: <?php echo date("d.m.Y", strtotime($kupon['expiry_date'])); ?></div>
                            </div>
                        <?php endwhile;
                    else: ?>
                        <p style="color: #666; font-style: italic;">Aktif kuponunuz bulunmuyor.</p>
                    <?php endif; ?>
                </div>
            </section>
            
            <section>
                <h3 style="color: #fff; border-bottom: 2px solid #f1c40f; padding-bottom: 10px; margin-bottom: 20px;">🎟️ Katıldığım Etkinlikler</h3>
                <div class="my-events">
                    <?php if (mysqli_num_rows($sorgu_etkinlikler) > 0): ?>
                        <?php while($e = mysqli_fetch_assoc($sorgu_etkinlikler)): ?>
                            <div style="background: #1e1e1e; padding: 15px; border-radius: 12px; border-left: 5px solid #27ae60; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h4 style="color: #fff; margin: 0;"><?php echo htmlspecialchars($e['title']); ?></h4>
                                    <small style="color: #bbb;">📍 <?php echo htmlspecialchars($e['location'] ?? 'KTÜ Galeri Salonu'); ?> | 🗓️ <?php echo date("d.m.Y", strtotime($e['event_date'])); ?></small>
                                </div>
                                <span style="background: rgba(39, 174, 96, 0.1); color: #2ecc71; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold;">Onaylandı</span>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: #666; font-style: italic;">Henüz bir etkinliğe kayıt olmadınız.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section>
                <h3 style="color: #fff; border-bottom: 2px solid #e67e22; padding-bottom: 10px; margin-bottom: 20px;">🖼️ Sahip Olduğum Eserler</h3>
                <div class="my-artworks" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
                    <?php if (mysqli_num_rows($sorgu_eserler) > 0): ?>
                        <?php while($art = mysqli_fetch_assoc($sorgu_eserler)): ?>
                            <div style="background: #1e1e1e; border-radius: 12px; overflow: hidden; border: 1px solid #333;">
                                <img src="<?php echo $art['image_url']; ?>" style="width: 100%; height: 150px; object-fit: cover;">
                                <div style="padding: 10px;">
                                    <h5 style="color: #fff; margin: 0;"><?php echo htmlspecialchars($art['title']); ?></h5>
                                    <small style="color: #f1c40f;">Koleksiyonunuzda</small>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: #666; font-style: italic;">Henüz bir eser satın almadınız.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section>
                <h3 style="color: #fff; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px;">✉️ Geçmiş Destek Taleplerim</h3>
                <div class="my-support">
                    <?php if (mysqli_num_rows($sorgu_destek) > 0): ?>
                        <?php while($ticket = mysqli_fetch_assoc($sorgu_destek)): ?>
                            <div style="background: #1e1e1e; padding: 15px; border-radius: 10px; margin-bottom: 10px; border: 1px solid #333;">
                                <div style="display: flex; justify-content: space-between;">
                                    <strong style="color: #fff;"><?php echo htmlspecialchars($ticket['subject']); ?></strong>
                                    <span style="color: <?php echo ($ticket['status'] == 'open') ? '#27ae60' : '#888'; ?>; font-size: 0.8rem; font-weight: bold;">
                                        <?php echo strtoupper($ticket['status']); ?>
                                    </span>
                                </div>
                                <p style="color: #888; font-size: 0.9rem; margin-top: 5px;"><?php echo htmlspecialchars($ticket['message']); ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: #666; font-style: italic;">Açık bir destek talebiniz bulunmuyor.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section style="background: #1e1e1e; padding: 25px; border-radius: 15px; border: 1px solid #333;">
                <h3 style="color: #3498db; margin-bottom: 20px;">🆕 Yeni Destek Talebi Oluştur</h3>
                <form action="destek_islem.php" method="POST">
                    <div style="margin-bottom: 15px;">
                        <label style="color: #ccc; display: block; margin-bottom: 5px;">Konu Seçin:</label>
                        <select name="subject" required style="width: 100%; padding: 10px; background: #2c2c2c; color: white; border: 1px solid #444; border-radius: 8px;">
                            <option value="Ödeme Sorunu">💳 Ödeme Sorunu</option>
                            <option value="Bilet İşlemleri">🎟️ Bilet / Etkinlik İşlemleri</option>
                            <option value="Sanatçı Başvurusu">🎨 Sanatçı Başvurusu Hakkında</option>
                            <option value="Hata Bildirimi">🐛 Teknik Hata Bildirimi</option>
                            <option value="Diğer">📝 Diğer</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="color: #ccc; display: block; margin-bottom: 5px;">Mesajınız:</label>
                        <textarea name="message" required placeholder="Sorununuzu detaylıca açıklayın..." style="width: 100%; padding: 10px; background: #2c2c2c; color: white; border: 1px solid #444; border-radius: 8px; height: 100px;"></textarea>
                    </div>
                    <button type="submit" style="background: #3498db; color: white; border: none; padding: 10px 25px; border-radius: 20px; font-weight: bold; cursor: pointer;">Talebi Gönder</button>
                </form>
            </section>

        </div>
    </main>

</body>
</html>
<?php ob_end_flush(); ?>