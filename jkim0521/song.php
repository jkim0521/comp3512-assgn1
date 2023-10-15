<?php
session_start();
    include 'php/config.php';

    // Check if song_id is provided
    if(isset($_GET['song_id'])){
        $song_id = $_GET['song_id'];

        // Prepare SQL statement
        $stmt = $pdo->prepare('SELECT * FROM songs WHERE id = ?');
        $stmt->execute([$song_id]);

        $song = $stmt->fetch();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Single Song Details</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <?php if(isset($song)): ?>
        <h1><?php echo $song['title']; ?></h1>
        <p>Artist: <?php echo $song['artist']; ?></p>
        <p>Genre: <?php echo $song['genre']; ?></p>
        <p>Year: <?php echo $song['year']; ?></p>
        <p>Popularity: <?php echo $song['popularity']; ?></p>
        <p>Acousticness: <?php echo $song['acousticness']; ?></p>
        <p>Danceability: <?php echo $song['danceability']; ?></p>
        <p>Duration: <?php echo $song['duration']; ?> minutes</p>
        <p>Energy: <?php echo $song['energy']; ?></p>
        <p>BPM: <?php echo $song['bpm']; ?></p>
        <p>Speechiness: <?php echo $song['speechiness']; ?></p>
        <p>Valence: <?php echo $song['valence']; ?></p>
    <?php else: ?>
        <p>Song not found.</p>
    <?php endif; ?>
</body>
</html>