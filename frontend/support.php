<?php include 'baglanti.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Müşteri Destek</title>
</head>
<body>
    <h2>İletişim Formu</h2>
    <form action="destek_gonder.php" method="POST">
        <input type="text" name="subject" placeholder="Konu" required><br><br>
        <textarea name="message" placeholder="Sorunuzu buraya yazın..."></textarea><br><br>
        <button type="submit">Gönder</button>
    </form>

    <hr>
    <h3>Destek Taleplerim</h3>
    <table border="1">
        <tr>
            <th>Talep No</th>
            <th>Konu</th>
            <th>Durum</th>
        </tr>
        <tr>
            <td>1</td>
            <td>Ödeme Sorunu</td>
            <td>Yanıtlandı</td>
        </tr>
    </table>
</body>
</html>