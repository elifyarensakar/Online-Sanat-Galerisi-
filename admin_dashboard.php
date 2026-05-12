<?php
include 'config.php';
session_start();

// Sadece yöneticiler girebilsin
if ($_SESSION['role'] !== 'admin') { die("Yetkisiz erişim!"); }

// İstatistikleri çekelim
$total_users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_sales = $db->query("SELECT SUM(total_price) FROM orders")->fetchColumn();
$most_viewed = $db->query("SELECT title, view_count FROM artworks ORDER BY view_count DESC LIMIT 1")->fetch();
?>

<div style="background: #f4f4f4; padding: 20px;">
    <h1>Yönetici Rapor Paneli</h1>
    <p><strong>Toplam Kayıtlı Kullanıcı:</strong> <?php echo $total_users; ?></p>
    <p><strong>Toplam Ciro:</strong> <?php echo $total_sales; ?> TL</p>
    <p><strong>En Çok İlgi Gören Eser:</strong> <?php echo $most_viewed['title']; ?> (<?php echo $most_viewed['view_count']; ?> izlenme)</p>
</div>