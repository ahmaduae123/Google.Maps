<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    $name = $_POST['name'];
    $lat = $_POST['lat'];
    $lon = $_POST['lon'];
    $city = $_POST['city'];
    $street = $_POST['street'];
    $user_id = session_id(); // Using session_id as a simple user identifier
    $stmt = $conn->prepare("INSERT INTO favorites (user_id, location_name, latitude, longitude, city, street) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $name, $lat, $lon, $city, $street]);
    echo "Location saved!";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM favorites WHERE user_id = ?");
$stmt->execute([session_id()]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MapClone - Favorites</title>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #e0eafc, #cfdef3);
            height: 100vh;
            overflow: hidden;
        }
        #map {
            height: 60vh;
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .navbar {
            background: #1a73e8;
            padding: 15px;
            color: white;
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .navbar a:hover {
            background: #1557b0;
        }
        .favorites-container {
            padding: 20px;
            text-align: center;
        }
        .favorites-list {
            margin-top: 20px;
            font-size: 16px;
            color: #333;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .favorite-item {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .favorite-item button {
            padding: 5px 10px;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .favorite-item button:hover {
            background: #1557b0;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="#" onclick="redirect('index.php')">Home</a>
        <a href="#" onclick="redirect('search.php')">Search</a>
        <a href="#" onclick="redirect('directions.php')">Directions</a>
        <a href="#" onclick="redirect('favorites.php')">Favorites</a>
    </div>
    <div class="favorites-container">
        <h1>Your Favorite Locations</h1>
        <div id="map"></div>
        <div class="favorites-list">
            <?php foreach ($favorites as $fav): ?>
                <div class="favorite-item">
                    <span><?php echo htmlspecialchars($fav['location_name']); ?> (<?php echo $fav['latitude']; ?>, <?php echo $fav['longitude']; ?>)</span>
                    <button onclick="showOnMap(<?php echo $fav['latitude']; ?>, <?php echo $fav['longitude']; ?>, '<?php echo addslashes($fav['location_name']); ?>')">View</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function redirect(page) {
            window.location.href = page;
        }
        var map = L.map('map').setView([51.505, -0.09], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        function showOnMap(lat, lon, name) {
            map.setView([lat, lon], 13);
            map.eachLayer(layer => {
                if (layer instanceof L.Marker) map.removeLayer(layer);
            });
            L.marker([lat, lon]).addTo(map).bindPopup(name).openPopup();
        }
    </script>
</body>
</html>
