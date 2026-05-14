<?php
session_start();
include 'baglanti.php';

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    $subject = mysqli_real_escape_string($baglan, $_POST['subject']);
    $message = mysqli_real_escape_string($baglan, $_POST['message']);

    $sorgu = "INSERT INTO support_tickets (user_id, subject, message, status) VALUES ('$user_id', '$subject', '$message', 'open')";
    if(mysqli_query($baglan, $sorgu)){
        echo "<script>alert('Talebiniz alındı!'); window.location.href='profile.php';</script>";
    }
}
?>