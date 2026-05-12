<?php
include 'config.php';

$id = $_GET['id'];

// İstatistik güncelleme: Her tıklandığında görüntülenme sayısını 1 artır (Gereksinim 16)
$db->prepare("UPDATE artworks SET view_count = view_count + 1 WHERE id = ?")->execute([$id]);

// Eser bilgilerini ve sanatçı ismini çekelim (JOIN kullanarak)
$query = $db->prepare("SELECT artworks.*, users.full_name as artist_name 
                       FROM artworks 
                       JOIN users ON artworks.artist_id = users.id 
                       WHERE artworks.id = ?");
$query->execute([$id]);
$artwork = $query->fetch(PDO::FETCH_ASSOC);
?>