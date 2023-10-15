<?php
session_start();
include 'php/config.php';

// Check if user has a favorites list in the session, if not, create one
if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

// Handle the action when a user tries to add a song to favorites
if (isset($_GET['addToFav'])) {
    $songIdToAdd = intval($_GET['addToFav']);
    if (!in_array($songIdToAdd, $_SESSION['favorites'])) {
        $_SESSION['favorites'][] = $songIdToAdd;
    }
    header("Location: browse.php");
    exit;
}

// Initialize results array
$results = [];

// Create an empty array for active filters
$activeFilters = [];

// Prepare SQL query based on filters
$whereClause = [];
$queryParams = [];

if (isset($_GET['query']) && !empty($_GET['query'])) {
    $whereClause[] = "(s.title LIKE :query OR a.artist_name LIKE :query OR g.genre_name LIKE :query)";
    $queryParams['query'] = '%' . $_GET['query'] . '%';
    $activeFilters['query'] = $_GET['query'];
}

if (isset($_GET['artist']) && !empty($_GET['artist'])) {
    $whereClause[] = "a.artist_name = :artist";
    $queryParams['artist'] = $_GET['artist'];
    $activeFilters['artist'] = $_GET['artist'];
}

if (isset($_GET['genre']) && !empty($_GET['genre'])) {
    $whereClause[] = "s.genre_id = :genre";
    $queryParams['genre'] = $_GET['genre'];
    $genreNameQuery = $db->prepare("SELECT genre_name FROM genres WHERE genre_id = :genre_id");
    $genreNameQuery->execute(['genre_id' => $_GET['genre']]);
    $genreName = $genreNameQuery->fetchColumn();
    $activeFilters['genre'] = $genreName;
}

if (isset($_GET['year']) && !empty($_GET['year'])) {
    if (isset($_GET['year_option']) && in_array($_GET['year_option'], ['=', '<', '>'])) {
        $operator = $_GET['year_option'];
        $whereClause[] = "s.year $operator :year";
        $queryParams['year'] = $_GET['year'];
        $activeFilters['year'] = $operator . ' ' . $_GET['year'];
    }
}

$where = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";
$stmt = $db->prepare("
    SELECT s.song_id, s.title, a.artist_name, s.year, g.genre_name, s.popularity
    FROM songs s
    INNER JOIN artists a ON s.artist_id = a.artist_id
    INNER JOIN genres g ON s.genre_id = g.genre_id
$where
    ORDER BY s.popularity DESC
");
$stmt->execute($queryParams);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse / Search Results</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Browse / Search Results</h1>
    <header>
        <?php include 'php/header.php'; ?>
    </header>
    <div class="content">
    <div class="page-title">
            Browse Music
        </div>
        <div class="filters-container">
            Current filter/search criteria:
            <?php foreach ($activeFilters as $filter => $value): ?>
                <span class="filter-tag">
                    <?php echo ucfirst($filter) . ': ' . $value; ?>
                    <a href="?<?php echo http_build_query(array_diff_key($_GET, [$filter => $value])); ?>">X</a>
                </span>
            <?php endforeach; ?>
            <button class="filter-button" onclick="window.location.href='browse.php'">Show All</button>
        </div>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Artist</th>
                <th>Year</th>
                <th>Genre</th>
                <th>Popularity</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $song): ?>
                <tr>
                <td><a href="song_details.php?id=<?php echo $song['song_id']; ?>"><?php echo strlen($song['title']) > 25 ? substr($song['title'], 0, 24) . '&hellip;' : $song['title']; ?></a></td>
                    <td><?= $song['artist_name'] ?></td>
                    <td><?= $song['year'] ?></td>
                    <td><?= $song['genre_name'] ?></td>
                    <td><?= $song['popularity'] ?></td>
                    <td>
                        <?php if (!in_array($song['song_id'], $_SESSION['favorites'])): ?>
                            <a href="browse.php?addToFav=<?php echo $song['song_id']; ?>">
                                <img src="images/icon-heart.png" alt="Add to Favorites" class="favorite-icon">
                            </a>
                        <?php else: ?>
                            <img src="images/icon-heart-full.png" alt="Already in Favorites" class="favorite-icon-disabled">
                        <?php endif; ?>
                    </td>
                    <td><a href="song_details.php?id=<?php echo $song['song_id']; ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php include 'php/footer.php'; ?>
</body>
