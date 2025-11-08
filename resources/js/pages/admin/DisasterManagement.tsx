import { Head } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import DashboardIcon from '@mui/icons-material/Dashboard';
import WarningIcon from '@mui/icons-material/Warning';
import CheckCircleIcon from '@mui/icons-material/CheckCircle';
import PublicIcon from '@mui/icons-material/Public';
import AssessmentIcon from '@mui/icons-material/Assessment';
import RefreshIcon from '@mui/icons-material/Refresh';

interface Stats {
  total_disasters: number;
  active_disasters: number;
  total_earthquakes: number;
  total_alerts: number;
  unverified_disasters: number;
}

interface Disaster {
  id: number;
  type: string;
  name: string;
  location: string;
  severity: string;
  status: string;
  created_at: string;
  is_verified: boolean;
}

interface DisasterByType {
  type: string;
  count: number;
}

export default function DisasterManagement({
  stats: initialStats,
  recentDisasters: initialDisasters,
  disastersByType: initialDisastersByType,
}: {
  stats: Stats;
  recentDisasters: Disaster[];
  disastersByType: DisasterByType[];
}) {
  const [stats, setStats] = useState<Stats>(initialStats);
  const [recentDisasters, setRecentDisasters] = useState<Disaster[]>(initialDisasters);
  const [disastersByType, setDisastersByType] = useState<DisasterByType[]>(initialDisastersByType);
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [lastUpdate, setLastUpdate] = useState<Date>(new Date());
  const [hasNewData, setHasNewData] = useState(false);

  // Auto-refresh every 10 seconds
  useEffect(() => {
    const interval = setInterval(() => {
      fetchLatestData();
    }, 10000); // 10 seconds

    return () => clearInterval(interval);
  }, []);

  const fetchLatestData = async () => {
    try {
      setIsRefreshing(true);
      const response = await fetch('/admin/disasters/dashboard-stats', {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      });

      if (response.ok) {
        const data = await response.json();

        // Check if there's new data
        const hasChanges =
          JSON.stringify(data.stats) !== JSON.stringify(stats) ||
          JSON.stringify(data.recentDisasters) !== JSON.stringify(recentDisasters);

        if (hasChanges) {
          setHasNewData(true);
          setTimeout(() => setHasNewData(false), 2000); // Remove highlight after 2 seconds
        }

        setStats(data.stats);
        setRecentDisasters(data.recentDisasters);
        setDisastersByType(data.disastersByType);
        setLastUpdate(new Date());
      }
    } catch (error) {
      console.error('Error fetching latest data:', error);
    } finally {
      setIsRefreshing(false);
    }
  };

  const getTypeIcon = (type: string) => {
    switch (type) {
      case 'flood': return '🌊';
      case 'typhoon': return '🌀';
      case 'fire': return '🔥';
      case 'earthquake': return '🌍';
      default: return '⚠️';
    }
  };

  const getSeverityColor = (severity: string) => {
    switch (severity) {
      case 'critical': return 'text-red-600 bg-red-900/20 border-red-600';
      case 'high': return 'text-orange-600 bg-orange-900/20 border-red-600';
      case 'moderate': return 'text-yellow-600 bg-yellow-900/20 border-red-600';
      case 'low': return 'text-green-500 bg-green-900/20 border-red-600';
      default: return 'text-gray-500 bg-gray-900/20 border-red-600';
    }
  };

  return (
    <>
      <Head title="Admin - Disaster Management" />

      <div className="min-h-screen bg-black text-white">
        {/* Header */}
        <header className="bg-gradient-to-r from-red-900 to-black border-b-2 border-red-600 px-4 md:px-6 py-4">
          <div className="max-w-7xl mx-auto flex items-center justify-between">
            <div className="flex items-center space-x-3">
              <div className="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center">
                <DashboardIcon sx={{ fontSize: 28, color: 'white' }} />
              </div>
              <div>
                <h1 className="text-2xl md:text-3xl font-bold text-red-500">Admin Dashboard</h1>
                <p className="text-xs text-gray-400">
                  Disaster Management System • Last updated: {lastUpdate.toLocaleTimeString()}
                </p>
              </div>
            </div>

            <div className="flex items-center gap-3">
              <button
                onClick={fetchLatestData}
                disabled={isRefreshing}
                className="px-3 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg transition text-sm flex items-center gap-2 disabled:opacity-50"
                title="Refresh data"
              >
                <RefreshIcon className={isRefreshing ? 'animate-spin' : ''} sx={{ fontSize: 18 }} />
                {isRefreshing ? 'Refreshing...' : 'Refresh'}
              </button>
              <a
                href="/dashboard"
                className="px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg transition text-sm"
              >
                ← Back to Dashboard
              </a>
            </div>
          </div>
        </header>

        {/* Main Content */}
        <main className="max-w-7xl mx-auto px-4 md:px-6 py-8">
          {/* Stats Grid */}
          <div className={`grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8 transition-all duration-300 ${hasNewData ? 'scale-[1.01]' : ''}`}>
            <div className="bg-gray-900 border-2 border-red-600 rounded-lg p-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-400 text-sm">Total Disasters</p>
                  <p className="text-3xl font-bold text-white">{stats.total_disasters}</p>
                </div>
                <WarningIcon sx={{ fontSize: 40, color: '#ef4444' }} />
              </div>
            </div>

            <div className="bg-gray-900 border-2 border-red-600 rounded-lg p-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-400 text-sm">Active</p>
                  <p className="text-3xl font-bold text-red-400">{stats.active_disasters}</p>
                </div>
                <AssessmentIcon sx={{ fontSize: 40, color: '#f87171' }} />
              </div>
            </div>

            <div className="bg-gray-900 border-2 border-red-600 rounded-lg p-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-400 text-sm">Earthquakes</p>
                  <p className="text-3xl font-bold text-red-300">{stats.total_earthquakes}</p>
                </div>
                <PublicIcon sx={{ fontSize: 40, color: '#fca5a5' }} />
              </div>
            </div>

            <div className="bg-gray-900 border-2 border-red-600 rounded-lg p-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-400 text-sm">Total Alerts</p>
                  <p className="text-3xl font-bold text-yellow-500">{stats.total_alerts}</p>
                </div>
                <WarningIcon sx={{ fontSize: 40, color: '#eab308' }} />
              </div>
            </div>

            <div className="bg-gray-900 border-2 border-red-600 rounded-lg p-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-400 text-sm">Pending Reports</p>
                  <p className="text-3xl font-bold text-gray-300">{stats.unverified_disasters}</p>
                </div>
                <CheckCircleIcon sx={{ fontSize: 40, color: '#9ca3af' }} />
              </div>
            </div>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {/* Recent Disasters */}
            <div className="lg:col-span-2 bg-gray-900 border-2 border-red-600 rounded-lg p-6">
              <h2 className="text-2xl font-bold text-red-500 mb-4">Recent Disasters</h2>
              <div className="space-y-3">
                {recentDisasters.length === 0 ? (
                  <p className="text-gray-500 text-center py-8">No disasters recorded yet</p>
                ) : (
                  recentDisasters.map((disaster) => (
                    <div
                      key={disaster.id}
                      className="bg-gray-800 border border-red-600 rounded-lg p-4 hover:border-red-500 transition"
                    >
                      <div className="flex items-start justify-between">
                        <div className="flex-1">
                          <div className="flex items-center gap-2 mb-2">
                            <span className="text-2xl">{getTypeIcon(disaster.type)}</span>
                            <h3 className="font-semibold text-white">{disaster.name}</h3>
                            {!disaster.is_verified && (
                              <span className="px-2 py-1 bg-yellow-900/30 text-yellow-500 text-xs rounded border border-red-600">
                                Unverified
                              </span>
                            )}
                          </div>
                          <p className="text-sm text-gray-400">
                            📍 {disaster.location} • {new Date(disaster.created_at).toLocaleDateString()}
                          </p>
                        </div>
                        <div className={`px-3 py-1 rounded text-xs font-bold border ${getSeverityColor(disaster.severity)}`}>
                          {disaster.severity.toUpperCase()}
                        </div>
                      </div>
                    </div>
                  ))
                )}
              </div>
            </div>

            {/* Disasters by Type */}
            <div className="bg-gray-900 border-2 border-red-600 rounded-lg p-6">
              <h2 className="text-2xl font-bold text-red-500 mb-4">By Type</h2>
              <div className="space-y-3">
                {disastersByType.map((item) => (
                  <div
                    key={item.type}
                    className="bg-gray-800 border border-red-600 rounded-lg p-4"
                  >
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-2">
                        <span className="text-2xl">{getTypeIcon(item.type)}</span>
                        <span className="text-white capitalize">{item.type}s</span>
                      </div>
                      <span className="text-2xl font-bold text-red-500">{item.count}</span>
                    </div>
                  </div>
                ))}
              </div>

              <div className="mt-6 pt-6 border-t border-red-600">
                <h3 className="text-lg font-bold text-white mb-3">Quick Actions</h3>
                <div className="space-y-2">
                  <a
                    href="/admin/disasters/create"
                    className="w-full px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition text-sm flex items-center justify-center gap-2"
                  >
                    ➕ Create New Disaster
                  </a>
                  <a
                    href="/admin/disasters"
                    className="w-full px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg transition text-sm flex items-center justify-center gap-2"
                  >
                    📋 View All Disasters
                  </a>
                  <a
                    href="/admin/disasters/verify"
                    className="w-full px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg transition text-sm flex items-center justify-center gap-2"
                  >
                    ✅ Review Citizen Reports
                  </a>
                  <button
                    onClick={() => {
                      if (confirm('This will create a simulated disaster for testing. Continue?')) {
                        window.location.href = '/admin/disasters/simulate';
                      }
                    }}
                    className="w-full px-4 py-2 bg-red-700 hover:bg-red-800 rounded-lg transition text-sm flex items-center justify-center gap-2"
                  >
                    🎭 Simulate Disaster
                  </button>
                </div>
              </div>
            </div>
          </div>

          {/* Info Box */}
          <div className="mt-6 bg-red-900/20 border-2 border-red-600 rounded-lg p-6">
            <h3 className="text-xl font-bold text-red-500 mb-2">Admin Features</h3>
            <p className="text-gray-300 mb-4">
              As an administrator, you have full access to manage all disaster data, verify community reports,
              and oversee the entire monitoring system.
            </p>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
              <div className="bg-gray-900 rounded p-3">
                <p className="font-semibold text-white mb-1">✅ Create & Update</p>
                <p className="text-gray-400">Add new disasters and update existing records</p>
              </div>
              <div className="bg-gray-900 rounded p-3">
                <p className="font-semibold text-white mb-1">✅ Verify Reports</p>
                <p className="text-gray-400">Review and verify community-submitted reports</p>
              </div>
              <div className="bg-gray-900 rounded p-3">
                <p className="font-semibold text-white mb-1">✅ Manage Data</p>
                <p className="text-gray-400">Full control over all disaster information</p>
              </div>
            </div>
          </div>
        </main>
      </div>
    </>
  );
}
