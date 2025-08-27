<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Admin — Delivery Zones</title>

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <!-- Leaflet Draw CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>

  <style>
    #map { height: 80vh; width: 100%; }
    .controls { margin: 0.5rem 0; }
  </style>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
  <div class="container" style="padding:12px;">
    <h2>Admin — Draw Delivery Zones</h2>

    <div class="controls">
      <label>Restaurant</label>
      <select id="restaurantSelect">
        <option value="">-- Select restaurant --</option>
        @foreach($restaurants as $r)
          <option value="{{ $r->id }}">{{ $r->name }}</option>
        @endforeach
      </select>

      <button id="btnLoadZones">Load existing zones</button>
      <button id="btnClearMap">Clear shapes</button>
    </div>

    <div id="map"></div>

    <div style="margin-top:8px;">
      <small>Draw polygons for custom shapes or circles for radius zones (circle center = lat/lng; radius in km).</small>
    </div>
  </div>

  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <!-- Leaflet Draw -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

  <script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    // initialize map
    const map = L.map('map').setView([23.8103, 90.4125], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // FeatureGroup to store editable layers
    const drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    // Draw control
    const drawControl = new L.Control.Draw({
      edit: {
        featureGroup: drawnItems,
        remove: true,
      },
      draw: {
        polyline: false,
        marker: false,
        rectangle: false,
        circlemarker: false,
        polygon: {
          allowIntersection: false,
          showArea: true,
        },
        circle: true  // used for radius zones
      }
    });
    map.addControl(drawControl);

    // When a new shape is created
    map.on(L.Draw.Event.CREATED, function (event) {
      const layer = event.layer;
      drawnItems.addLayer(layer);

      // Prompt to select restaurant if not chosen
      const restaurantId = document.getElementById('restaurantSelect').value;
      if (!restaurantId) {
        alert('Please select a restaurant first.');
        drawnItems.removeLayer(layer);
        return;
      }

      // Build payload for polygon or radius
      if (layer instanceof L.Polygon && !(layer instanceof L.Circle)) {
        // polygon -> array of lat/lng pairs
        const latlngs = layer.getLatLngs()[0].map(p => ({ lat: p.lat, lng: p.lng }));
        saveZone({
          restaurant_id: restaurantId,
          type: 'polygon',
          polygon: latlngs
        });
      } else if (layer instanceof L.Circle) {
        // circle -> center + radius in meters, convert to km
        const center = layer.getLatLng();
        const radiusMeters = layer.getRadius();
        const radiusKm = radiusMeters / 1000;
        saveZone({
          restaurant_id: restaurantId,
          type: 'radius',
          center_latitude: center.lat,
          center_longitude: center.lng,
          radius: radiusKm
        });
      }
    });

    // Save zone to server
    async function saveZone(payload) {
      try {
        const resp = await fetch("http://localhost:8000/zones", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify(payload)
        });

        const data = await resp.json();
        if (!resp.ok) {
          alert('Error saving zone: ' + (data.message || JSON.stringify(data)));
          return;
        }
        alert('Zone saved');
      } catch (err) {
        console.error(err);
        alert('Error saving zone');
      }
    }

    // Load existing zones
    document.getElementById('btnLoadZones').addEventListener('click', async () => {
      const restaurantId = document.getElementById('restaurantSelect').value;
      // load all zones (or filter by restaurant)
      const resp = await fetch("http://localhost:8000/zones/list");
      const zones = await resp.json();
      drawnItems.clearLayers();

      zones.forEach(z => {
        // if restaurant filter selected, skip others
        if (restaurantId && String(z.restaurant_id) !== String(restaurantId)) return;

        if (z.type === 'polygon' && Array.isArray(z.polygon)) {
          const latlngs = z.polygon.map(p => [p.lat ?? p[0], p.lng ?? p[1]]);
          const poly = L.polygon(latlngs, { color: '#3388ff' }).addTo(drawnItems);
          poly.zoneId = z.id;
        } else if (z.type === 'radius') {
          const center = [z.center_latitude, z.center_longitude];
          const circle = L.circle(center, { radius: (z.radius || 0) * 1000, color: '#ff8800' }).addTo(drawnItems);
          circle.zoneId = z.id;
        }
      });
    });

    document.getElementById('btnClearMap').addEventListener('click', () => {
      drawnItems.clearLayers();
    });

  </script>
</body>
</html>