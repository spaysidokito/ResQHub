<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ResQHub</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
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
        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: #ef4444;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        h1 {
            font-size: 1.875rem;
            color: #ef4444;
            font-weight: bold;
        }
        .subtitle {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        .refresh-btn {
            padding: 0.5rem 1rem;
            background: #1f2937;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .refresh-btn:hover {
            background: #374151;
        }
        .refresh-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .rotate {
            animation: rotate 1s linear infinite;
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .btn {
            padding: 0.5rem 1rem;
            background: #1f2937;
            color: #fff;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #374151;
        }
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #111827;
            border: 2px solid #ef4444;
            border-radius: 0.5rem;
            padding: 1.5rem;
        }
        .stat-label {
            color: #9ca3af;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
        }
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }
        .card {
            background: #111827;
            border: 2px solid #ef4444;
            border-radius: 0.5rem;
            padding: 1.5rem;
        }
        .card-title {
            font-size: 1.5rem;
            color: #ef4444;
            margin-bottom: 1rem;
            font-weight: bold;
        }
        .disaster-item {
            background: #1f2937;
            border: 2px solid #ef4444;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
        }
        .disaster-item:hover {
            border-color: #dc2626;
        }
        .disaster-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.5rem;
        }
        .disaster-name {
            font-weight: 600;
            font-size: 1rem;
        }
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-critical { background: rgba(220, 38, 38, 0.2); color: #dc2626; border: 2px solid #dc2626; }
        .badge-high { background: rgba(249, 115, 22, 0.2); color: #f97316; border: 2px solid #ef4444; }
        .badge-moderate { background: rgba(234, 179, 8, 0.2); color: #eab308; border: 2px solid #ef4444; }
        .badge-low { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 2px solid #ef4444; }
        .disaster-meta {
            font-size: 0.875rem;
            color: #9ca3af;
        }
        .type-item {
            background: #1f2937;
            border: 2px solid #ef4444;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .type-count {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ef4444;
        }
        .actions {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid #ef4444;
        }
        .action-btn {
            display: block;
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background: #ef4444;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: background 0.3s;
        }
        .action-btn:hover {
            background: #dc2626;
        }
        .action-btn-secondary {
            background: #1f2937;
        }
        .action-btn-secondary:hover {
            background: #374151;
        }
        .stop-btn {
            background: #dc2626;
            color: white;
            border: none;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        .stop-btn:hover {
            background: #b91c1c;
            transform: scale(1.05);
        }
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <div class="logo-icon">
                    <span class="material-icons" style="color: white; font-size: 28px;">dashboard</span>
                </div>
                <div>
                    <h1>Admin Dashboard</h1>
                    <div class="subtitle">Disaster Management System • <span id="lastUpdate">Just now</span></div>
                </div>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <button id="refreshBtn" class="refresh-btn" onclick="refreshData()">
                    <span class="material-icons" id="refreshIcon" style="font-size: 18px;">refresh</span>
                    <span id="refreshText">Refresh</span>
                </button>
                <a href="/dashboard" class="btn">← Back to Dashboard</a>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Disasters</div>
                <div class="stat-value" style="color: #ef4444;">{{ $stats['total_disasters'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Active</div>
                <div class="stat-value" style="color: #f97316;">{{ $stats['active_disasters'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Earthquakes</div>
                <div class="stat-value" style="color: #fca5a5;">{{ $stats['total_earthquakes'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Alerts</div>
                <div class="stat-value" style="color: #eab308;">{{ $stats['total_alerts'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pending Reports</div>
                <div class="stat-value" style="color: #9ca3af;">{{ $stats['unverified_disasters'] }}</div>
            </div>
        </div>

        <div class="content-grid">
            <!-- Recent Disasters -->
            <div class="card">
                <h2 class="card-title">Recent Disasters</h2>
                @forelse($recentDisasters as $disaster)
                    <div class="disaster-item">
                        <div class="disaster-header">
                            <div>
                                <div class="disaster-name">
                                    @if($disaster->type === 'flood')
                                        <span class="material-icons" style="font-size: 20px; vertical-align: middle; color: #3b82f6;">water</span>
                                    @elseif($disaster->type === 'typhoon')
                                        <span class="material-icons" style="font-size: 20px; vertical-align: middle; color: #6366f1;">cyclone</span>
                                    @elseif($disaster->type === 'fire')
                                        <span class="material-icons" style="font-size: 20px; vertical-align: middle; color: #f97316;">local_fire_department</span>
                                    @else
                                        <span class="material-icons" style="font-size: 20px; vertical-align: middle; color: #eab308;">public</span>
                                    @endif
                                    {{ $disaster->name }}
                                    @if(!$disaster->is_verified)
                                        <span class="badge" style="background: rgba(234, 179, 8, 0.2); color: #eab308; border: 2px solid #ef4444;">Unverified</span>
                                    @endif
                                </div>
                                <div class="disaster-meta">
                                    <span class="material-icons" style="font-size: 14px; vertical-align: middle;">location_on</span> {{ $disaster->location }} • {{ $disaster->created_at->format('M d, Y') }}
                                    @if($disaster->source === 'SIMULATION')
                                        <span style="color: #7c3aed; font-weight: 600;"> • <span class="material-icons" style="font-size: 14px; vertical-align: middle;">science</span> SIMULATION</span>
                                    @endif
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <span class="badge badge-{{ $disaster->severity }}">{{ strtoupper($disaster->severity) }}</span>
                                @if($disaster->source === 'SIMULATION')
                                    <form method="POST" action="{{ route('admin.disasters.destroy', $disaster) }}" style="margin: 0;" onsubmit="return confirm('Stop this simulation?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="stop-btn" style="display: flex; align-items: center; gap: 0.25rem;">
                                            <span class="material-icons" style="font-size: 16px;">stop_circle</span> Stop
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="text-align: center; color: #6b7280; padding: 2rem 0;">No disasters recorded yet</p>
                @endforelse
            </div>

            <!-- Disasters by Type -->
            <div class="card">
                <h2 class="card-title">By Type</h2>
                @foreach($disastersByType as $item)
                    <div class="type-item">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            @if($item->type === 'flood')
                                <span class="material-icons" style="color: #3b82f6;">water</span>
                            @elseif($item->type === 'typhoon')
                                <span class="material-icons" style="color: #6366f1;">cyclone</span>
                            @elseif($item->type === 'fire')
                                <span class="material-icons" style="color: #f97316;">local_fire_department</span>
                            @else
                                <span class="material-icons" style="color: #eab308;">public</span>
                            @endif
                            <span style="text-transform: capitalize;">{{ $item->type }}s</span>
                        </div>
                        <div class="type-count">{{ $item->count }}</div>
                    </div>
                @endforeach

                <div class="actions">
                    <h3 style="color: #fff; margin-bottom: 1rem;">Quick Actions</h3>
                    <a href="/admin/disasters/create" class="action-btn" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <span class="material-icons" style="font-size: 18px;">add_circle</span> Create New Disaster
                    </a>
                    <a href="/admin/disasters/list" class="action-btn action-btn-secondary" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <span class="material-icons" style="font-size: 18px;">list</span> View All Disasters
                    </a>
                    <a href="/admin/disasters/verify" class="action-btn action-btn-secondary" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <span class="material-icons" style="font-size: 18px;">verified</span> Review Citizen Reports
                    </a>
                    <button onclick="if(confirm('This will create a simulated disaster for testing. Continue?')) { window.location.href='/admin/disasters/simulate'; }" class="action-btn" style="border: none; cursor: pointer; background: #7c3aed; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <span class="material-icons" style="font-size: 18px;">science</span> Simulate Disaster
                    </button>
                    <button onclick="simulateAlert()" class="action-btn" style="border: none; cursor: pointer; background: #f59e0b; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <span class="material-icons" style="font-size: 18px;">notifications_active</span> Simulate Alert
                    </button>
                    <button onclick="stopTestAlerts()" class="action-btn" style="border: none; cursor: pointer; background: #dc2626; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <span class="material-icons" style="font-size: 18px;">notifications_off</span> Stop Test Alerts
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Popup -->
    @if(session('success'))
    <div id="successPopup" style="position: fixed; top: 20px; right: 20px; background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 1.5rem 2rem; border-radius: 12px; box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3); z-index: 9999; min-width: 300px; animation: slideIn 0.3s ease-out;">
        <div style="display: flex; align-items: start; gap: 1rem;">
            <span class="material-icons" style="font-size: 32px;">check_circle</span>
            <div style="flex: 1;">
                <div style="font-weight: bold; font-size: 1.125rem; margin-bottom: 0.25rem;">Success!</div>
                <div style="font-size: 0.875rem; opacity: 0.95;">{{ session('success') }}</div>
            </div>
            <button onclick="closePopup()" style="background: none; border: none; color: white; cursor: pointer; font-size: 1.5rem; line-height: 1; padding: 0; opacity: 0.8; transition: opacity 0.2s;">×</button>
        </div>
        <div style="margin-top: 1rem; height: 4px; background: rgba(255,255,255,0.3); border-radius: 2px; overflow: hidden;">
            <div id="progressBar" style="height: 100%; background: white; width: 100%; animation: shrink 5s linear;"></div>
        </div>
    </div>

    <style>
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        @keyframes shrink {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>

    <script>
        function closePopup() {
            const popup = document.getElementById('successPopup');
            popup.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => popup.remove(), 300);
        }

        // Auto close after 5 seconds
        setTimeout(closePopup, 5000);
    </script>
    @endif

    <script>
        async function simulateAlert() {
            if (!confirm('This will send a test alert to all users. Continue?')) return;

            try {
                const response = await fetch('/admin/disasters/simulate-alert', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (response.ok) {
                    alert('✅ Test alert sent to all users!');
                    location.reload();
                } else {
                    alert('❌ Error sending alert');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error sending alert');
            }
        }

        async function stopTestAlerts() {
            if (!confirm('This will remove all test alerts and test disasters. Continue?')) return;

            try {
                const response = await fetch('/admin/disasters/stop-test-alerts', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ Error stopping test alerts');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error stopping test alerts');
            }
        }

        // Auto-refresh functionality
        let refreshInterval;
        let lastUpdateTime = new Date();

        function updateLastUpdateTime() {
            const now = new Date();
            const diff = Math.floor((now - lastUpdateTime) / 1000);
            const updateEl = document.getElementById('lastUpdate');

            if (diff < 60) {
                updateEl.textContent = diff === 0 ? 'Just now' : `${diff}s ago`;
            } else if (diff < 3600) {
                updateEl.textContent = `${Math.floor(diff / 60)}m ago`;
            } else {
                updateEl.textContent = lastUpdateTime.toLocaleTimeString();
            }
        }

        async function refreshData() {
            const refreshBtn = document.getElementById('refreshBtn');
            const refreshIcon = document.getElementById('refreshIcon');
            const refreshText = document.getElementById('refreshText');

            refreshBtn.disabled = true;
            refreshIcon.classList.add('rotate');
            refreshText.textContent = 'Refreshing...';

            try {
                const response = await fetch('/admin/disasters/dashboard-stats', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();

                    // Update stats
                    document.querySelectorAll('.stat-value').forEach((el, index) => {
                        const values = [
                            data.stats.total_disasters,
                            data.stats.active_disasters,
                            data.stats.total_earthquakes,
                            data.stats.total_alerts,
                            data.stats.unverified_disasters
                        ];
                        if (el.textContent !== values[index].toString()) {
                            el.style.animation = 'pulse 0.5s';
                            setTimeout(() => el.style.animation = '', 500);
                        }
                        el.textContent = values[index];
                    });

                    lastUpdateTime = new Date();
                    updateLastUpdateTime();

                    // Reload page if there are significant changes
                    const currentTotal = parseInt(document.querySelector('.stat-value').textContent);
                    if (Math.abs(currentTotal - data.stats.total_disasters) > 0) {
                        setTimeout(() => location.reload(), 1000);
                    }
                }
            } catch (error) {
                console.error('Error refreshing data:', error);
            } finally {
                refreshBtn.disabled = false;
                refreshIcon.classList.remove('rotate');
                refreshText.textContent = 'Refresh';
            }
        }

        // Auto-refresh every 10 seconds
        refreshInterval = setInterval(refreshData, 10000);

        // Update "last updated" time every second
        setInterval(updateLastUpdateTime, 1000);

        // Initial update
        updateLastUpdateTime();
    </script>
</body>
</html>
