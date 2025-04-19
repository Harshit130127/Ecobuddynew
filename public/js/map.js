class MapManager {
    constructor() {
        this.map = L.map('map');
        this.userMarker = null;
        this.facilityMarkers = [];
        this.facilities = [];
        this.initMap();
    }

    
    initMap() {
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(this.map);

        // Immediately try to locate user with visual feedback
        this.createUserMarker([0, 0], "Locating you..."); // Temporary marker
        this.map.locate({
            setView: true,
            maxZoom: 15,
            timeout: 10000,
            enableHighAccuracy: true
        });

        // Move facility loading to location handlers
        this.map.on('locationfound', (e) => {
            this.handleLocationFound(e);
            this.fetchAllFacilities(); // Load facilities AFTER location found
        });

        this.map.on('locationerror', (e) => {
            this.handleLocationError(e);
            this.fetchAllFacilities(); // Load facilities AFTER error handling
        });

        this.initLocationButton();
        this.initSearch();
        // Removed fetchAllFacilities() from here
    }

    initSearch() {
        const searchInput = document.getElementById('searchInput');
        let timeout;
        
        searchInput.addEventListener('input', (e) => {
            clearTimeout(timeout);
            const term = e.target.value.trim();
            
            timeout = setTimeout(() => {
                this.moveToFacility(term);
                this.highlightFacility(term);
            }, 300);
        });
    }

    moveToFacility(searchTerm) {
        if (!searchTerm) return;

        const lowerTerm = searchTerm.toLowerCase();
        const match = this.facilities.find(facility => {
            return (
                facility.title?.toLowerCase().includes(lowerTerm) ||
                facility.category?.toLowerCase().includes(lowerTerm) ||
                facility.location?.toLowerCase().includes(lowerTerm)
            );
        });

        if (match) {
            const lat = this.parseCoordinate(match.latitude, 'latitude');
            const lng = this.parseCoordinate(match.longitude, 'longitude');
            
            if (!isNaN(lat) && !isNaN(lng)) {
                this.map.stop();
                const targetBounds = L.latLngBounds([lat, lng], [lat, lng]).pad(0.5);
                this.map.flyToBounds(targetBounds, {
                    animate: true,
                    duration: 1,
                    padding: [50, 50]
                });

                setTimeout(() => {
                    this.map.invalidateSize();
                    const targetMarker = this.facilityMarkers.find(m => 
                        m.getLatLng().lat === lat && 
                        m.getLatLng().lng === lng
                    );
                    if (targetMarker) {
                        targetMarker.setIcon(this.getDefaultIcon());
                        targetMarker.openPopup();
                    }
                }, 500);
            }
        }
    }

    highlightFacility(searchTerm) {
        const searchLower = searchTerm.toLowerCase();
        this.facilityMarkers.forEach(marker => {
            const title = marker.options.title || '';
            const shouldHighlight = title.toLowerCase().includes(searchLower);
            
            if (shouldHighlight) {
                const img = new Image();
                img.src = '/Ecobuddy/images/marker-icon-red.png';
                img.onload = () => {
                    marker.setIcon(L.icon({
                        iconUrl: '/Ecobuddy/images/marker-icon-red.png',
                        shadowUrl: '/Ecobuddy/images/marker-shadow.png',
                        iconSize: [25, 41]
                    }));
                };
            } else {
                marker.setIcon(this.getDefaultIcon());
            }
        });
    }

    clearMarkerGhosts() {
        const container = this.map.getContainer();
        const ghostMarkers = container.querySelectorAll('.leaflet-marker-icon:not([src])');
        ghostMarkers.forEach(ghost => ghost.remove());
    }

    createUserMarker(latlng, message) {
        const icon = L.icon({
            iconUrl: '/Ecobuddy/images/marker-icon.png',
            shadowUrl: '/Ecobuddy/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41],
            className: 'user-marker-pulse'
        });

        if (this.userMarker) {
            this.userMarker.setLatLng(latlng);
            this.userMarker.setPopupContent(message);
        } else {
            this.userMarker = L.marker(latlng, { icon })
                .bindPopup(message)
                .addTo(this.map)
                .openPopup();
        }
    }

    
    handleLocationFound(e) {
        this.createUserMarker(e.latlng, "Your Current Location");
        // Force zoom and maintain view even after facilities load
        this.map.setView(e.latlng, 15);
        if (this.userMarker) this.userMarker.openPopup();
        
        // Add slight padding around user location
        const userBounds = L.latLngBounds([e.latlng, e.latlng]).pad(0.02);
        this.map.flyToBounds(userBounds, {duration: 0.5});
    }


    handleLocationError(e) {
        const fallbackCenter = [53.4808, -2.2426];
        this.map.setView(fallbackCenter, 13);
        this.createUserMarker(
            fallbackCenter,
            'Location access denied. Click <b>"My Location"</b> to try again.'
        );
    }


    initLocationButton() {
        const locateBtn = document.getElementById('locateBtn');
        locateBtn?.addEventListener('click', () => {
            if (!this.userMarker) {
                this.createUserMarker(this.map.getCenter(), "Locating...");
            }
            this.map.locate({
                setView: true,
                maxZoom: 15,
                timeout: 15000,
            });
        });
    }

    async fetchAllFacilities() {
        try {
            const response = await fetch('/Ecobuddy/api/ecofacilities.php');
            this.facilities = await response.json();
            this.showAllFacilityMarkers();
            updateFacilitiesTable(this.facilities);
        } catch (error) {
            console.error('Error fetching facilities:', error);
        }
    }

    showAllFacilityMarkers() {
        this.facilityMarkers.forEach(m => this.map.removeLayer(m));
        this.facilityMarkers = [];
        const bounds = L.latLngBounds();
    
        this.facilities.forEach(facility => {
            const marker = this.createFacilityMarker(facility);
            if (marker) {
                marker.addTo(this.map);
                this.facilityMarkers.push(marker);
                bounds.extend(marker.getLatLng());
            }
        });
    
        // Only adjust view if no user location exists
        if (this.facilityMarkers.length > 0 && !this.userMarker) {
            this.map.fitBounds(bounds, {
                padding: [50, 50],
                maxZoom: 14
            });
        }
    }

    createFacilityMarker(facility) {
    const lat = this.parseCoordinate(facility.latitude, 'latitude');
    const lng = this.parseCoordinate(facility.longitude, 'longitude');
    if (isNaN(lat) || isNaN(lng)) return null;

    const popupContent = `
        <div class="map-popup">
            <button class="map-popup-close">&times;</button>
            <h4 class="map-popup-title">${escapeHtml(facility.title)}</h4>
            <div class="map-popup-section">
                <i class="fas fa-info-circle"></i>
                <span class="map-popup-status ${facility.status_of_facility === 'active' ? 'active' : 'inactive'}">
                    ${escapeHtml(facility.status_of_facility)}
                </span>
            </div>
            <div class="map-popup-section">
                <i class="fas fa-map-marker-alt"></i>
                ${escapeHtml(facility.location)}
            </div>
            <div class="map-popup-grid">
                <div class="map-popup-coordinates">
                    <div><span class="coord-label">Lat:</span> ${escapeHtml(facility.latitude)}</div>
                    <div><span class="coord-label">Lng:</span> ${escapeHtml(facility.longitude)}</div>
                </div>
                ${facility.photo ? `
                <div class="map-popup-photo">
                    <img src="${escapeHtml(facility.photo)}" 
                         alt="${escapeHtml(facility.title)}" 
                         onerror="this.src='https://via.placeholder.com/150'">
                </div>
                ` : ''}
            </div>
            ${facility.description ? `
            <div class="map-popup-section">
                <i class="fas fa-align-left"></i>
                <div class="map-popup-description">${escapeHtml(facility.description)}</div>
            </div>
            ` : ''}
        </div>
    `;

    const marker = L.marker([lat, lng], { 
        icon: this.getDefaultIcon(),
        title: facility.title 
    }).bindPopup(popupContent, { 
        closeButton: false,
        className: 'leaflet-popup-custom'
    });

    // Add click handler for custom close button
    marker.on('popupopen', () => {
        const popup = marker.getPopup();
        popup.getElement()
            .querySelector('.map-popup-close')
            .addEventListener('click', () => {
                this.map.closePopup(popup);
            });
    });

    return marker;
}

    getDefaultIcon() {
        return L.icon({
            iconUrl: '/Ecobuddy/images/marker-icon.png',
            shadowUrl: '/Ecobuddy/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
    }

    parseCoordinate(coordStr, type) {
        if (typeof coordStr !== 'string') return NaN;
        const numMatch = coordStr.match(/[\d.]+/);
        if (!numMatch) return NaN;

        let value = parseFloat(numMatch[0]);
        const direction = coordStr.trim().slice(-1).toUpperCase();

        if (type === 'latitude' && direction === 'S') value = -value;
        if (type === 'longitude' && direction === 'W') value = -value;

        return value;
    }
}

function updateFacilitiesTable(facilities) {
    const tbody = document.getElementById('facilitiesTableBody');
    if (!tbody) return;

    tbody.innerHTML = facilities?.length ? facilities.map(facility => `
        <tr>
            <td>${escapeHtml(facility.title)}</td>
            <td>${escapeHtml(facility.category)}</td>
            <td>${escapeHtml(facility.latitude)}</td>
            <td>${escapeHtml(facility.longitude)}</td>
        </tr>
    `).join('') : '<tr><td colspan="4" class="text-center">No facilities found</td></tr>';
}

function escapeHtml(text) {
    return text?.toString()?.replace(/[&<>"']/g, m => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;',
        '"': '&quot;', "'": '&#039;'
    }[m])) || '';
}

async function saveComment(event, facilityId) {
    event.preventDefault();
    const comment = document.getElementById(`comment-${facilityId}`).value;

    try {
        const response = await fetch(`/Ecobuddy/api/update-comment.php?id=${facilityId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ comment })
        });
        if (!response.ok) throw new Error('Failed to save comment');
        alert('Comment saved successfully!');
    } catch (error) {
        console.error('Error saving comment:', error);
        alert('Error saving comment. Please try again.');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.mapManager = new MapManager();
    
    const icons = [
        '/Ecobuddy/images/marker-icon.png',
        // '/Ecobuddy/images/marker-icon-red.png',
        '/Ecobuddy/images/marker-shadow.png'
    ];
    
    icons.forEach(url => {
        const img = new Image();
        img.src = url;
    });
});