import './bootstrap';

// Map functionality
document.addEventListener('DOMContentLoaded', function() {
    // Check if Leaflet is available
    if (typeof L !== 'undefined') {
        initializeMap();
    }
});

function initializeMap() {
    const mapData = document.getElementById('map-data');
    if (!mapData) return;

    const warehouseIcon = L.icon({
        iconUrl: 'https://icons.iconarchive.com/icons/double-j-design/origami-colored-pencil/256/red-home-icon.png',
        iconSize:     [42, 42],
        iconAnchor:   [16, 16],
        popupAnchor:  [0, -32]
    });

    const deliveryIcon = L.icon({
        iconUrl: 'https://icons.iconarchive.com/icons/custom-icon-design/pretty-office-11/256/truck-icon.png',
        iconSize:     [42, 42],
        iconAnchor:   [16, 16],
        popupAnchor:  [0, -32]
    });

    const warehouseCoords = [
        parseFloat(mapData.dataset.warehouseLat),
        parseFloat(mapData.dataset.warehouseLng)
    ];

    const deliveryCoords = [
        parseFloat(mapData.dataset.deliveryLat),
        parseFloat(mapData.dataset.deliveryLng)
    ];

    const radius = parseFloat(mapData.dataset.radius);

    const map = L.map('map').setView(warehouseCoords, 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const warehouseMarker = L.marker(warehouseCoords, { icon: warehouseIcon }).addTo(map)
        .bindPopup('<b>Warehouse Location</b>');

    const featureGroupItems = [warehouseMarker];

    const hasDeliveryData = deliveryCoords[0] !== warehouseCoords[0] || deliveryCoords[1] !== warehouseCoords[1];

    if (hasDeliveryData) {
        const deliveryMarker = L.marker(deliveryCoords, { icon: deliveryIcon }).addTo(map)
            .bindPopup('<b>Last Delivery Location</b>');
        featureGroupItems.push(deliveryMarker);
    }

    if (radius > 0) {
        const proximityCircle = L.circle(warehouseCoords, {
            color: 'green',
            fillColor: '#28a745',
            fillOpacity: 0.2,
            radius: radius
        }).addTo(map);
        featureGroupItems.push(proximityCircle);
    }

    if (featureGroupItems.length > 1) {
        const group = new L.featureGroup(featureGroupItems);
        map.fitBounds(group.getBounds().pad(0.2));
    }
}
