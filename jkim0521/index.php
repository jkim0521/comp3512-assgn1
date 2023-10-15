<?php
session_start();
include 'php/config.php';

function fetchAndDisplayData($db, $query, $title) {
    $stmt = $db->prepare($query);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "<ul>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>" . $row['title'] . " by " . $row['artist_name'] . "</li>";
        }
        echo "</ul>";
    } else {
        //echo "<p>No data found for " . htmlspecialchars($title) . "!</p>";
    }
}

$category = isset($_GET['category']) ? $_GET['category'] : null;
$results = [];

if ($category) {
    switch ($category) {
        case 'top_genres':
            $query = "
                SELECT g.genre_name, COUNT(s.song_id) AS song_count
                FROM songs s
                INNER JOIN genres g ON s.genre_id = g.genre_id
                GROUP BY g.genre_name
                ORDER BY song_count DESC
                LIMIT 10;
            ";
            break;
        
        case 'top_artists':
            $query = "
                SELECT a.artist_name, COUNT(s.song_id) AS song_count
                FROM songs s
                INNER JOIN artists a ON s.artist_id = a.artist_id
                GROUP BY a.artist_name
                ORDER BY song_count DESC
                LIMIT 10;
            ";
            break;

        case 'most_popular_songs':
            $query = "
                SELECT s.title, a.artist_name, s.popularity
                FROM songs s
                INNER JOIN artists a ON s.artist_id = a.artist_id
                ORDER BY s.popularity DESC 
                LIMIT 10;
            ";
            break;

        case 'one_hit_wonders':
            $query = "
                WITH ArtistSongCount AS (
                    SELECT a.artist_id, COUNT(s.song_id) AS song_count
                    FROM songs s
                    INNER JOIN artists a ON s.artist_id = a.artist_id
                    GROUP BY a.artist_id
                    HAVING song_count = 1
                )
                SELECT s.title, a.artist_name, s.popularity
                FROM songs s
                INNER JOIN artists a ON s.artist_id = a.artist_id
                INNER JOIN ArtistSongCount asc ON a.artist_id = asc.artist_id
                ORDER BY s.popularity DESC            
                LIMIT 10;
            ";
            break;

        case 'longest_acoustic_songs':
            $query = "
                SELECT s.title, a.artist_name, s.duration
                FROM songs s
                INNER JOIN artists a ON s.artist_id = a.artist_id
                WHERE s.acousticness > 40
                ORDER BY s.duration DESC
                LIMIT 10;";
            break;

        case 'at_the_club':
            $query = "
                SELECT s.title, a.artist_name, (s.danceability*1.6 + s.energy*1.4) AS club_suitability
                FROM songs s
                INNER JOIN artists a ON s.artist_id = a.artist_id
                WHERE s.danceability > 80
                ORDER BY club_suitability DESC
                LIMIT 10;
            ";
            break;

        case 'running_songs':
            $query = "
                SELECT s.title, a.artist_name, (s.energy*1.3 + s.valence*1.6) AS run_suitability
                FROM songs s
                INNER JOIN artists a ON s.artist_id = a.artist_id
                WHERE s.bpm BETWEEN 120 AND 125
                ORDER BY run_suitability DESC
                LIMIT 10;
            ";
            break;

        case 'studying_songs':
            $query = "
                SELECT s.title, a.artist_name, (s.acousticness*0.8 + (100-s.speechiness) + (100-s.valence)) AS study_suitability
                FROM songs s
                INNER JOIN artists a ON s.artist_id = a.artist_id
                WHERE s.bpm BETWEEN 100 AND 115 AND s.speechiness BETWEEN 1 AND 20
                ORDER BY study_suitability DESC
                LIMIT 10;
            ";
            break;

        default:
            // Default to an empty query if the category doesn't match any known categories
            $query = "";
    }

    // If a query was set, execute it and fetch the results
    if ($query) {
        $stmt = $db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Database</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/section-styles.css">
</head>

<body>
    <header>
        <?php include 'php/header.php'; ?>
    </header>

    <div class="content">

    <!-- Homepage Title & Description -->
    <div class="homepage-header">
        <h1 class="page-title">Homepage</h1>
        <p class="page-description">This project aims to create a dynamic music web application 
            that categorizes songs form a database based on different criteria such as genre, artist popularity and other 
            specific attributes.
        </p>
    </div>

        <!-- Top Genres -->
        <section style="background-image: url('images/section1.png');" onclick="window.location.href='index.php?category=top_genres'">
        <h2>Top Genres</h2>
        <?php if ($category === "top_genres") fetchAndDisplayData($db, $query, "Top Genres"); ?>
</section>


        <!-- Top Artists -->
        <section style="background-image: url('images/section2.png');" onclick="window.location.href='index.php?category=top_artists'">
        <h2>Top Artists</h2>
        <?php if ($category === "top_artists") fetchAndDisplayData($db, $query, "Top Artists"); ?>
</section>

        <!-- Most Popular Songs -->
        <section style="background-image: url('images/section3.png');" onclick="window.location.href='index.php?category=most_popular_songs'">
        <h2>Most Popular Songs</h2>
        <?php if ($category === "most_popular_songs") fetchAndDisplayData($db, $query, "Most Popular Songs"); ?>
</section>


        <!-- One-Hit Wonders -->        
        <section style="background-image: url('images/section4.png');" onclick="window.location.href='index.php?category=one_hit_wonders'">
        <h2>One-hit Wonders</h2>
        <?php if ($category === "one_hit_wonders") fetchAndDisplayData($db, $query, "One-hit Wonders"); ?>
</section>

        <!-- Longest Acoustic Songs -->  
        <section style="background-image: url('images/section5.png');" onclick="window.location.href='index.php?category=longest_acoustic_songs'">
        <h2>Longest Acoustic Songs</h2>
        <?php if ($category === "longest_acoustic_songs") fetchAndDisplayData($db, $query, "Longest Acoustic Songs"); ?>
</section>
        
        <!-- At the Club -->
        <section style="background-image: url('images/section6.png');" onclick="window.location.href='index.php?category=at_the_club'">
        <h2>At the Club</h2>
        <?php if ($category === "at_the_club") fetchAndDisplayData($db, $query, "At the Club"); ?>
</section>


        <!-- Running Songs -->
        <section style="background-image: url('images/section7.png');" onclick="window.location.href='index.php?category=running_songs'">
        <h2>Running Songs</h2>
        <?php if ($category === "running_songs") fetchAndDisplayData($db, $query, "Running Songs"); ?>
</section>


        <!-- Studying -->
        <section style="background-image: url('images/section8.png');" onclick="window.location.href='index.php?category=studying_songs'">
        <h2>Studying Songs</h2>
        <?php if ($category === "studying_songs") fetchAndDisplayData($db, $query, "Studying Songs"); ?>
</section>


    </div>

    <!-- Results Display Section -->
<div class="results-container">
    <h2>Results for <?php echo htmlspecialchars($category); ?></h2>
    
    <?php if (count($results) > 0): ?>
        <ol>
            <?php foreach ($results as $row): ?>
                <!-- Check if the keys exist in the $row array before accessing them -->
                <li>
                    <strong>Song:</strong> <?php echo isset($row['title']) ? htmlspecialchars($row['title']) : 'N/A'; ?>
                    <br>
                    <strong>Artist:</strong> <?php echo isset($row['artist_name']) ? htmlspecialchars($row['artist_name']) : 'N/A'; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php else: ?>
        <!-- Display a message if no results were found -->
        <p>No results found for this category.</p>
    <?php endif; ?>
</div>
    <?php include 'php/footer.php'; ?>

</body>
</html>
