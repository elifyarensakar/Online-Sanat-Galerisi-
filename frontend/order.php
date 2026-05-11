<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

// Satın Alma Tetikleyici (Basit bir senaryo)
if (isset($_POST['buy_artwork_id'])) {
    $art_id = $_POST['buy_artwork_id'];
    $price = $_POST['amount'];
    
    $db->prepare("INSERT INTO orders (user_id, artwork_id, total_price, status) VALUES (?, ?, ?, 'completed')")
       ->execute([$user_id, $art_id, $price]);
    echo "Satın alım başarılı!";
}

$my_orders = $db->prepare("SELECT orders.*, artworks.title FROM orders 
                          JOIN artworks ON orders.artwork_id = artworks.id 
                          WHERE orders.user_id = ?");
$my_orders->execute([$user_id]);
?>

<h2>Sipariş Geçmişim</h2>
<ul>
    <?php foreach($my_orders->fetchAll() as $order): ?>
        <li><?php echo $order['title']; ?> - <?php echo $order['total_price']; ?> TL - Durum: <?php echo $order['status']; ?></li>
    <?php endforeach; ?>
</ul>