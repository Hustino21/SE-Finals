<?php
// Start user session
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include required files
require_once 'config/database.php';
require_once 'includes/logger.php';

$current_user = $_SESSION['username'];
$search_term = '';
$params = [];

// Base queries for fetching data
$artist_query = "SELECT * FROM artists";
$album_query = "SELECT albums.album_id, albums.album_title, albums.release_year, albums.added_by, albums.last_updated, artists.artist_name 
                FROM albums JOIN artists ON albums.artist_id = artists.artist_id";
$disco_query = "SELECT a.artist_name, b.album_title, b.release_year 
                FROM artists a 
                LEFT JOIN albums b ON a.artist_id = b.artist_id";

// Handle search functionality
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $search_term = trim($_GET['search']);
    
    // Log search activity
    logActivity($pdo, $current_user, 'READ', "Searched database for: " . $search_term);
    
    // Append search filters to queries
    $search_sql = " WHERE artist_name LIKE ? OR genre LIKE ?";
    $album_search_sql = " WHERE album_title LIKE ? OR artists.artist_name LIKE ?";
    $disco_search_sql = " WHERE a.artist_name LIKE ? OR a.genre LIKE ? OR b.album_title LIKE ?";
    
    $artist_query .= $search_sql;
    $album_query .= $album_search_sql;
    $disco_query .= $disco_search_sql;
    
    // Set parameters for prepared statements
    $params = ["%$search_term%", "%$search_term%"];
    $disco_params = ["%$search_term%", "%$search_term%", "%$search_term%"];
}

// Order discography by artist and release year
$disco_query .= " ORDER BY a.artist_name ASC, b.release_year DESC";

// Execute artist query
$stmt_artists = $pdo->prepare($artist_query);
$stmt_artists->execute(isset($_GET['search']) ? $params : []);
$artists = $stmt_artists->fetchAll();

// Execute album query
$stmt_albums = $pdo->prepare($album_query);
$stmt_albums->execute(isset($_GET['search']) ? $params : []);
$albums = $stmt_albums->fetchAll();

// Execute discography query
$stmt_disco = $pdo->prepare($disco_query);
$stmt_disco->execute(isset($_GET['search']) ? $disco_params : []);
$discography_raw = $stmt_disco->fetchAll();

// Group discography by artist
$discography = [];
foreach ($discography_raw as $row) {
    $artist = $row['artist_name'];
    if (!isset($discography[$artist])) {
        $discography[$artist] = [];
    }
    if ($row['album_title']) {
        $discography[$artist][] = $row['album_title'] . " (" . $row['release_year'] . ")";
    }
}

// Fetch all artists for dropdowns
$all_artists = $pdo->query("SELECT * FROM artists")->fetchAll();

// Set page title and load header
$page_title = "Dashboard - Music Label Manager";
require_once 'includes/header.php'; 
?>

<!-- Search Bar Section -->
<div class="card search-card">
    <form method="GET" style="display: flex; gap: 15px; align-items: center; width: 100%;">
        <strong style="white-space: nowrap;">Search System:</strong>
        <input type="text" name="search" placeholder="Search by artist, genre, or album..." value="<?= htmlspecialchars($search_term) ?>" style="flex: 1; margin: 0;">
        <button type="submit" style="margin: 0;">Search</button>
        <?php if($search_term): ?>
            <a href="index.php"><button type="button" class="btn-secondary" style="margin: 0;">Clear</button></a>
        <?php endif; ?>
    </form>
</div>

<!-- Forms for adding data -->
<div class="dashboard-section">
    <h3><span class="section-icon">➕</span> Data Entry</h3>
    <div class="grid-2">
        <div class="card form-card">
            <h4>Register New Artist</h4>
            <form action="actions/artist_actions.php" method="POST">
                <label>Artist Name:</label>
                <input type="text" name="artist_name" required>
                <label>Genre:</label>
                <input type="text" name="genre" required>
                <label>Date Signed:</label>
                <input type="date" name="date_signed" required>
                <button type="submit" name="add_artist" class="full-width">Add Artist</button>
            </form>
        </div>

        <div class="card form-card">
            <h4>Publish New Album</h4>
            <form action="actions/album_actions.php" method="POST">
                <label>Select Artist:</label>
                <select name="artist_id" required>
                    <option value="">-- Choose an Artist --</option>
                    <?php foreach ($all_artists as $artist): ?>
                        <option value="<?= $artist['artist_id'] ?>"><?= htmlspecialchars($artist['artist_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Album Title:</label>
                <input type="text" name="album_title" required>
                <label>Release Year:</label>
                <input type="number" name="release_year" required>
                <button type="submit" name="add_album" class="full-width">Add Album</button>
            </form>
        </div>
    </div>
</div>

<!-- Discography summary table -->
<div class="dashboard-section">
    <h3><span class="section-icon">📊</span> Discography Overview</h3>
    <div class="card table-card">
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Artist Name</th>
                    <th>Albums Released</th>
                    <th style="width: 10%; text-align: center;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($discography as $artist_name => $albums_list): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($artist_name) ?></strong></td>
                        <td>
                            <?php if (empty($albums_list)): ?>
                                <em style="color: var(--text-secondary);">No albums registered yet.</em>
                            <?php else: ?>
                                <ul style="list-style-type: none; padding: 0; margin: 0;">
                                    <?php foreach ($albums_list as $album_item): ?>
                                        <li style="padding: 4px 0; color: var(--text-primary);">
                                            <span style="color: var(--accent-color); margin-right: 8px;">●</span> 
                                            <?= htmlspecialchars($album_item) ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge"><?= count($albums_list) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($discography)): ?>
                    <tr><td colspan="3" style="text-align: center;">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Detailed records tables -->
<div class="dashboard-section">
    <h3><span class="section-icon">⚙️</span> Database Management</h3>
    
    <div class="card table-card" style="margin-bottom: 20px;">
        <h4 style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid var(--surface-border);">Artist Records (Parent)</h4>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Artist Name</th>
                        <th>Genre</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($artists as $artist): ?>
                        <tr>
                            <td><?= $artist['artist_id'] ?></td>
                            <td><strong><?= htmlspecialchars($artist['artist_name']) ?></strong></td>
                            <td><?= htmlspecialchars($artist['genre']) ?></td>
                            <td>
                                <form action="actions/artist_actions.php" method="POST" class="inline-form">
                                    <input type="hidden" name="artist_id" value="<?= $artist['artist_id'] ?>">
                                    <input type="text" name="new_artist_name" class="small-input" value="<?= htmlspecialchars($artist['artist_name']) ?>" required>
                                    <input type="text" name="new_genre" class="tiny-input" value="<?= htmlspecialchars($artist['genre']) ?>" required>
                                    <button type="submit" name="update_artist">Update</button>
                                </form>
                                <form action="actions/artist_actions.php" method="POST" class="inline-form">
                                    <input type="hidden" name="artist_id" value="<?= $artist['artist_id'] ?>">
                                    <button type="submit" name="delete_artist" class="btn-danger" onclick="return confirm('WARNING: Deleting an artist will also delete all their albums. Proceed?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card table-card">
        <h4 style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid var(--surface-border);">Album Records (Child)</h4>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Album Title</th>
                        <th>Artist</th>
                        <th>Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($albums as $album): ?>
                        <tr>
                            <td><?= $album['album_id'] ?></td>
                            <td><strong><?= htmlspecialchars($album['album_title']) ?></strong></td>
                            <td><?= htmlspecialchars($album['artist_name']) ?></td>
                            <td><?= $album['release_year'] ?></td>
                            <td>
                                <form action="actions/album_actions.php" method="POST" class="inline-form">
                                    <input type="hidden" name="album_id" value="<?= $album['album_id'] ?>">
                                    <input type="text" name="new_title" class="small-input" value="<?= htmlspecialchars($album['album_title']) ?>" required>
                                    <input type="number" name="new_year" class="tiny-input" value="<?= $album['release_year'] ?>" required>
                                    <button type="submit" name="update_album">Update</button>
                                </form>
                                <form action="actions/album_actions.php" method="POST" class="inline-form">
                                    <input type="hidden" name="album_id" value="<?= $album['album_id'] ?>">
                                    <button type="submit" name="delete_album" class="btn-danger" onclick="return confirm('Are you sure you want to delete this album?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>