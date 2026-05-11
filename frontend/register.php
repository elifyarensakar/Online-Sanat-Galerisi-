<?php
session_start();
require_once 'baglanti.php'; // Veritabanı bağlantı dosyan

$mesaj = "";
$hata = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $sifre = $_POST['password'];
    $sifre_tekrar = $_POST['password_confirm'];

    if (!empty($email) && !empty($sifre)) {
        if ($sifre !== $sifre_tekrar) {
            $hata = "Şifreler birbiriyle eşleşmiyor.";
        } else {
            // E-posta adresi zaten kayıtlı mı kontrol et
            $kontrol_sorgu = "SELECT id FROM users WHERE email = ?";
            $stmt = $baglanti->prepare($kontrol_sorgu);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $sonuc = $stmt->get_result();

            if ($sonuc->num_rows > 0) {
                $hata = "Bu e-posta adresi zaten kullanımda.";
            } else {
                // Şifreyi güvenli bir şekilde hash'leme (En önemli kısım!)
                $hashli_sifre = password_hash($sifre, PASSWORD_DEFAULT);

                // Kullanıcıyı veritabanına ekle
                $ekle_sorgu = "INSERT INTO users (email, password) VALUES (?, ?)";
                $stmt = $baglanti->prepare($ekle_sorgu);
                $stmt->bind_param("ss", $email, $hashli_sifre);

                if ($stmt->execute()) {
                    $mesaj = "Kayıt başarıyla tamamlandı! Giriş yapabilirsiniz.";
                } else {
                    $hata = "Kayıt sırasında bir hata oluştu: " . $baglanti->error;
                }
            }
        }
    } else {
        $hata = "Lütfen tüm alanları doldurun.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol - Online Sanat Galerisi</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .register-container { width: 350px; margin: 80px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; font-family: sans-serif; }
        .error { color: #d9534f; background: #f2dede; padding: 10px; border-radius: 4px; margin-bottom: 10px; }
        .success { color: #3c763d; background: #dff0d8; padding: 10px; border-radius: 4px; margin-bottom: 10px; }
        input { width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 12px; background-color: #5cb85c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #4cae4c; }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Yeni Hesap Oluştur</h2>
    
    <?php if($hata) echo "<div class='error'>$hata</div>"; ?>
    <?php if($mesaj) echo "<div class='success'>$mesaj</div>"; ?>
    
    <form action="register.php" method="POST">
        <label>E-posta Adresi:</label>
        <input type="email" name="email" placeholder="ornek@mail.com" required>
        
        <label>Şifre:</label>
        <input type="password" name="password" placeholder="Şifreniz" required>
        
        <label>Şifre Tekrar:</label>
        <input type="password" name="password_confirm" placeholder="Şifrenizi tekrar girin" required>
        
        <button type="submit">Kayıt Ol</button>
    </form>
    <p style="text-align: center;">Zaten üye misin? <a href="login.php">Giriş Yap</a></p>
</div>

</body>
</html>