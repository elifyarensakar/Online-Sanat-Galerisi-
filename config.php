<?php
$host = "localhost";
$user = "root"; // XAMPP kullanıyorsan varsayılan budur
$pass = "";     // XAMPP kullanıyorsan varsayılan boştur
$db_name = "sanat_galerisi_db";

try {
    $db = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}
?>