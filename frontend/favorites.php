<?php 
// Veritabanı bağlantısını üst klasörden çekiyoruz
include '../baglanti.php'; 

// Veritabanından eserleri çekmek için sorgu yapıyoruz
// Şimdilik id=1 olan eseri çekelim, ilerde tüm favorilerini listeleyeceğiz
$sorgu = mysqli_query($baglan, "SELECT * FROM artworks WHERE id = 1");
$eser = mysqli_fetch_assoc($sorgu);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/style.css">
    <title>Favorilerim</title>
</head>
<body>
    <h2>Beğendiğim Eserler</h2>
    <div class="favorite-list">
        
        <?php if($eser): ?>
            <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
                <img src="https://via.placeholder.com/150" alt="Sanat Eseri">
                
                <h3><?php echo $eser['title']; ?></h3>
                
                <p><?php echo $eser['description']; ?></p>
                
                <p><strong>Fiyat: </strong><?php echo $eser['price']; ?> TL</p>
                
                <button style="background-color: red; color:white; border:none; padding:5px 10px; cursor:pointer;">
                    Favorilerden Çıkar
                </button>
            </div>
        <?php else: ?>
            <p>Henüz favori eseriniz bulunmuyor veya veritabanına veri eklenmemiş.</p> 
        <?php endif; ?>

    </div>
</body>
</html>