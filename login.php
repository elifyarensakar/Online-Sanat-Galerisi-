<?php
session_start();
include 'baglanti.php'; 

$hata = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($baglan, $_POST['email']);
    $sifre = $_POST['password'];

    if (!empty($email) && !empty($sifre)) {
        // Sütun adını password_hash olarak güncelledik ve full_name'i de çektik
        $sorgu = "SELECT id, full_name, password_hash FROM users WHERE email = ?";
        $stmt = $baglan->prepare($sorgu);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $sonuc = $stmt->get_result();

        if ($sonuc->num_rows === 1) {
            $kullanici = $sonuc->fetch_assoc();
            
            // password_verify ile hash'lenmiş şifreyi kontrol ediyoruz
            if (password_verify($sifre, $kullanici['password_hash'])) {
                $_SESSION['user_id'] = $kullanici['id'];
                $_SESSION['full_name'] = $kullanici['full_name']; // Navbar'da isim görünmesi için
                
                header("Location: index.php"); 
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
    <title>Giriş Yap - KTÜ Sanat Galerisi</title>
    <link rel="stylesheet" href="style.css"> </head>
<body style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #121212; margin: 0;">

<div class="auth-card" style="background: #1e1e1e; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); width: 100%; max-width: 400px; border: 1px solid #333;">
    <h2 style="color: #f1c40f; text-align: center; margin-bottom: 30px;">Giriş Yap</h2>
    
    <?php if($hata): ?>
        <div style="background: rgba(231, 76, 60, 0.2); color: #e74c3c; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; border: 1px solid #e74c3c; font-size: 0.9rem;">
            <?php echo $hata; ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['kayit']) && $_GET['kayit'] == 'basarili'): ?>
        <div style="background: rgba(39, 174, 96, 0.2); color: #27ae60; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; border: 1px solid #27ae60; font-size: 0.9rem;">
            Kayıt başarılı! Şimdi giriş yapabilirsiniz.
        </div>
    <?php endif; ?>
    
    <form action="login.php" method="POST">
        <div style="margin-bottom: 20px;">
            <label style="color: #ccc; display: block; margin-bottom: 8px;">E-posta:</label>
            <input type="email" name="email" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #333; background: #2c2c2c; color: white; box-sizing: border-box; outline: none;">
        </div>
        
        <div style="margin-bottom: 30px;">
            <label style="color: #ccc; display: block; margin-bottom: 8px;">Şifre:</label>
            <input type="password" name="password" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #333; background: #2c2c2c; color: white; box-sizing: border-box; outline: none;">
        </div>
        
        <button type="submit" style="width: 100%; padding: 14px; border-radius: 25px; border: none; background: #27ae60; color: white; font-weight: bold; cursor: pointer; transition: 0.3s; font-size: 1rem;">Giriş Yap</button>
    </form>
    
    <p style="text-align: center; margin-top: 25px; color: #888;">
        Hesabın yok mu? <a href="register.php" style="color: #3498db; text-decoration: none; font-weight: bold;">Kayıt ol.</a>
    </p>
</div>

</body>
</html>