<?php
ob_start();
session_start();
include 'baglanti.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : null;
$artwork_id = isset($_GET['artwork_id']) ? (int)$_GET['artwork_id'] : null;

$item_title = "";
$item_price = 0;
$item_type = "";

if ($event_id) {
    $sorgu = mysqli_query($baglan, "SELECT * FROM events WHERE id = '$event_id'");
    $item = mysqli_fetch_assoc($sorgu);
    if ($item) { $item_title = $item['title']; $item_price = $item['price']; $item_type = "event"; }
} elseif ($artwork_id) {
    $sorgu = mysqli_query($baglan, "SELECT * FROM artworks WHERE id = '$artwork_id' AND status = 'available'");
    $item = mysqli_fetch_assoc($sorgu);
    if ($item) { $item_title = $item['title']; $item_price = $item['price']; $item_type = "artwork"; }
}

if (!$item) { header("Location: index.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Güvenli Ödeme - KTÜ Galeri</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; color: #888; font-size: 0.85rem; }
        .input-field { width: 100%; padding: 12px; background: #2c2c2c; border: 1px solid #444; color: white; border-radius: 8px; box-sizing: border-box; outline: none; }
        .input-field:focus { border-color: #f1c40f; }
        .coupon-box { display: flex; gap: 10px; margin-top: 10px; }
        .btn-apply { background: #444; color: white; border: none; padding: 0 15px; border-radius: 8px; cursor: pointer; font-size: 0.8rem; }
        .btn-apply:hover { background: #555; }
    </style>
</head>
<body style="background-color: #121212; color: white; font-family: 'Segoe UI', sans-serif;">

<div class="container" style="max-width: 500px; margin: 40px auto; padding: 20px;">
    <div class="payment-card" style="background: #1e1e1e; padding: 30px; border-radius: 20px; border: 1px solid #333; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
        
        <div style="margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between;">
            <a href="javascript:history.back()" style="color: #666; text-decoration: none; font-size: 0.9rem;">← Geri</a>
            <h2 style="color: #f1c40f; margin: 0; font-size: 1.5rem;">💳 Ödeme Onayı</h2>
            <div style="width: 30px;"></div>
        </div>
        
        <div style="background: linear-gradient(145deg, #262626, #1a1a1a); padding: 20px; border-radius: 15px; margin-bottom: 25px; border: 1px solid #333;">
            <p style="margin: 0; font-size: 0.75rem; color: #f1c40f; text-transform: uppercase; letter-spacing: 1.5px; font-weight: bold;">
                <?php echo ($item_type == "event") ? "Etkinlik Bileti" : "Sanat Eseri"; ?>
            </p>
            <h3 style="margin: 10px 0; color: #fff; font-size: 1.1rem;"><?php echo htmlspecialchars($item_title); ?></h3>
            
            <div style="margin-top: 15px; border-top: 1px solid #333; padding-top: 15px;">
                <label style="font-size: 0.8rem; color: #666;">İndirim Kuponu</label>
                <div class="coupon-box">
                    <input type="text" id="c_input" placeholder="Örn: KTU10" class="input-field" style="padding: 8px;">
                    <button type="button" class="btn-apply" onclick="checkCoupon()">Uygula</button>
                </div>
            </div>

            <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #888;">Toplam Tutar:</span>
                <span id="final_price" style="color: #27ae60; font-weight: bold; font-size: 1.4rem;">
                    <?php echo number_format($item_price, 2, ',', '.'); ?> TL
                </span>
            </div>
        </div>

        <form action="odeme_islem.php" method="POST">
            <input type="hidden" name="item_id" value="<?php echo ($event_id ?? $artwork_id); ?>">
            <input type="hidden" name="item_type" value="<?php echo $item_type; ?>">
            <input type="hidden" id="amount_input" name="amount" value="<?php echo $item_price; ?>">

            <div class="input-group">
                <label>Kart Sahibi</label>
                <input type="text" name="card_name" required placeholder="Ad Soyad" class="input-field">
            </div>

            <div class="input-group">
                <label>Kart Numarası</label>
                <input type="text" name="card_number" maxlength="16" required placeholder="0000 0000 0000 0000" class="input-field">
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 25px;">
                <div style="flex: 1;">
                    <label style="font-size: 0.85rem; color: #888; margin-bottom: 5px; display: block;">S.K.T</label>
                    <input type="text" name="expiry" placeholder="AA/YY" maxlength="5" required class="input-field">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 0.85rem; color: #888; margin-bottom: 5px; display: block;">CVV</label>
                    <input type="password" name="cvv" maxlength="3" required placeholder="***" class="input-field">
                </div>
            </div>

            <button type="submit" style="width:100%; padding:16px; background:#27ae60; color:white; border:none; border-radius:12px; font-weight:bold; cursor:pointer; font-size: 1rem; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);">
                Ödemeyi Onayla
            </button>
        </form>
    </div>
</div>

<script>
function checkCoupon() {
    const code = document.getElementById('c_input').value;
    const originalPrice = <?php echo $item_price; ?>;
    
    // Basit bir AJAX simülasyonu veya kontrolü
    // Senaryo: KTU10 kodu %10 indirim yapar
    if(code.toUpperCase() === 'KTU10') {
        const discount = originalPrice * 0.10;
        const newPrice = originalPrice - discount;
        document.getElementById('final_price').innerText = newPrice.toLocaleString('tr-TR') + ' TL';
        document.getElementById('amount_input').value = newPrice;
        alert('Kupon Uygulandı! %10 indirim kazandınız.');
    } else {
        alert('Geçersiz kupon kodu.');
    }
}
</script>

</body>
</html>