<?php
$host = "localhost";
$kullanici = "root";
$sifre = "";
$veritabani = "sanat_galerisi_db";

$baglan = mysqli_connect($host, $kullanici, $sifre, $veritabani);

if (!$baglan) {
    die("Veritabanı bağlantısı başarısız: " . mysqli_connect_error());
}

// Türkçe karakter sorunu yaşamamak için:
mysqli_set_charset($baglan, "utf8");

/* RAPOR NOTU:
Sistemdeki tüm tablolar InnoDB motoru kullanılarak 
Foreign Key (Yabancı Anahtar) ilişkileriyle birbirine bağlanmıştır. 
Bu sayede veri bütünlüğü korunmakta ve ilişkisel sorgular optimize edilmektedir.
*/
?>