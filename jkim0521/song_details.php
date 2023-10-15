<?php
session_start();
include 'php/config.php';

// Check if song_id is set
if (isset($_GET['id'])) {
    $song_id = $_GET['id'];

    // Prepare a statement to retrieve the song details
    $stmt = $db->prepare("
        SELECT s.title, a.artist_name, s.year, g.genre_name, s.popularity, s.duration
        FROM songs s
        INNER JOIN artists a ON s.artist_id = a.artist_id
        INNER JOIN genres g ON s.genre_id = g.genre_id
        WHERE s.song_id = :id
    ");

    $stmt->execute(['id' => $song_id]);

    // Fetch the result
    $song = $stmt->fetch(PDO::FETCH_ASSOC);
}

function formatDuration($seconds) {
    $minutes = floor($seconds / 60);
    $seconds = $seconds % 60;
    return "{$minutes}m {$seconds}s";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Single Song Details</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/song-details-styles.css">
</head>

<body>
    <h1>Single Song Details</h1>
    <header>
        <?php include 'php/header.php'; ?>
    </header>
    <div class="content">
        <?php if (isset($song) && $song): ?>
            <div class="song-details">
                <div class="song-title">
                    Title: <?= $song['title'] ?>
                </div>
                <div class="song-subdetails">
                    <?= $song['artist_name'] ?>, <?= $song['year'] ?>
                </div>
                <div class="song-attributes">
                    Genre: <?= $song['genre_name'] ?><br>
                    Popularity: <?= $song['popularity'] ?><br>
                </div>
                <div class="duration">
                    <?= formatDuration($song['duration']) ?>
                </div>
            </div>
        <?php else: ?>
            <p>Song not found!</p>
        <?php endif; ?>
    </div>
    <?php include 'php/footer.php'; ?>
</body>
</html>
