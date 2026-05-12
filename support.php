<?php 
// 1. Bağlantı yolunu düzeltiyoruz (Üst klasörde olduğu için ../ ekledik)
include '../baglanti.php'; 

// 2. Form gönderildiğinde çalışacak kısım (INSERT işlemi)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $user_id = 1; // Şimdilik test için senin ID'ni (1) kullanıyoruz

    $ekle = mysqli_query($baglan, "INSERT INTO support_tickets (user_id, subject, message, status) 
                                   VALUES ('$user_id', '$subject', '$message', 'open')");
    
    if ($ekle) {
        echo "<script>alert('Destek talebiniz başarıyla alındı!');</script>";
    } else {
        echo "<script>alert('Hata oluştu!');</script>";
    }
}

// 3. Mevcut talepleri veritabanından çekme (SELECT işlemi)
$talepler = mysqli_query($baglan, "SELECT * FROM support_tickets WHERE user_id = 1");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Müşteri Destek</title>
</head>
<body>
    <h2>İletişim Formu</h2>
    <form action="support.php" method="POST">
        <input type="text" name="subject" placeholder="Konu" required><br><br>
        <textarea name="message" placeholder="Sorunuzu buraya yazın..." required></textarea><br><br>
        <button type="submit">Gönder</button>
    </form>

    <hr>
    <h3>Destek Taleplerim</h3>
    <table border="1">
        <tr>
            <th>Talep No</th>
            <th>Konu</th>
            <th>Durum</th>
        </tr>
        
        <?php while($talep = mysqli_fetch_assoc($talepler)): ?>
        <tr>
            <td><?php echo $talep['id']; ?></td>
            <td><?php echo $talep['subject']; ?></td>
            <td><?php echo $talep['status']; ?></td>
        </tr>
        <?php endwhile; ?>
        
        <?php if(mysqli_num_rows($talepler) == 0): ?>
            <tr><td colspan="3">Henüz bir talebiniz bulunmuyor.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>