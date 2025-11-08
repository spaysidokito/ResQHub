<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Disasters - ResQHub Admin</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
            max-width: 1280px;
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
            display: inline-block;
        }
        .btn:hover { background: #374151; }
        .container {
            max-width: 1280px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .filters {
            background: #111827;
            border: 2px solid #374151;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        select {
            padding: 0.5rem;
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 0.5rem;
            color: #fff;
        }
        .disaster-card {
            background: #111827;
            border: 2px solid #374151;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: border-color 0.3s;
        }
        .disaster-card:hover {
            border-color: #ef4444;
        }
        .disaster-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }
        .disaster-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .disaster-meta {
            color: #9ca3af;
            font-size: 0.875rem;
        }
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-critical { background: rgba(220, 38, 38, 0.2); color: #dc2626; border: 1px solid #dc2626; }
        .badge-high { background: rgba(249, 115, 22, 0.2); color: #f97316; border: 1px solid #f97316; }
        .badge-moderate { background: rgba(234, 179, 8, 0.2); color: #eab308; border: 1px solid #eab308; }
        .badge-low { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; }
        .badge-active { background: rgba(59, 130, 246, 0.2); color: #3b82f6; border: 1px solid #3b82f6; }
        .badge-monitoring { background: rgba(234, 179, 8, 0.2); color: #eab308; border: 1px solid #eab308; }
        .badge-resolved { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; }
        .disaster-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #374151;
        }
        .detail-item {
            font-size: 0.875rem;
        }
        .detail-label {
            color: #9ca3af;
            margin-bottom: 0.25rem;
        }
        .detail-value {
            color: #fff;
            font-weight: 500;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }
        .loading {
            text-align: center;
            padding: 4rem 2rem;
        }
        .spinner {
            border: 3px solid #374151;
            border-top: 3px solid #ef4444;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #374151;
            flex-wrap: wrap;
        }
        .action-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .action-btn-edit {
            background: #3b82f6;
            color: white;
        }
        .action-btn-edit:hover {
            background: #2563eb;
        }
        .action-btn-resolve {
            background: #10b981;
            color: white;
        }
        .action-btn-resolve:hover {
            background: #059669;
        }
        .action-btn-delete {
            background: #ef4444;
            color: white;
        }
        .action-btn-delete:hover {
            background: #dc2626;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: #111827;
            border: 2px solid #ef4444;
            border-radius: 0.5rem;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-header {
            font-size: 1.5rem;
            color: #ef4444;
            margin-bottom: 1.5rem;
            font-weight: bold;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            color: #9ca3af;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 0.375rem;
            color: #fff;
        }
        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }
        .modal-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
        .modal-actions button {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 0.375rem;
            font-weight: 600;
            cursor: pointer;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1>All Disasters</h1>
            <a href="/admin/disasters" class="btn">← Back to Dashboard</a>
        </div>
    </header>

    <div class="container">
        <div class="filters">
            <select id="typeFilter" onchange="filterDisasters()">
                <option value="">All Types</option>
                <option value="flood">Floods</option>
                <option value="typhoon">Typhoons</option>
                <option value="fire">Fires</option>
                <option value="earthquake">Earthquakes</option>
            </select>
            <select id="statusFilter" onchange="filterDisasters()">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="monitoring">Monitoring</option>
                <option value="resolved">Resolved</option>
            </select>
            <select id="severityFilter" onchange="filterDisasters()">
                <option value="">All Severity</option>
                <option value="critical">Critical</option>
                <option value="high">High</option>
                <option value="moderate">Moderate</option>
                <option value="low">Low</option>
            </select>
        </div>

        <div id="disasters-container">
            <div class="loading">
                <div class="spinner"></div>
                <p>Loading disasters...</p>
            </div>
        </div>
    </div>

    <script>
        let allDisasters = [];

        async function loadDisasters() {
            try {
                const response = await fetch('/admin/disasters/list', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                allDisasters = data.data;
                displayDisasters(allDisasters);
            } catch (error) {
                console.error('Error loading disasters:', error);
                document.getElementById('disasters-container').innerHTML =
                    '<div class="empty-state"><p>Error loading disasters. Please try again.</p></div>';
            }
        }

        function displayDisasters(disasters) {
            const container = document.getElementById('disasters-container');

            if (disasters.length === 0) {
                container.innerHTML = '<div class="empty-state"><p>No disasters found</p></div>';
                return;
            }

            container.innerHTML = disasters.map(disaster => `
                <div class="disaster-card">
                    <div class="disaster-header">
                        <div>
                            <div class="disaster-title">
                                ${getTypeIcon(disaster.type)} ${disaster.name}
                                ${!disaster.is_verified ? '<span class="badge" style="background: rgba(234, 179, 8, 0.2); color: #eab308; border: 1px solid #eab308;">Unverified</span>' : ''}
                            </div>
                            <div class="disaster-meta">
                                <span class="material-icons" style="font-size: 14px; vertical-align: middle;">location_on</span> ${disaster.location} • ${new Date(disaster.created_at).toLocaleDateString()}
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <span class="badge badge-${disaster.severity}">${disaster.severity.toUpperCase()}</span>
                            <span class="badge badge-${disaster.status}">${disaster.status.toUpperCase()}</span>
                        </div>
                    </div>
                    <p style="color: #9ca3af; margin-bottom: 1rem;">${disaster.description || 'No description'}</p>
                    <div class="disaster-details">
                        <div class="detail-item">
                            <div class="detail-label">Type</div>
                            <div class="detail-value">${disaster.type.charAt(0).toUpperCase() + disaster.type.slice(1)}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Source</div>
                            <div class="detail-value">${disaster.source}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Started</div>
                            <div class="detail-value">${new Date(disaster.started_at).toLocaleString()}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Coordinates</div>
                            <div class="detail-value">${disaster.latitude.toFixed(4)}, ${disaster.longitude.toFixed(4)}</div>
                        </div>
                    </div>
                    <div class="actions">
                        <button onclick="editDisaster(${disaster.id})" class="action-btn action-btn-edit" style="display: flex; align-items: center; gap: 0.25rem;"><span class="material-icons" style="font-size: 16px;">edit</span> Edit</button>
                        ${disaster.status !== 'resolved' ? `<button onclick="resolveDisaster(${disaster.id})" class="action-btn action-btn-resolve" style="display: flex; align-items: center; gap: 0.25rem;"><span class="material-icons" style="font-size: 16px;">check_circle</span> Mark Resolved</button>` : ''}
                        <button onclick="deleteDisaster(${disaster.id})" class="action-btn action-btn-delete" style="display: flex; align-items: center; gap: 0.25rem;"><span class="material-icons" style="font-size: 16px;">delete</span> Delete</button>
                    </div>
                </div>
            `).join('');
        }

        function getTypeIcon(type) {
            const icons = {
                flood: '<span class="material-icons" style="font-size: 20px; vertical-align: middle; color: #3b82f6;">water</span>',
                typhoon: '<span class="material-icons" style="font-size: 20px; vertical-align: middle; color: #6366f1;">cyclone</span>',
                fire: '<span class="material-icons" style="font-size: 20px; vertical-align: middle; color: #f97316;">local_fire_department</span>',
                earthquake: '<span class="material-icons" style="font-size: 20px; vertical-align: middle; color: #eab308;">public</span>'
            };
            return icons[type] || '<span class="material-icons" style="font-size: 20px; vertical-align: middle; color: #ef4444;">warning</span>';
        }

        function filterDisasters() {
            const type = document.getElementById('typeFilter').value;
            const status = document.getElementById('statusFilter').value;
            const severity = document.getElementById('severityFilter').value;

            let filtered = allDisasters;

            if (type) {
                filtered = filtered.filter(d => d.type === type);
            }
            if (status) {
                filtered = filtered.filter(d => d.status === status);
            }
            if (severity) {
                filtered = filtered.filter(d => d.severity === severity);
            }

            displayDisasters(filtered);
        }

        // Load disasters on page load
        loadDisasters();

        // Edit disaster
        let currentDisaster = null;

        function editDisaster(id) {
            currentDisaster = allDisasters.find(d => d.id === id);
            if (!currentDisaster) return;

            document.getElementById('edit-name').value = currentDisaster.name;
            document.getElementById('edit-description').value = currentDisaster.description || '';
            document.getElementById('edit-latitude').value = currentDisaster.latitude;
            document.getElementById('edit-longitude').value = currentDisaster.longitude;
            document.getElementById('edit-location').value = currentDisaster.location;
            document.getElementById('edit-severity').value = currentDisaster.severity;
            document.getElementById('edit-status').value = currentDisaster.status;

            document.getElementById('editModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('editModal').classList.remove('active');
            currentDisaster = null;
        }

        async function saveEdit() {
            if (!currentDisaster) return;

            const formData = {
                name: document.getElementById('edit-name').value,
                description: document.getElementById('edit-description').value,
                latitude: parseFloat(document.getElementById('edit-latitude').value),
                longitude: parseFloat(document.getElementById('edit-longitude').value),
                location: document.getElementById('edit-location').value,
                severity: document.getElementById('edit-severity').value,
                status: document.getElementById('edit-status').value,
            };

            try {
                const response = await fetch(`/admin/disasters/${currentDisaster.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(formData)
                });

                if (response.ok) {
                    closeModal();
                    loadDisasters();
                    alert('✅ Disaster updated successfully!');
                } else {
                    const error = await response.json();
                    console.error('Update error:', error);
                    alert('❌ Error updating disaster: ' + (error.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Update error:', error);
                alert('❌ Error updating disaster: ' + error.message);
            }
        }

        async function resolveDisaster(id) {
            if (!confirm('Mark this disaster as resolved?')) return;

            try {
                const response = await fetch(`/admin/disasters/${id}/resolve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    loadDisasters();
                    alert('✅ Disaster marked as resolved!');
                } else {
                    const error = await response.json();
                    console.error('Resolve error:', error);
                    alert('❌ Error resolving disaster: ' + (error.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Resolve error:', error);
                alert('❌ Error resolving disaster: ' + error.message);
            }
        }

        async function deleteDisaster(id) {
            if (!confirm('Are you sure you want to delete this disaster? This cannot be undone.')) return;

            try {
                const response = await fetch(`/admin/disasters/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    loadDisasters();
                    alert('✅ Disaster deleted successfully!');
                } else {
                    const error = await response.json();
                    console.error('Delete error:', error);
                    alert('❌ Error deleting disaster: ' + (error.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Delete error:', error);
                alert('❌ Error deleting disaster: ' + error.message);
            }
        }

        // Close modal on outside click
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" style="display: flex; align-items: center; gap: 0.5rem;"><span class="material-icons" style="font-size: 28px;">edit</span> Edit Disaster</div>

            <div class="form-group">
                <label>Disaster Name</label>
                <input type="text" id="edit-name" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea id="edit-description"></textarea>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Latitude</label>
                    <input type="number" step="0.000001" id="edit-latitude" required>
                </div>
                <div class="form-group">
                    <label>Longitude</label>
                    <input type="number" step="0.000001" id="edit-longitude" required>
                </div>
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" id="edit-location" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Severity</label>
                    <select id="edit-severity" required>
                        <option value="low">Low</option>
                        <option value="moderate">Moderate</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="edit-status" required>
                        <option value="active">Active</option>
                        <option value="monitoring">Monitoring</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>
            </div>

            <div class="modal-actions">
                <button onclick="closeModal()" style="background: #374151; color: white;">Cancel</button>
                <button onclick="saveEdit()" style="background: #3b82f6; color: white; display: flex; align-items: center; justify-content: center; gap: 0.5rem;"><span class="material-icons" style="font-size: 18px;">save</span> Save Changes</button>
            </div>
        </div>
    </div>
</body>
</html>
