<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Reports - ResQHub Admin</title>
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
            border: none;
            cursor: pointer;
        }
        .btn:hover { background: #374151; }
        .btn-success {
            background: #10b981;
        }
        .btn-success:hover {
            background: #059669;
        }
        .btn-danger {
            background: #ef4444;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        .container {
            max-width: 1280px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .disaster-card {
            background: #111827;
            border: 2px solid #eab308;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
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
        .badge-unverified {
            background: rgba(234, 179, 8, 0.2);
            color: #eab308;
            border: 1px solid #eab308;
        }
        .badge-critical { background: rgba(220, 38, 38, 0.2); color: #dc2626; border: 1px solid #dc2626; }
        .badge-high { background: rgba(249, 115, 22, 0.2); color: #f97316; border: 1px solid #f97316; }
        .badge-moderate { background: rgba(234, 179, 8, 0.2); color: #eab308; border: 1px solid #eab308; }
        .badge-low { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; }
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
        .actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #374151;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
            background: #111827;
            border: 2px solid #374151;
            border-radius: 0.5rem;
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
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            background: #065f46;
            border: 1px solid #10b981;
            color: #d1fae5;
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
            border: 2px solid #374151;
            border-radius: 0.5rem;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
        }
        .modal-header {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #fff;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #9ca3af;
            font-weight: 500;
        }
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 0.5rem;
            color: #fff;
            font-size: 1rem;
            min-height: 100px;
            resize: vertical;
        }
        .form-group textarea:focus {
            outline: none;
            border-color: #ef4444;
        }
        .modal-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }
        .btn-cancel {
            background: #374151;
        }
        .btn-cancel:hover {
            background: #4b5563;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1 style="display: flex; align-items: center; gap: 0.5rem;">
                <span class="material-icons" style="font-size: 32px;">verified</span> Verify Reports
            </h1>
            <a href="/admin/disasters" class="btn" style="display: flex; align-items: center; gap: 0.5rem;">
                <span class="material-icons" style="font-size: 18px;">arrow_back</span> Back to Dashboard
            </a>
        </div>
    </header>

    <div class="container">
        <div id="message-container"></div>

        <!-- Pending Reports Section -->
        <div style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #eab308; display: flex; align-items: center; gap: 0.5rem;">
                <span class="material-icons">pending_actions</span> Pending Reports
            </h2>
            <div id="pending-container">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Loading pending reports...</p>
                </div>
            </div>
        </div>

        <!-- Verified Reports Section -->
        <div style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #10b981; display: flex; align-items: center; gap: 0.5rem;">
                <span class="material-icons">verified</span> Verified Reports
            </h2>
            <div id="verified-container">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Loading verified reports...</p>
                </div>
            </div>
        </div>

        <!-- Rejected Reports Section -->
        <div>
            <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #ef4444; display: flex; align-items: center; gap: 0.5rem;">
                <span class="material-icons">cancel</span> Rejected Reports
            </h2>
            <div id="rejected-container">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Loading rejected reports...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function loadPendingReports() {
            try {
                const response = await fetch('{{ url("/api/citizen-reports/pending") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const reports = await response.json();
                console.log('Pending reports:', reports);
                displayPendingReports(reports);
            } catch (error) {
                console.error('Error loading pending reports:', error);
                document.getElementById('pending-container').innerHTML =
                    `<div class="empty-state"><p style="color: #ef4444;">Error: ${error.message}</p></div>`;
            }
        }

        function getReportTypeLabel(type) {
            const labels = {
                felt_tremor: 'Felt Tremor/Shaking',
                infrastructure_damage: 'Infrastructure Damage',
                safety_update: 'Safety Update',
                casualty: 'Casualty Report',
                resource_need: 'Resource Need',
                other: 'Other'
            };
            return labels[type] || type;
        }

        async function loadVerifiedReports() {
            try {
                const response = await fetch('{{ url("/api/citizen-reports/verified") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const reports = await response.json();
                console.log('Verified reports:', reports);
                displayVerifiedReports(reports);
            } catch (error) {
                console.error('Error loading verified reports:', error);
                document.getElementById('verified-container').innerHTML =
                    `<div class="empty-state"><p style="color: #ef4444;">Error: ${error.message}</p></div>`;
            }
        }

        function displayPendingReports(reports) {
            const container = document.getElementById('pending-container');

            if (reports.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <span class="material-icons" style="font-size: 64px; color: #10b981; margin-bottom: 1rem;">check_circle</span>
                        <h2 style="color: #10b981; margin-bottom: 1rem;">All Clear!</h2>
                        <p>No pending reports at the moment.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = reports.map(report => `
                <div class="disaster-card" id="report-${report.id}">
                    <div class="disaster-header">
                        <div>
                            <div class="disaster-title">
                                ${getTypeIcon(report.type)} ${report.name}
                                <span class="badge badge-unverified" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <span class="material-icons" style="font-size: 14px;">warning</span> PENDING
                                </span>
                            </div>
                            <div class="disaster-meta">
                                <span class="material-icons" style="font-size: 14px; vertical-align: middle;">person</span> ${report.user ? report.user.name : 'Unknown'} •
                                <span class="material-icons" style="font-size: 14px; vertical-align: middle;">location_on</span> ${report.location} • ${new Date(report.created_at).toLocaleDateString()}
                            </div>
                        </div>
                        <span class="badge badge-${report.severity}">${report.severity.toUpperCase()}</span>
                    </div>
                    <p style="color: #9ca3af; margin-bottom: 1rem;">${report.description || 'No description'}</p>
                    ${report.photo ? `
                        <div style="margin-bottom: 1rem;">
                            <img src="/${report.photo}" alt="Report photo" style="max-width: 100%; max-height: 300px; border-radius: 0.5rem; border: 2px solid #374151;">
                        </div>
                    ` : ''}
                    <div class="disaster-details">
                        <div class="detail-item">
                            <div class="detail-label">Report Type</div>
                            <div class="detail-value">${getReportTypeLabel(report.report_type)}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Disaster Type</div>
                            <div class="detail-value">${report.type ? report.type.charAt(0).toUpperCase() + report.type.slice(1) : 'N/A'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Reported By</div>
                            <div class="detail-value">${report.user ? report.user.name : 'Unknown'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Related Disaster</div>
                            <div class="detail-value">${report.disaster ? report.disaster.name : 'N/A'}</div>
                        </div>
                    </div>
                    <div class="actions">
                        <button onclick="verifyReport(${report.id})" class="btn btn-success" style="display: flex; align-items: center; gap: 0.5rem;">
                            <span class="material-icons" style="font-size: 18px;">check_circle</span> Verify & Approve
                        </button>
                        <button onclick="rejectReport(${report.id})" class="btn btn-danger" style="display: flex; align-items: center; gap: 0.5rem;">
                            <span class="material-icons" style="font-size: 18px;">cancel</span> Reject
                        </button>
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

        function displayVerifiedReports(reports) {
            const container = document.getElementById('verified-container');

            if (reports.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <span class="material-icons" style="font-size: 64px; color: #6b7280; margin-bottom: 1rem;">inbox</span>
                        <p>No verified reports yet.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = reports.map(report => `
                <div class="disaster-card" style="border-color: #10b981;">
                    <div class="disaster-header">
                        <div>
                            <div class="disaster-title">
                                ${getTypeIcon(report.type)} ${report.name}
                                <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <span class="material-icons" style="font-size: 14px;">verified</span> VERIFIED
                                </span>
                            </div>
                            <div class="disaster-meta">
                                <span class="material-icons" style="font-size: 14px; vertical-align: middle;">person</span> ${report.user ? report.user.name : 'Unknown'} •
                                <span class="material-icons" style="font-size: 14px; vertical-align: middle;">location_on</span> ${report.location} • ${new Date(report.created_at).toLocaleDateString()}
                            </div>
                        </div>
                        <span class="badge badge-${report.severity}">${report.severity.toUpperCase()}</span>
                    </div>
                    <p style="color: #9ca3af; margin-bottom: 1rem;">${report.description || 'No description'}</p>
                    ${report.photo ? `
                        <div style="margin-bottom: 1rem;">
                            <img src="/${report.photo}" alt="Report photo" style="max-width: 100%; max-height: 300px; border-radius: 0.5rem; border: 2px solid #374151;">
                        </div>
                    ` : ''}
                    <div class="disaster-details">
                        <div class="detail-item">
                            <div class="detail-label">Type</div>
                            <div class="detail-value">${report.type.charAt(0).toUpperCase() + report.type.slice(1)}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Reported By</div>
                            <div class="detail-value">${report.user ? report.user.name : 'Unknown'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Verified By</div>
                            <div class="detail-value">${report.verified_by_user ? report.verified_by_user.name : 'Admin'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Verified At</div>
                            <div class="detail-value">${new Date(report.verified_at).toLocaleDateString()}</div>
                        </div>
                    </div>
                    ${report.admin_notes ? `
                        <div style="margin-top: 1rem; padding: 1rem; background: #1f2937; border-radius: 0.5rem; border-left: 3px solid #10b981;">
                            <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.25rem;">Admin Notes:</div>
                            <div style="color: #fff;">${report.admin_notes}</div>
                        </div>
                    ` : ''}
                </div>
            `).join('');
        }

        let currentReportId = null;

        function verifyReport(id) {
            currentReportId = id;
            document.getElementById('verify-notes').value = '';
            document.getElementById('verify-modal').classList.add('active');
        }

        function closeVerifyModal() {
            document.getElementById('verify-modal').classList.remove('active');
            currentReportId = null;
        }

        async function submitVerify() {
            if (!currentReportId) return;

            const notes = document.getElementById('verify-notes').value;

            try {
                const response = await fetch(`/api/citizen-reports/${currentReportId}/verify`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ notes })
                });

                if (response.ok) {
                    showMessage('<span class="material-icons" style="font-size: 18px; vertical-align: middle;">check_circle</span> Report verified successfully!');
                    closeVerifyModal();
                    loadPendingReports();
                    loadVerifiedReports();
                } else {
                    showMessage('<span class="material-icons" style="font-size: 18px; vertical-align: middle;">error</span> Error verifying report', true);
                }
            } catch (error) {
                showMessage('<span class="material-icons" style="font-size: 18px; vertical-align: middle;">error</span> Error verifying report', true);
            }
        }

        function rejectReport(id) {
            currentReportId = id;
            document.getElementById('reject-notes').value = '';
            document.getElementById('reject-modal').classList.add('active');
        }

        function closeRejectModal() {
            document.getElementById('reject-modal').classList.remove('active');
            currentReportId = null;
        }

        async function submitReject() {
            if (!currentReportId) return;

            const notes = document.getElementById('reject-notes').value;

            try {
                const response = await fetch(`/api/citizen-reports/${currentReportId}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ notes })
                });

                if (response.ok) {
                    showMessage('<span class="material-icons" style="font-size: 18px; vertical-align: middle;">cancel</span> Report rejected');
                    closeRejectModal();
                    loadPendingReports();
                    loadRejectedReports();
                } else {
                    showMessage('<span class="material-icons" style="font-size: 18px; vertical-align: middle;">error</span> Error rejecting report', true);
                }
            } catch (error) {
                showMessage('<span class="material-icons" style="font-size: 18px; vertical-align: middle;">error</span> Error rejecting report', true);
            }
        }

        function showMessage(message, isError = false) {
            const container = document.getElementById('message-container');
            container.innerHTML = `
                <div class="alert" style="${isError ? 'background: #7f1d1d; border-color: #ef4444; color: #fecaca;' : ''}">
                    ${message}
                </div>
            `;
            setTimeout(() => {
                container.innerHTML = '';
            }, 3000);
        }

        async function loadRejectedReports() {
            try {
                const response = await fetch('{{ url("/api/citizen-reports/rejected") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const reports = await response.json();
                console.log('Rejected reports:', reports);
                displayRejectedReports(reports);
            } catch (error) {
                console.error('Error loading rejected reports:', error);
                document.getElementById('rejected-container').innerHTML =
                    `<div class="empty-state"><p style="color: #ef4444;">Error: ${error.message}</p></div>`;
            }
        }

        function displayRejectedReports(reports) {
            const container = document.getElementById('rejected-container');

            if (reports.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <span class="material-icons" style="font-size: 64px; color: #6b7280; margin-bottom: 1rem;">inbox</span>
                        <p>No rejected reports.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = reports.map(report => `
                <div class="disaster-card" style="border-color: #ef4444; opacity: 0.8;">
                    <div class="disaster-header">
                        <div>
                            <div class="disaster-title">
                                ${getTypeIcon(report.type)} ${report.name}
                                <span class="badge" style="background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid #ef4444; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <span class="material-icons" style="font-size: 14px;">cancel</span> REJECTED
                                </span>
                            </div>
                            <div class="disaster-meta">
                                <span class="material-icons" style="font-size: 14px; vertical-align: middle;">person</span> ${report.user ? report.user.name : 'Unknown'} •
                                <span class="material-icons" style="font-size: 14px; vertical-align: middle;">location_on</span> ${report.location} • ${new Date(report.created_at).toLocaleDateString()}
                            </div>
                        </div>
                        <span class="badge badge-${report.severity}">${report.severity.toUpperCase()}</span>
                    </div>
                    <p style="color: #9ca3af; margin-bottom: 1rem;">${report.description || 'No description'}</p>
                    ${report.photo ? `
                        <div style="margin-bottom: 1rem;">
                            <img src="/${report.photo}" alt="Report photo" style="max-width: 100%; max-height: 300px; border-radius: 0.5rem; border: 2px solid #374151;">
                        </div>
                    ` : ''}
                    <div class="disaster-details">
                        <div class="detail-item">
                            <div class="detail-label">Type</div>
                            <div class="detail-value">${report.type.charAt(0).toUpperCase() + report.type.slice(1)}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Reported By</div>
                            <div class="detail-value">${report.user ? report.user.name : 'Unknown'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Rejected By</div>
                            <div class="detail-value">${report.verified_by_user ? report.verified_by_user.name : 'Admin'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Rejected At</div>
                            <div class="detail-value">${new Date(report.verified_at).toLocaleDateString()}</div>
                        </div>
                    </div>
                    ${report.admin_notes ? `
                        <div style="margin-top: 1rem; padding: 1rem; background: #1f2937; border-radius: 0.5rem; border-left: 3px solid #ef4444;">
                            <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.25rem;">Rejection Reason:</div>
                            <div style="color: #fff;">${report.admin_notes}</div>
                        </div>
                    ` : ''}
                    <div class="actions">
                        <button onclick="deleteReport(${report.id})" class="btn btn-danger" style="display: flex; align-items: center; gap: 0.5rem;">
                            <span class="material-icons" style="font-size: 18px;">delete</span> Delete Permanently
                        </button>
                    </div>
                </div>
            `).join('');
        }

        async function deleteReport(id) {
            if (!confirm('Are you sure you want to permanently delete this report? This action cannot be undone.')) return;

            try {
                const response = await fetch(`/api/citizen-reports/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    showMessage('<span class="material-icons" style="font-size: 18px; vertical-align: middle;">delete</span> Report deleted permanently');
                    loadRejectedReports();
                } else {
                    showMessage('<span class="material-icons" style="font-size: 18px; vertical-align: middle;">error</span> Error deleting report', true);
                }
            } catch (error) {
                showMessage('<span class="material-icons" style="font-size: 18px; vertical-align: middle;">error</span> Error deleting report', true);
            }
        }

        // Load reports on page load
        loadPendingReports();
        loadVerifiedReports();
        loadRejectedReports();

        // Close modal when clicking outside (after DOM is loaded)
        window.addEventListener('DOMContentLoaded', function() {
            const verifyModal = document.getElementById('verify-modal');
            const rejectModal = document.getElementById('reject-modal');

            if (verifyModal) {
                verifyModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeVerifyModal();
                    }
                });
            }

            if (rejectModal) {
                rejectModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeRejectModal();
                    }
                });
            }
        });
    </script>

    <!-- Verify Modal -->
    <div id="verify-modal" class="modal">
        <div class="modal-content">
            <h3 class="modal-header" style="color: #10b981;">
                <span class="material-icons" style="vertical-align: middle;">check_circle</span>
                Verify Report
            </h3>
            <div class="form-group">
                <label>Admin Notes (Optional)</label>
                <textarea id="verify-notes" placeholder="Add any notes about this verification..."></textarea>
            </div>
            <div class="modal-actions">
                <button onclick="closeVerifyModal()" class="btn btn-cancel">Cancel</button>
                <button onclick="submitVerify()" class="btn btn-success" style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="material-icons" style="font-size: 18px;">check_circle</span>
                    Verify
                </button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="reject-modal" class="modal">
        <div class="modal-content">
            <h3 class="modal-header" style="color: #ef4444;">
                <span class="material-icons" style="vertical-align: middle;">cancel</span>
                Reject Report
            </h3>
            <div class="form-group">
                <label>Reason for Rejection (Optional)</label>
                <textarea id="reject-notes" placeholder="Explain why this report is being rejected..."></textarea>
            </div>
            <div class="modal-actions">
                <button onclick="closeRejectModal()" class="btn btn-cancel">Cancel</button>
                <button onclick="submitReject()" class="btn btn-danger" style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="material-icons" style="font-size: 18px;">cancel</span>
                    Reject
                </button>
            </div>
        </div>
    </div>
</body>
</html>
