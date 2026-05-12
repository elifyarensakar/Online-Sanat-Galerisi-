<?php
session_start();
include 'baglanti.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($baglan, $_POST['full_name']);
    $email = mysqli_real_escape_string($baglan, $_POST['email']);
    
    // Şifreyi güvenli hale getiriyoruz
    $password_raw = $_POST['password'];
    $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);

    // BURASI KRİTİK: Sütun adını senin tablondaki gibi 'password_hash' yaptık
    $sql = "INSERT INTO users (full_name, email, password_hash, role) VALUES ('$full_name', '$email', '$hashed_password', 'customer')";
    
    if (mysqli_query($baglan, $sql)) {
        header("Location: login.php?kayit=basarili");
        exit();
    } else {
        $hata = "Veritabanı Hatası: " . mysqli_error($baglan);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol - KTÜ Sanat Galerisi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #121212; margin: 0;">

    <div class="auth-card" style="background: #1e1e1e; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); width: 100%; max-width: 400px; border: 1px solid #333;">
        <h2 style="color: #f1c40f; text-align: center; margin-bottom: 30px;">Kayıt Ol</h2>
        
        <?php if(isset($hata)): ?>
            <div style="background: rgba(231, 76, 60, 0.2); color: #e74c3c; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; border: 1px solid #e74c3c;">
                <?php echo $hata; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div style="margin-bottom: 20px;">
                <label style="color: #ccc; display: block; margin-bottom: 8px;">Ad Soyad</label>
                <input type="text" name="full_name" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #333; background: #2c2c2c; color: white; box-sizing: border-box; outline: none;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="color: #ccc; display: block; margin-bottom: 8px;">E-posta</label>
                <input type="email" name="email" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #333; background: #2c2c2c; color: white; box-sizing: border-box; outline: none;">
            </div>

            <div style="margin-bottom: 30px;">
                <label style="color: #ccc; display: block; margin-bottom: 8px;">Şifre</label>
                <input type="password" name="password" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #333; background: #2c2c2c; color: white; box-sizing: border-box; outline: none;">
            </div>

            <button type="submit" style="width: 100%; padding: 14px; border-radius: 25px; border: none; background: #27ae60; color: white; font-weight: bold; cursor: pointer; transition: 0.3s; font-size: 1rem;">
                Hesabı Oluştur
            </button>
        </form>

        <p style="text-align: center; margin-top: 25px; color: #888;">
            Zaten hesabın var mı? <a href="login.php" style="color: #3498db; text-decoration: none;">Giriş Yap</a>
        </p>
    </div>

</body>
</html>