import './bootstrap';

// Map functionality
document.addEventListener('DOMContentLoaded', function() {
    // Check if Leaflet is available
    if (typeof L !== 'undefined') {
        initializeMap();
    }
});

function initializeMap() {
    // Get the data element
    const mapData = document.getElementById('map-data');

    // Get warehouse coordinates from data attributes
    const warehouseCoords = [
        parseFloat(mapData.dataset.warehouseLat) || 0,
        parseFloat(mapData.dataset.warehouseLng) || 0
    ];

    // Get the last delivery coordinates from data attributes
    const deliveryCoords = [
        parseFloat(mapData.dataset.deliveryLat) || warehouseCoords[0],
        parseFloat(mapData.dataset.deliveryLng) || warehouseCoords[1]
    ];

    // Initialize the map
    const map = L.map('map').setView(warehouseCoords, 15);

    // Add the tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Add warehouse marker
    const warehouseMarker = L.marker(warehouseCoords).addTo(map)
        .bindPopup('<b>Warehouse Location</b>');

    // Add delivery marker if coordinates exist and are different from warehouse
    if (deliveryCoords[0] !== warehouseCoords[0] || deliveryCoords[1] !== warehouseCoords[1]) {
        const deliveryMarker = L.marker(deliveryCoords).addTo(map)
            .bindPopup('<b>Last Delivery Location</b>');

        // Fit map to markers
        const group = new L.featureGroup([warehouseMarker, deliveryMarker]);
        map.fitBounds(group.getBounds().pad(0.5));
    }
}
