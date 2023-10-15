<?php
session_start();
include 'php/config.php';

$genre_filter = '';

// Fetch genres for dropdown
$stmt = $db->prepare("SELECT genre_id, genre_name FROM genres");
$stmt->execute();
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
$search_query = $_GET['query'] ?? '';

// Fetch artists for dropdown
$stmt = $db->prepare("SELECT artist_id, artist_name FROM artists ORDER BY artist_name");
$stmt->execute();
$artists = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the user has submitted a search
if (isset($_GET['query']) || isset($_GET['genre']) || isset($_GET['year']) || isset($_GET['year_option']) || isset($_GET['artist'])) {
    header("Location: browse.php?" . $_SERVER['QUERY_STRING']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - Music Database</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/search-styles.css">
</head>

<body>
    <div class="search-container">
    <h1>Song Search</h1>
    <header>
        <?php include 'php/header.php'; ?>
    </header>
    <!-- Start of the form tag -->
    <form action="browse.php" method="GET">

    <!-- Song Title Search -->
    <label for="query">Title:</label>
    <input type="text" name="query" id="query" placeholder="Enter song title">

    <!-- Artist Dropdown -->
    <label for="artist">Artist:</label>
    <select name="artist" id="artist">
    <option value="" selected>Any</option>
    <?php foreach ($artists as $artist): ?>
        <option value="<?php echo $artist['artist_name']; ?>">
            <?php echo $artist['artist_name']; ?>
        </option>
    <?php endforeach; ?>
    </select>

    <!-- Genre Dropdown -->
    <label for="genre">Genre:</label>
    <select name="genre" id="genre">
    <option value="" selected>Any</option>
    <?php foreach ($genres as $genre): ?>
        <option value="<?php echo $genre['genre_id']; ?>">
            <?php echo $genre['genre_name']; ?>
        </option>
    <?php endforeach; ?>
    </select>

    <!-- Year Dropdown and Input -->
    <label for="year_option">Year:</label>
    <select name="year_option" id="year_option">
        <option value="" selected>Any</option>
        <option value="=">Equals To</option>
        <option value="<">Less Than</option>
        <option value=">">Greater Than</option>
    </select>
    <input type="number" name="year" id="year" placeholder="Enter year">

    <!-- Search Button -->
    <button type="submit">Search</button>
</div>
    <?php include 'php/footer.php'; ?>
</body>
</html>
