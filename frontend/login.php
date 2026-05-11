<?php
session_start();
require_once 'baglanti.php'; // Veritabanı bağlantı dosyan

$hata = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $sifre = $_POST['password'];

    if (!empty($email) && !empty($sifre)) {
        // SQL Enjeksiyonuna karşı koruma ve kullanıcıyı bulma
        $sorgu = "SELECT id, password FROM users WHERE email = ?";
        $stmt = $baglanti->prepare($sorgu);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $sonuc = $stmt->get_result();

        if ($sonuc->num_rows === 1) {
            $kullanici = $sonuc->fetch_assoc();
            
            // Şifre kontrolü (Şifrelerin password_hash ile saklandığını varsayıyoruz)
            if (password_verify($sifre, $kullanici['password'])) {
                $_SESSION['user_id'] = $kullanici['id'];
                header("Location: index.php"); // Giriş başarılıysa ana sayfaya yönlendir
                exit();
            } else {
                $hata = "Hatalı şifre girdiniz.";
            }
        } else {
            $hata = "Bu e-posta adresi ile kayıtlı kullanıcı bulunamadı.";
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
    <title>Giriş Yap - Online Sanat Galerisi</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .login-container { width: 300px; margin: 100px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .error { color: red; margin-bottom: 10px; }
        input { width: 100%; margin-bottom: 10px; padding: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #333; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Giriş Yap</h2>
    <?php if($hata) echo "<p class='error'>$hata</p>"; ?>
    
    <form action="login.php" method="POST">
        <label>E-posta:</label>
        <input type="email" name="email" required>
        
        <label>Şifre:</label>
        <input type="password" name="password" required>
        
        <button type="submit">Giriş Yap</button>
    </form>
    <p><a href="register.php">Hesabın yok mu? Kayıt ol.</a></p>
</div>

</body>
</html>