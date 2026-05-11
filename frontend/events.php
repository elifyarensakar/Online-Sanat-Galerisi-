<?php
include 'config.php';
session_start();

// Rezervasyon İşlemi
if (isset($_GET['book_id']) && isset($_SESSION['user_id'])) {
    $event_id = $_GET['book_id'];
    $user_id = $_SESSION['user_id'];

    // Kontenjan kontrolü
    $event = $db->query("SELECT capacity, current_participants FROM events WHERE id = $event_id")->fetch();
    
    if ($event['current_participants'] < $event['capacity']) {
        $db->prepare("INSERT INTO reservations (user_id, event_id, status) VALUES (?, ?, 'confirmed')")->execute([$user_id, $event_id]);
        $db->query("UPDATE events SET current_participants = current_participants + 1 WHERE id = $event_id");
        echo "Rezervasyon başarılı!";
    } else {
        echo "Maalesef kontenjan dolu.";
    }
}

$all_events = $db->query("SELECT * FROM events")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Etkinlikler ve Atölyeler</h2>
<?php foreach($all_events as $e): ?>
    <div>
        <h3><?php echo $e['title']; ?></h3>
        <p>Kontenjan: <?php echo $e['current_participants'] . "/" . $e['capacity']; ?></p>
        <a href="events.php?book_id=<?php echo $e['id']; ?>">Rezervasyon Yap</a>
    </div>
<?php endforeach; ?>