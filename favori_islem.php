<?php
session_start();
include 'baglanti.php'; // Senin hazırladığın baglanti.php dosyasını çağırıyor

// Kullanıcı giriş yapmış mı ve bir eser ID'si gelmiş mi kontrol et
if (isset($_SESSION['user_id']) && isset($_POST['artwork_id'])) {
    $u_id = $_SESSION['user_id'];
    $a_id = mysqli_real_escape_string($baglan, $_POST['artwork_id']);

    // Bu eser zaten favorilerde mi?
    $kontrol = mysqli_query($baglan, "SELECT * FROM favorites WHERE user_id = '$u_id' AND artwork_id = '$a_id'");

    if (mysqli_num_rows($kontrol) > 0) {
        // Zaten varsa: Favorilerden SİL (Geri çekme işlemi)
        mysqli_query($baglan, "DELETE FROM favorites WHERE user_id = '$u_id' AND artwork_id = '$a_id'");
        echo "Favorilerden çıkarıldı!";
    } else {
        // Yoksa: Favorilere EKLE
        mysqli_query($baglan, "INSERT INTO favorites (user_id, artwork_id) VALUES ('$u_id', '$a_id')");
        echo "Favorilere eklendi!";
    }
} else {
    echo "Hata: Giriş yapmanız gerekiyor.";
}
?>