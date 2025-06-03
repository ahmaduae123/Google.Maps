<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MapClone - Directions</title>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
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
        .directions-container {
            padding: 20px;
            text-align: center;
        }
        .input-group {
            margin: 10px 0;
        }
        .input-group input {
            padding: 10px;
            width: 80%;
            max-width: 500px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }
        .directions-button {
            padding: 10px 20px;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .directions-button:hover {
            background: #1557b0;
        }
        #directions {
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
    <div class="directions-container">
        <h1>Get Directions</h1>
        <div class="input-group">
            <input type="text" id="start" placeholder="Starting location...">
        </div>
        <div class="input-group">
            <input type="text" id="end" placeholder="Destination...">
        </div>
        <button class="directions-button" onclick="getDirections()">Get Directions</button>
        <div id="map"></div>
        <div id="directions"></div>
    </div>
    <script>
        function redirect(page) {
            window.location.href = page;
        }
        var map = L.map('map').setView([51.505, -0.09], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        async function getDirections() {
            const start = document.getElementById('start').value;
            const end = document.getElementById('end').value;
            if (!start || !end) return;

            const startResp = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(start)}&format=json`);
            const endResp = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(end)}&format=json`);
            const startData = await startResp.json();
            const endData = await endResp.json();

            if (startData.length > 0 && endData.length > 0) {
                const startCoords = [startData[0].lat, startData[0].lon];
                const endCoords = [endData[0].lat, endData[0].lon];
                map.eachLayer(layer => {
                    if (layer instanceof L.Routing.Control) map.removeLayer(layer);
                });
                L.Routing.control({
                    waypoints: [
                        L.latLng(startCoords[0], startCoords[1]),
                        L.latLng(endCoords[0], endCoords[1])
                    ],
                    routeWhileDragging: true
                }).addTo(map);
            } else {
                document.getElementById('directions').innerHTML = '<p>Invalid start or end location.</p>';
            }
        }
    </script>
</body>
</html>
