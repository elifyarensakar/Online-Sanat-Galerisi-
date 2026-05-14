<?php
session_start();
include 'baglanti.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $artwork_id = mysqli_real_escape_string($baglan, $_POST['artwork_id']);
    $comment_text = mysqli_real_escape_string($baglan, $_POST['comment_text']);
    $rating = (int)$_POST['rating'];

    // Ödev Gereksinimi: item_type genellikle 'artwork' olur
    $sql = "INSERT INTO comments (user_id, item_id, item_type, comment_text, rating) 
            VALUES ('$user_id', '$artwork_id', 'artwork', '$comment_text', '$rating')";

    if (mysqli_query($baglan, $sql)) {
        header("Location: detay.php?id=" . $artwork_id);
        exit();
    } else {
        echo "Hata: " . mysqli_error($baglan);
    }
} else {
    header("Location: index.php");
    exit();
}
?>