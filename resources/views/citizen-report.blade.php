<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report On-Ground Conditions - ResQHub</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h1 {
            font-size: 1.875rem;
            color: #ef4444;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn {
            padding: 0.5rem 1rem;
            background: #1f2937;
            color: #fff;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
        }
        .btn:hover { background: #374151; }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .alert-info {
            background: #1e3a8a;
            border: 1px solid #3b82f6;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        .alert-success {
            background: #065f46;
            border: 1px solid #10b981;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            color: #d1fae5;
        }
        .disasters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .disaster-card {
            background: #111827;
            border: 2px solid #374151;
            border-radius: 0.5rem;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .disaster-card:hover {
            border-color: #ef4444;
            transform: translateY(-2px);
        }
        .disaster-card.selected {
            border-color: #ef4444;
            background: #1f2937;
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
        .report-form {
            background: #111827;
            border: 2px solid #ef4444;
            border-radius: 0.5rem;
            padding: 2rem;
            display: none;
        }
        .report-form.active {
            display: block;
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
            min-height: 120px;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-submit:hover {
            background: #dc2626;
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
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
            background: #111827;
            border: 2px solid #374151;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1>
                <span class="material-icons" style="font-size: 32px;">report_problem</span>
                Report On-Ground Conditions
            </h1>
            <a href="/dashboard" class="btn">
                <span class="material-icons" style="font-size: 18px;">arrow_back</span>
                Back to Dashboard
            </a>
        </div>
    </header>

    <div class="container">
        <div class="alert-info">
            <p style="color: #93c5fd; font-weight: 600; margin-bottom: 0.5rem;">
                <span class="material-icons" style="font-size: 18px; vertical-align: middle;">info</span>
                Help Verify Disaster Information
            </p>
            <p style="color: #bfdbfe; font-size: 0.875rem;">
                Select an active disaster below and report what you're experiencing: felt tremors, infrastructure damage, safety updates, or resource needs. Your reports help emergency responders and warn others in your area.
            </p>
        </div>

        @if(session('success'))
            <div class="alert-success">
                <p style="font-weight: 600; margin-bottom: 0.5rem;">
                    <span class="material-icons" style="font-size: 18px; vertical-align: middle;">check_circle</span>
                    Report Submitted!
                </p>
                <p style="font-size: 0.875rem;">{{ session('success') }}</p>
            </div>
        @endif

        <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #ef4444;">
            <span class="material-icons" style="vertical-align: middle;">warning</span>
            Active Disasters
        </h2>

        <div id="disasters-container">
            <div class="loading">
                <div class="spinner"></div>
                <p>Loading active disasters...</p>
            </div>
        </div>

        <div id="report-form-container"></div>
    </div>

    <script>
        let selectedDisaster = null;

        async function loadDisasters() {
            try {
                const response = await fetch('{{ url("/api/disasters") }}?status=active', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                console.log('Disasters data:', data);
                displayDisasters(data.disasters || data.data || data);
            } catch (error) {
                console.error('Error loading disasters:', error);
                document.getElementById('disasters-container').innerHTML =
                    `<div class="empty-state">
                        <p style="color: #ef4444; font-weight: 600;">Error: ${error.message}</p>
                        <p style="margin-top: 0.5rem;">Please check the browser console for details.</p>
                    </div>`;
            }
        }

        function displayDisasters(disasters) {
            const container = document.getElementById('disasters-container');

            if (disasters.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <span class="material-icons" style="font-size: 64px; color: #10b981; margin-bottom: 1rem;">check_circle</span>
                        <h2 style="color: #10b981; margin-bottom: 1rem;">No Active Disasters</h2>
                        <p>There are currently no active disasters to report on.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = `<div class="disasters-grid">${disasters.map(disaster => `
                <div class="disaster-card" onclick="selectDisaster(${disaster.id}, this)">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div>
                            <div style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">
                                ${getTypeIcon(disaster.type)} ${disaster.name}
                            </div>
                            <div style="color: #9ca3af; font-size: 0.875rem;">
                                <span class="material-icons" style="font-size: 14px; vertical-align: middle;">location_on</span> ${disaster.location}
                            </div>
                        </div>
                        <span class="badge badge-${disaster.severity}">${disaster.severity.toUpperCase()}</span>
                    </div>
                    <p style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">${disaster.description || 'No description'}</p>
                    <div style="color: #6b7280; font-size: 0.75rem;">
                        ${new Date(disaster.created_at).toLocaleDateString()} • ${disaster.source}
                    </div>
                </div>
            `).join('')}</div>`;
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

        function selectDisaster(disasterId, element) {
            // Remove previous selection
            document.querySelectorAll('.disaster-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Add selection to clicked card
            element.classList.add('selected');
            selectedDisaster = disasterId;

            // Show report form
            showReportForm(disasterId);
        }

        function showReportForm(disasterId) {
            const formContainer = document.getElementById('report-form-container');
            formContainer.innerHTML = `
                <div class="report-form active" style="margin-top: 2rem;">
                    <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; color: #ef4444;">
                        <span class="material-icons" style="vertical-align: middle;">edit_note</span>
                        Submit Your Report
                    </h3>

                    <form action="{{ route('citizen.report.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="disaster_id" value="${disasterId}">

                        <div class="form-group">
                            <label>What are you reporting? *</label>
                            <select name="report_type" required>
                                <option value="">Select Report Type</option>
                                <option value="felt_tremor">Felt Tremor/Shaking</option>
                                <option value="infrastructure_damage">Infrastructure Damage</option>
                                <option value="safety_update">Safety Update</option>
                                <option value="casualty">Casualty Report</option>
                                <option value="resource_need">Resource Need</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Description *</label>
                            <textarea name="description" required placeholder="Describe what you're experiencing or witnessing... (max 1000 characters)">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>
                                <span class="material-icons" style="font-size: 18px; vertical-align: middle;">photo_camera</span>
                                Upload Photo (Optional)
                            </label>
                            <div style="background: #1f2937; border: 2px dashed #374151; border-radius: 0.5rem; padding: 1.5rem; text-align: center;">
                                <input type="file" name="photo" id="photo" accept="image/*" style="display: none;" onchange="previewImage(event)">
                                <label for="photo" style="cursor: pointer; display: block;">
                                    <div id="preview-container">
                                        <span class="material-icons" style="font-size: 48px; color: #6b7280;">add_photo_alternate</span>
                                        <p style="color: #9ca3af; margin-top: 0.5rem;">Click to upload a photo</p>
                                        <p style="color: #6b7280; font-size: 0.75rem;">JPG, PNG, GIF (Max 5MB)</p>
                                    </div>
                                    <img id="preview" style="display: none; max-width: 100%; max-height: 300px; border-radius: 0.5rem; margin: 0 auto;">
                                </label>
                                <button type="button" id="remove-photo" onclick="removeImage()" style="display: none; margin-top: 0.5rem; padding: 0.5rem 1rem; background: #ef4444; color: white; border: none; border-radius: 0.5rem; cursor: pointer;">
                                    <span class="material-icons" style="font-size: 16px; vertical-align: middle;">delete</span>
                                    Remove Photo
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">
                            <span class="material-icons" style="font-size: 20px;">send</span>
                            Submit Report
                        </button>
                    </form>
                </div>
            `;
        }

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('preview');
                    const previewContainer = document.getElementById('preview-container');
                    const removeBtn = document.getElementById('remove-photo');

                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    previewContainer.style.display = 'none';
                    removeBtn.style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            const photoInput = document.getElementById('photo');
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('preview-container');
            const removeBtn = document.getElementById('remove-photo');

            photoInput.value = '';
            preview.src = '';
            preview.style.display = 'none';
            previewContainer.style.display = 'block';
            removeBtn.style.display = 'none';
        }

        // Load disasters on page load
        loadDisasters();
    </script>
</body>
</html>
