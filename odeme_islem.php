<?php
ob_start();
session_start();
include 'baglanti.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id    = $_SESSION['user_id'];
    $item_id    = mysqli_real_escape_string($baglan, $_POST['item_id']);
    $item_type  = mysqli_real_escape_string($baglan, $_POST['item_type']); 
    $amount     = mysqli_real_escape_string($baglan, $_POST['amount']);

    // image_85ed96.png görüntüsündeki GERÇEK tablo yapına göre güncelledim:
    // artwork_id: Satın alınan ürünün ID'si
    // total_price: Senin tablondaki ücret sütunu adı
    // payment_method: Varsayılan olarak 'Kart' atıyoruz
    
    // Not: Eğer bir etkinlik alınıyorsa artwork_id kısmına etkinlik ID'sini kaydediyoruz.
    $sql_order = "INSERT INTO orders (user_id, artwork_id, total_price, payment_method) 
                  VALUES ('$user_id', '$item_id', '$amount', 'Kredi Kartı')";
    
    if (mysqli_query($baglan, $sql_order)) {
        
        if ($item_type == "artwork") {
            // Eser satıldı işaretle
            mysqli_query($baglan, "UPDATE artworks SET status = 'sold' WHERE id = '$item_id'");
        } else if ($item_type == "event") {
            // Rezervasyon ve katılımcı güncelleme
            mysqli_query($baglan, "INSERT INTO reservations (user_id, event_id, status) VALUES ('$user_id', '$item_id', 'confirmed')");
            mysqli_query($baglan, "UPDATE events SET current_participants = current_participants + 1 WHERE id = '$item_id'");
        }

        // Başarılı yönlendirme
        echo "<script>
                alert('Ödeme Alındı! KTÜ Galeri keyifli günler diler.'); 
                window.location.href='index.php';
              </script>";
        exit();

    } else {
        // Eğer hala hata alırsan burası sana nedenini söyleyecek
        die("Veritabanı Yazma Hatası: " . mysqli_error($baglan));
    }
}
ob_end_flush();
?>