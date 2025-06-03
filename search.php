<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MapClone - Search</title>
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
        .search-container {
            padding: 20px;
            text-align: center;
        }
        .search-bar {
            padding: 10px;
            width: 80%;
            max-width: 500px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }
        .search-button {
            padding: 10px 20px;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .search-button:hover {
            background: #1557b0;
        }
        #results {
            margin-top: 20px;
            font-size: 16px;
            color: #333;
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
    <div class="search-container">
        <h1>Search Locations</h1>
        <input type="text" id="searchInput" class="search-bar" placeholder="Enter location...">
        <button class="search-button" onclick="searchLocation()">Search</button>
        <div id="map"></div>
        <div id="results"></div>
    </div>
    <script>
        function redirect(page) {
            window.location.href = page;
        }
        var map = L.map('map').setView([51.505, -0.09], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        async function searchLocation() {
            const query = document.getElementById('searchInput').value;
            if (!query) return;
            const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&addressdetails=1`);
            const data = await response.json();
            const resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = '';
            map.eachLayer(layer => {
                if (layer instanceof L.Marker) map.removeLayer(layer);
            });
            if (data.length > 0) {
                const { lat, lon, display_name, address } = data[0];
                map.setView([lat, lon], 13);
                L.marker([lat, lon]).addTo(map).bindPopup(display_name).openPopup();
                resultsDiv.innerHTML = `
                    <p><strong>Name:</strong> ${display_name}</p>
                    <p><strong>Coordinates:</strong> ${lat}, ${lon}</p>
                    <p><strong>City:</strong> ${address.city || 'N/A'}</p>
                    <p><strong>Street:</strong> ${address.road || 'N/A'}</p>
                    <button onclick="saveFavorite('${display_name}', ${lat}, ${lon}, '${address.city || ''}', '${address.road || ''}')">Save to Favorites</button>
                `;
            } else {
                resultsDiv.innerHTML = '<p>No results found.</p>';
            }
        }

        function saveFavorite(name, lat, lon, city, street) {
            fetch('favorites.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=save&name=${encodeURIComponent(name)}&lat=${lat}&lon=${lon}&city=${encodeURIComponent(city)}&street=${encodeURIComponent(street)}`
            }).then(response => response.text()).then(data => {
                alert(data);
            });
        }
    </script>
</body>
</html>
