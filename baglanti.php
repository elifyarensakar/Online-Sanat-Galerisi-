<?php
// Veritabanı bilgilerini değişkenlere atıyoruz
$host = "localhost";
$kullanici = "root";
$sifre = "";
$veritabani = "sanat_galerisi_db";

// Bağlantıyı kuruyoruz
$baglan = mysqli_connect($host, $kullanici, $sifre, $veritabani);

// Bağlantı kontrolü
if (!$baglan) {
    die("Veritabanı bağlantısı başarısız: " . mysqli_connect_error());
}

// Türkçe karakter sorunu yaşamamak için karakter setini ayarlıyoruz
mysqli_set_charset($baglan, "utf8");

/**
 * 💡 GELİŞTİRİCİ NOTU:
 * Beyaz ekran sorunlarını önlemek için, geliştirme aşamasında 
 * PHP'nin tüm hataları ekrana basmasını sağlıyoruz. 
 * Proje bittiğinde bu iki satırı silebilirsin.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* RAPOR NOTU:
Sistemdeki tüm tablolar InnoDB motoru kullanılarak 
Foreign Key (Yabancı Anahtar) ilişkileriyle birbirine bağlanmıştır. 
Bu sayede veri bütünlüğü korunmakta ve ilişkisel sorgular optimize edilmektedir.
*/
?>