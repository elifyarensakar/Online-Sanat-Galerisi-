<?php
session_start();
session_unset();
session_destroy();

// Önbelleği temizlemek için (tarayıcının geri gitmesini önlemek adına)
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 

// Kullanıcıyı ana sayfaya gönder
header("Location: index.php");
exit();
?>