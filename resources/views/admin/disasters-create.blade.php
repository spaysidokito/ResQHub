<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Disaster - ResQHub Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #000;
            color: #fff;
            min-height: 100vh;
        }
        .header {
            background: linear-gradient(to right, #7f1d1d, #000);
            border-bottom: 2px solid #ef4444;
            padding: 1rem 2rem;
        }
        .header-content {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h1 { font-size: 1.875rem; color: #ef4444; }
        .btn {
            padding: 0.5rem 1rem;
            background: #1f2937;
            color: #fff;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: background 0.3s;
        }
        .btn:hover { background: #374151; }
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .card {
            background: #111827;
            border: 2px solid #ef4444;
            border-radius: 0.5rem;
            padding: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #9ca3af;
            font-weight: 500;
        }
        input, select, textarea {
            width: 100%;
            padding: 0.75rem;
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 0.5rem;
            color: #fff;
            font-size: 1rem;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #ef4444;
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: #ef4444;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-submit:hover {
            background: #dc2626;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        @media (max-width: 768px) {
            .grid { grid-template-columns: 1fr; }
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            background: #7f1d1d;
            border: 1px solid #ef4444;
        }
        #map {
            height: 400px;
            width: 100%;
            border-radius: 0.5rem;
            border: 2px solid #374151;
            margin-bottom: 1rem;
        }
        .map-instructions {
            background: #1f2937;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            color: #9ca3af;
            font-size: 0.875rem;
            border: 1px solid #374151;
        }
        .map-instructions strong {
            color: #ef4444;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1>Create New Disaster</h1>
            <a href="/admin/disasters" class="btn">← Back</a>
        </div>
    </header>

    <div class="container">
        <div class="card">
            @if(session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif

            <form action="/admin/disasters" method="POST">
                @csrf

                <div class="form-group">
                    <label>Disaster Type *</label>
                    <select name="type" required>
                        <option value="">Select Type</option>
                        <option value="flood">🌊 Flood</option>
                        <option value="typhoon">🌀 Typhoon</option>
                        <option value="fire">🔥 Fire</option>
                        <option value="earthquake">🌍 Earthquake</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Disaster Name *</label>
                    <input type="text" name="name" required placeholder="e.g., Typhoon Uwan">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Describe the disaster..."></textarea>
                </div>

                <div class="form-group">
                    <label>Location on Map *</label>
                    <div class="map-instructions">
                        <strong>📍 Click on the map</strong> to select the disaster location. The coordinates and location name will be filled automatically.
                    </div>
                    <div id="map"></div>
                </div>

                <div class="grid">
                    <div class="form-group">
                        <label>Latitude *</label>
                        <input type="number" step="0.000001" name="latitude" id="latitude" required placeholder="Click on map" readonly>
                    </div>
                    <div class="form-group">
                        <label>Longitude *</label>
                        <input type="number" step="0.000001" name="longitude" id="longitude" required placeholder="Click on map" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label>Location Name *</label>
                    <input type="text" name="location" id="location" required placeholder="Will be filled from map">
                </div>

                <div class="grid">
                    <div class="form-group">
                        <label>Severity *</label>
                        <select name="severity" required>
                            <option value="">Select Severity</option>
                            <option value="low">Low</option>
                            <option value="moderate">Moderate</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="active">Active</option>
                            <option value="monitoring">Monitoring</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                </div>

                <input type="hidden" name="external_id" value="admin_{{ uniqid() }}">
                <input type="hidden" name="country" value="Philippines">
                <input type="hidden" name="source" value="Admin">
                <input type="hidden" name="started_at" value="{{ now() }}">

                <button type="submit" class="btn-submit">Create Disaster</button>
            </form>
        </div>
    </div>

    <script>
        // Initialize map centered on Philippines
        const map = L.map('map').setView([12.8797, 121.7740], 6);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        let marker = null;

        // Handle map clicks
        map.on('click', async function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            // Update form fields
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);

            // Remove existing marker
            if (marker) {
                map.removeLayer(marker);
            }

            // Add new marker
            marker = L.marker([lat, lng]).addTo(map);

            // Reverse geocode to get location name
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                const data = await response.json();

                let locationName = '';
                if (data.address) {
                    // Try to get city, town, or village
                    locationName = data.address.city ||
                                 data.address.town ||
                                 data.address.village ||
                                 data.address.municipality ||
                                 data.address.county ||
                                 data.address.state ||
                                 'Unknown Location';
                }

                document.getElementById('location').value = locationName;
                marker.bindPopup(`<b>${locationName}</b><br>Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}`).openPopup();
            } catch (error) {
                console.error('Error getting location name:', error);
                document.getElementById('location').value = 'Unknown Location';
                marker.bindPopup(`Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}`).openPopup();
            }
        });

        // Allow manual editing of location name
        document.getElementById('location').addEventListener('input', function() {
            this.removeAttribute('readonly');
        });
    </script>
</body>
</html>
