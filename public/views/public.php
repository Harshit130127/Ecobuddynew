<?php
session_start();
require_once __DIR__ . '/../../controllers/ecofacilitycontroller.php';
require_once __DIR__ . '/../../config/config.php';

$ecoFacilityController = new EcoFacilityController();
$searchTerm = $_GET['search'] ?? '';
$facilities = $ecoFacilityController ->getFacilities($searchTerm);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Public Eco Facilities</title>

    <!-- Leaflet CSS -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        crossorigin=""
    />
    <!-- Leaflet JS -->
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        crossorigin=""
    ></script>

    <!-- Bootstrap & Font Awesome -->
    <link
        rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
    />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/public.css" />

    <style>
      /* Style for facility thumbnails */
      .facility-thumbnail {
          max-width: 100px;
          max-height: 60px;
          object-fit: cover;
          border-radius: 4px;
      }
      /* My Location button style */
      #locateBtn {
          position: absolute;
          top: 10px;
          right: 10px;
          z-index: 1000;
          background-color: white;
          border: 1px solid #ccc;
          padding: 6px 10px;
          border-radius: 4px;
          cursor: pointer;
          box-shadow: 0 2px 6px rgba(0,0,0,0.3);
      }
      #locateBtn:hover {
          background-color: #f8f9fa;
      }
    </style>
</head>
<body>
    <div class="container-fluid" style="position: relative;">
        <div id="map" style="height: 70vh; width: 100%;"></div>

        <!-- My Location Button -->
        <button id="locateBtn" title="Go to My Location">
          <i class="fas fa-location-arrow"></i> My Location
        </button>

        <div class="row my-4">
            <div class="col-md-8 offset-md-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-success text-white">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                    <input
                        type="text"
                        id="searchInput"
                        class="form-control"
                        placeholder="Search by title, category, or location"
                        autocomplete="off"
                    />
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col text-center">
                <button class="btn btn-info" onclick="toggleTable()">
                    Toggle Table View
                </button>
            </div>
        </div>

        <!-- Facilities Table -->
        <div class="row" id="facilitiesTable" style="display: none;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>No.</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Location</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Status</th>
                                <th>Photo</th>
                            </tr>
                        </thead>
                        <tbody id="facilitiesTableBody">
                            <!-- Rows will be dynamically inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        crossorigin="anonymous"
    ></script>
    <script
        src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
        crossorigin="anonymous"
    ></script>

    <!-- Your map.js script -->
    <script src="../js/map.js"></script>

    <script>
        // Toggle facilities table visibility
        function toggleTable() {
            const table = document.getElementById('facilitiesTable');
            table.style.display = table.style.display === 'none' ? 'block' : 'none ';
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Dynamically update facilities table rows
        function updateFacilitiesTable(facilities) {
            const tbody = document.getElementById('facilitiesTableBody');
            tbody.innerHTML = '';

            if (!facilities || facilities.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center">No facilities found.</td></tr>';
                return;
            }

            facilities.forEach((facility, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${escapeHtml(facility.title)}</td>
                    <td>${escapeHtml(facility.category)}</td>
                    <td>${escapeHtml(facility.description || '')}</td>
                    <td>${escapeHtml(facility.location || '')}</td>
                    <td>${escapeHtml(facility.latitude)}</td>
                    <td>${escapeHtml(facility.longitude)}</td>
                    <td>
                        <span class="badge ${facility.status_of_facility === 'active' ? 'badge-success' : 'badge-danger'}">
                            ${escapeHtml(facility.status_of_facility)}
                        </span>
                    </td>
                    <td>
                        ${facility.photo ? `<img src="${escapeHtml(facility.photo)}" alt="Facility Photo" class="facility-thumbnail" onerror="this.onerror=null;this.src='https://via.placeholder.com/150'">` : 'No Photo'}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // // Listen to search input and update map markers & table
        // document.getElementById('searchInput').addEventListener('input', function (e) {
        //     const searchTerm = e.target.value.trim();

            // Debounce to avoid too many requests
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(() => {
                fetch(`/Ecobuddy/api/ecofacilities.php?search=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(facilities => {
                        if (window.mapManager) {
                            window.mapManager.moveToFacility(searchTerm);
                        }
                        updateFacilitiesTable(facilities);
                    })
                    .catch(err => {
                        console.error('Error fetching facilities:', err);
                    });
            }, 300);
        

        // On page load, populate table with initial facilities
        document.addEventListener('DOMContentLoaded', () => {
            const initialFacilities = <?= json_encode($facilities) ?>;
            updateFacilitiesTable(initialFacilities);
        });

        // My Location button handler
        document.getElementById('locateBtn').addEventListener('click', () => {
            if (window.mapManager && window.mapManager.userMarker) {
                window.mapManager.map.setView(window.mapManager.userMarker.getLatLng(), 15);
                window.mapManager.userMarker.openPopup();
            } else if (window.mapManager) {
                window.mapManager.map.locate({ setView: true, maxZoom: 15 });
            }
        });
    </script>
</body>
</html>