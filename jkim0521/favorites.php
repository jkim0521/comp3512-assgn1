<?php
session_start();
include 'php/config.php';

// Favorites stored in session
$favorites = isset($_SESSION['favorites']) ? $_SESSION['favorites'] : [];

if(isset($_GET['remove'])) {
    $song_id_to_remove = $_GET['remove'];
    $key = array_search($song_id_to_remove, $favorites);
    if($key !== false) {
        unset($favorites[$key]);
        $favorites = array_values($favorites); // Re-indexing the array
        // Update the session.
        $_SESSION['favorites'] = $favorites;
        // Redirect to avoid issues with refreshing
        header('Location: favorites.php');
        exit();
    }
}

$songs = [];

if (!empty($favorites)) {
    $placeholders = str_repeat('?,', count($favorites) - 1) . '?';
    $stmt = $db->prepare("
        SELECT s.song_id, s.title, a.artist_name, s.year, g.genre_name, s.popularity 
        FROM songs s
        INNER JOIN artists a ON s.artist_id = a.artist_id
        INNER JOIN genres g ON s.genre_id = g.genre_id
        WHERE s.song_id IN ($placeholders)
    ");
    $stmt->execute($favorites);
    $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorites - Music Database</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <header>
        <?php include 'php/header.php'; ?>
    </header>
    <div class="content">
    <h1>My Favorites</h1>
    <?php if (!empty($songs)): ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Artist</th>
                    <th>Year</th>
                    <th>Genre</th>
                    <th>Remove</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($songs as $song): ?>
                    <tr>
                        <td><?= $song['title'] ?></td>
                        <td><?= $song['artist_name'] ?></td>
                        <td><?= $song['year'] ?></td>
                        <td><?= $song['genre_name'] ?></td>
                        <td>
                            <a href="favorites.php?remove=<?= $song['song_id'] ?>" class="remove-fav-icon">
                                <img src="images/icon-heart-full.png" alt="Remove from favorites" class="heart-icon">
                            </a>
                        </td>
                        <td><a href="song_details.php?id=<?php echo $song['song_id']; ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No favorites added yet.</p>
    <?php endif; ?>
    </div>
    <?php include 'php/footer.php'; ?>
</body>
