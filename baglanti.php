<?php
$host = "localhost";
$kullanici = "root";
$sifre = "";
$veritabani = "sanat_galerisi_db";

$baglan = mysqli_connect($host, $kullanici, $sifre, $veritabani);

if (!$baglan) {
    die("Veritabani bağlantisi başarisiz: " . mysqli_connect_error());
}
// Türkçe karakter sorunu yaşamamak için:
mysqli_set_charset($baglan, "utf8");
?>

/*Sistemdeki tüm tablolar InnoDB motoru kullanilarak
 Foreign Key (Yabanci Anahtar) ilişkileriyle 
 birbirine bağlanmiştir. Bu sayede veri bütünlüğü
  korunmakta ve ilişkisel sorgular optimize edilmektedir.
  */