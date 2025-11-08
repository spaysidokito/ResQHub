import { useState, useEffect, useRef } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import RealMap from '@/components/RealMap';
import WindyMap from '@/components/WindyMap';
import EarthquakeList from '@/components/EarthquakeList';
import DisasterPanel from '@/components/DisasterPanel';
import Chatbot from '@/components/Chatbot';
import AlertPanel from '@/components/AlertPanel';
import SettingsModal from '@/components/SettingsModal';
import FloodMonitoring from '@/components/FloodMonitoring';
import OfflineIndicator from '@/components/OfflineIndicator';
import OfflineSafetyTips from '@/components/OfflineSafetyTips';
import { useOffline } from '@/hooks/useOffline';
import RefreshIcon from '@mui/icons-material/Refresh';
import SettingsIcon from '@mui/icons-material/Settings';
import ChatIcon from '@mui/icons-material/Chat';
import NotificationsIcon from '@mui/icons-material/Notifications';
import PublicIcon from '@mui/icons-material/Public';
import WavesIcon from '@mui/icons-material/Waves';
import TyphoonIcon from '@mui/icons-material/Cyclone';
import LocalFireDepartmentIcon from '@mui/icons-material/LocalFireDepartment';
import WarningIcon from '@mui/icons-material/Warning';
import LogoutIcon from '@mui/icons-material/Logout';
import PersonIcon from '@mui/icons-material/Person';
import AdminPanelSettingsIcon from '@mui/icons-material/AdminPanelSettings';
import CloudIcon from '@mui/icons-material/Cloud';

interface Earthquake {
  id: number;
  external_id: string;
  magnitude: number;
  location: string;
  latitude: number;
  longitude: number;
  depth: number;
  occurred_at: string;
  source: string;
}

interface Disaster {
  id: number;
  external_id: string;
  type: 'flood' | 'typhoon' | 'fire' | 'earthquake';
  name: string;
  description: string;
  latitude: number;
  longitude: number;
  location: string;
  country: string;
  severity: 'low' | 'moderate' | 'high' | 'critical';
  status: 'active' | 'monitoring' | 'resolved';
  started_at: string;
  source: string;
}

export default function Dashboard({
  earthquakes: initialEarthquakes = [],
  disasters: initialDisasters = []
}: {
  earthquakes?: Earthquake[];
  disasters?: Disaster[];
}) {
  const [earthquakes, setEarthquakes] = useState<Earthquake[]>(initialEarthquakes);
  const [disasters, setDisasters] = useState<Disaster[]>(initialDisasters);
  const [activeTab, setActiveTab] = useState<'all' | 'earthquake' | 'flood' | 'typhoon' | 'fire'>('all');
  const [showSettings, setShowSettings] = useState(false);
  const [showChatbot, setShowChatbot] = useState(false);
  const [unreadAlerts, setUnreadAlerts] = useState(0);
  const [showUserMenu, setShowUserMenu] = useState(false);
  const [mapView, setMapView] = useState<'disasters' | 'weather'>('disasters');
  const [showOfflineTips, setShowOfflineTips] = useState(false);
  const [userLocation, setUserLocation] = useState<any>(null);
  const alertPanelRef = useRef<HTMLDivElement>(null);
  const { auth } = usePage().props as any;
  const { isOnline, saveOfflineData, loadOfflineData } = useOffline();

  useEffect(() => {
    fetchUnreadAlerts();
    fetchUserLocation();
    const interval = setInterval(() => {
      refreshData();
      fetchUnreadAlerts();
    }, 60000);

    return () => clearInterval(interval);
  }, []);

  const fetchUserLocation = async () => {
    try {
      const response = await fetch('/api/preferences');
      if (response.ok) {
        const data = await response.json();
        const location = {
          latitude: data.latitude || 14.5995,
          longitude: data.longitude || 120.9842,
          location_name: data.location_name || 'Manila, Philippines',
          radius_km: data.radius_km || 100,
        };
        console.log('User location loaded:', location);
        setUserLocation(location);
      } else {

        const defaultLocation = {
          latitude: 14.5995,
          longitude: 120.9842,
          location_name: 'Manila, Philippines',
          radius_km: 100,
        };
        console.log('Using default location:', defaultLocation);
        setUserLocation(defaultLocation);
      }
    } catch (error) {
      console.error('Error fetching user location:', error);

      const defaultLocation = {
        latitude: 14.5995,
        longitude: 120.9842,
        location_name: 'Manila, Philippines',
        radius_km: 100,
      };
      console.log('Using default location after error:', defaultLocation);
      setUserLocation(defaultLocation);
    }
  };

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (showUserMenu) {
        const target = event.target as HTMLElement;
        if (!target.closest('.user-menu-container')) {
          setShowUserMenu(false);
        }
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, [showUserMenu]);

  const refreshData = async () => {

    if (!isOnline) {
      const offlineData = loadOfflineData();
      setEarthquakes(offlineData.earthquakes || []);
      setDisasters(offlineData.disasters || []);
      return;
    }

    try {
      const [eqResponse, disResponse] = await Promise.all([
        fetch('/api/earthquakes'),
        fetch('/api/disasters')
      ]);

      const eqData = await eqResponse.json();
      const disData = await disResponse.json();

      const newEarthquakes = eqData.earthquakes || [];
      const newDisasters = disData.disasters || [];

      setEarthquakes(newEarthquakes);
      setDisasters(newDisasters);

      saveOfflineData({
        earthquakes: newEarthquakes,
        disasters: newDisasters,
      });

    } catch (error) {
      console.error('Error fetching data:', error);

      const offlineData = loadOfflineData();
      if (offlineData.earthquakes.length > 0 || offlineData.disasters.length > 0) {
        setEarthquakes(offlineData.earthquakes);
        setDisasters(offlineData.disasters);
      }
    }
  };

  const fetchUnreadAlerts = async () => {
    try {
      const response = await fetch('/api/alerts/unread-count');
      const data = await response.json();
      setUnreadAlerts(data.count);
    } catch (error) {
      console.error('Error fetching alerts:', error);
    }
  };

  const filteredDisasters = activeTab === 'all'
    ? disasters
    : disasters.filter(d => d.type === activeTab);

  const allEvents = activeTab === 'earthquake' ? earthquakes : filteredDisasters;

  return (
    <>
      <Head title="ResQHub - Multi-Disaster Monitoring (Philippines)" />

      <div className="min-h-screen bg-black text-white">
        {}
        <header className="bg-gradient-to-r from-red-900 to-black border-b-2 border-red-600 px-4 md:px-6 py-3 relative z-50">
          <div className="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-3">
            <div className="flex items-center space-x-4">
              {}
              <div className="bg-white px-4 py-2 rounded-xl shadow-lg">
                <img
                  src="/images/resqhub-logo.png"
                  alt="ResQHub Logo"
                  className="h-12 w-auto object-contain"
                  onError={(e) => {

                    e.currentTarget.style.display = 'none';
                    const fallback = e.currentTarget.parentElement?.nextElementSibling;
                    if (fallback) (fallback as HTMLElement).style.display = 'flex';
                  }}
                />
              </div>
              {}
              <div className="w-12 h-12 bg-red-600 rounded-xl items-center justify-center hidden">
                <PublicIcon sx={{ fontSize: 32, color: 'white' }} />
              </div>
              <div>
                <p className="text-xs text-gray-400">Philippines Multi-Disaster Monitoring System</p>
              </div>
            </div>

            <div className="flex items-center gap-2 flex-wrap justify-center">
              <a
                href="/report-disaster"
                className="px-3 py-2 bg-orange-600 hover:bg-orange-700 rounded-lg transition text-sm flex items-center gap-1 font-semibold"
              >
                <WarningIcon sx={{ fontSize: 18 }} />
                <span className="hidden sm:inline">Report Disaster</span>
              </a>

              <button
                onClick={() => refreshData()}
                className="px-3 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition text-sm flex items-center gap-1"
              >
                <RefreshIcon sx={{ fontSize: 18 }} />
                <span className="hidden sm:inline">Refresh</span>
              </button>

              <button
                onClick={() => setShowSettings(true)}
                className="px-3 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg transition text-sm flex items-center gap-1"
              >
                <SettingsIcon sx={{ fontSize: 18 }} />
                <span className="hidden sm:inline">Settings</span>
              </button>

              <button
                onClick={() => setShowChatbot(!showChatbot)}
                className="px-3 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition text-sm flex items-center gap-1"
              >
                <ChatIcon sx={{ fontSize: 18 }} />
                <span className="hidden sm:inline">ResQBot</span>
              </button>

              <div className="relative">
                <button
                  onClick={() => {
                    alertPanelRef.current?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                  }}
                  className="px-3 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg transition text-sm flex items-center gap-1"
                >
                  <NotificationsIcon sx={{ fontSize: 18 }} />
                  <span className="hidden sm:inline">Alerts</span>
                </button>
                {unreadAlerts > 0 && (
                  <span className="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                    {unreadAlerts}
                  </span>
                )}
              </div>

              {}
              <div className="relative user-menu-container">
                <button
                  onClick={() => setShowUserMenu(!showUserMenu)}
                  className="px-3 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg transition text-sm flex items-center gap-1"
                >
                  <PersonIcon sx={{ fontSize: 18 }} />
                  <span className="hidden sm:inline">{auth?.user?.name || 'User'}</span>
                </button>

                {showUserMenu && (
                  <div className="absolute right-0 mt-2 w-64 bg-gray-800 border-2 border-red-600 rounded-lg shadow-lg z-[9999]">
                    <div className="p-2">
                      <div className="px-3 py-2 text-xs text-gray-400 border-b border-gray-700 truncate" title={auth?.user?.email}>
                        {auth?.user?.email}
                      </div>

                      {auth?.user?.role === 'admin' && (
                        <a
                          href="/admin/disasters"
                          className="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-700 rounded mt-2 transition"
                        >
                          <AdminPanelSettingsIcon sx={{ fontSize: 18 }} />
                          Admin Dashboard
                        </a>
                      )}

                      <button
                        onClick={() => router.post('/logout')}
                        className="w-full flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-700 rounded mt-1 transition text-red-400"
                      >
                        <LogoutIcon sx={{ fontSize: 18 }} />
                        Logout
                      </button>
                    </div>
                  </div>
                )}
              </div>
            </div>
          </div>
        </header>

        {}
        <div className="bg-gray-900 border-b border-gray-800 px-4 md:px-6 py-3 overflow-x-auto">
          <div className="max-w-7xl mx-auto flex gap-2 min-w-max md:min-w-0">
            {[
              { key: 'all', label: 'All Disasters', Icon: WarningIcon },
              { key: 'earthquake', label: 'Earthquakes', Icon: PublicIcon },
              { key: 'flood', label: 'Floods', Icon: WavesIcon },
              { key: 'typhoon', label: 'Typhoons', Icon: TyphoonIcon },
              { key: 'fire', label: 'Fires', Icon: LocalFireDepartmentIcon },
            ].map((tab) => (
              <button
                key={tab.key}
                onClick={() => setActiveTab(tab.key as any)}
                className={`px-4 py-2 rounded-lg transition whitespace-nowrap text-sm md:text-base flex items-center gap-2 ${
                  activeTab === tab.key
                    ? 'bg-red-600 text-white'
                    : 'bg-gray-800 text-gray-400 hover:bg-gray-700'
                }`}
              >
                <tab.Icon sx={{ fontSize: 20 }} />
                {tab.label}
              </button>
            ))}
          </div>
        </div>

        {}
        <main className="max-w-7xl mx-auto px-4 md:px-6 py-4 md:py-8">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
            {}
            <motion.div
              className="lg:col-span-2 space-y-4 md:space-y-6"
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.5 }}
            >
              {}
              <motion.div
                className="bg-gray-900 rounded-lg border-2 border-red-600 p-4 md:p-6"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: 0.1 }}
                whileHover={{ scale: 1.01 }}
 >
                <div className="flex items-center justify-between mb-4">
                  <h2 className="text-xl md:text-2xl font-bold text-red-500">
                    {mapView === 'disasters' ? 'Live Disaster Map' : 'Weather Map'}
                  </h2>
                  <div className="flex gap-2">
                    <button
                      onClick={() => setMapView('disasters')}
                      className={`px-3 py-1 rounded-lg text-sm flex items-center gap-1 transition ${
                        mapView === 'disasters'
                          ? 'bg-red-600 text-white'
                          : 'bg-gray-800 text-gray-400 hover:bg-gray-700'
                      }`}
                    >
                      <PublicIcon sx={{ fontSize: 16 }} />
                      Disasters
                    </button>
                    <button
                      onClick={() => setMapView('weather')}
                      className={`px-3 py-1 rounded-lg text-sm flex items-center gap-1 transition ${
                        mapView === 'weather'
                          ? 'bg-red-600 text-white'
                          : 'bg-gray-800 text-gray-400 hover:bg-gray-700'
                      }`}
                    >
                      <CloudIcon sx={{ fontSize: 16 }} />
                      Weather
                    </button>
                  </div>
                </div>
                {mapView === 'disasters' ? (
                  <RealMap earthquakes={earthquakes} disasters={disasters} userLocation={userLocation} />
                ) : (
                  <WindyMap />
                )}
              </motion.div>

              {}
              <motion.div
                className="bg-gray-900 rounded-lg border-2 border-red-600 p-4 md:p-6"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: 0.2 }}
                whileHover={{ scale: 1.01 }}
              >
                <h2 className="text-xl md:text-2xl font-bold text-red-500 mb-4">
                  {activeTab === 'all' ? 'All Active Disasters' :
                   activeTab === 'earthquake' ? 'Recent Earthquakes' :
                   `Active ${activeTab.charAt(0).toUpperCase() + activeTab.slice(1)}s`}
                </h2>
                {activeTab === 'earthquake' ? (
                  <EarthquakeList earthquakes={earthquakes} />
                ) : (
                  <DisasterPanel disasters={filteredDisasters} />
                )}
              </motion.div>
            </motion.div>

            {}
            <motion.div
              className="space-y-4 md:space-y-6"
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
     transition={{ duration: 0.5, delay: 0.3 }}
            >
              <div ref={alertPanelRef}>
                <AlertPanel onUnreadChange={setUnreadAlerts} />
              </div>

              <FloodMonitoring />

              {}
              {(!isOnline || showOfflineTips) && <OfflineSafetyTips />}

              <div className="bg-gray-900 rounded-lg border-2 border-red-600 p-4 md:p-6">
                <h3 className="text-lg md:text-xl font-bold text-red-500 mb-4">Quick Stats</h3>
                <div className="space-y-3 text-sm md:text-base">
                  <div className="flex justify-between items-center">
                    <span className="text-gray-400">Active Disasters:</span>
                    <span className="text-white font-bold">{disasters.length}</span>
                  </div>
                  <div className="flex justify-between items-center">
                    <span className="text-gray-400">Earthquakes Today:</span>
                    <span className="text-white font-bold">{earthquakes.length}</span>
                  </div>
                  <div className="flex justify-between items-center">
                    <span className="text-gray-400">Critical Alerts:</span>
                    <span className="text-red-500 font-bold">
                      {disasters.filter(d => d.severity === 'critical').length}
                    </span>
                  </div>
                  <div className="flex justify-between items-center">
                    <span className="text-gray-400">Unread Alerts:</span>
                    <span className="text-yellow-500 font-bold">{unreadAlerts}</span>
                  </div>
                </div>
              </div>

              <div className="bg-gray-900 rounded-lg border-2 border-red-600 p-4 md:p-6">
                <h3 className="text-lg md:text-xl font-bold text-red-500 mb-4">Safety Tips</h3>
                <ul className="space-y-2 text-xs md:text-sm text-gray-300">
                  <li>🔹 Stay informed through official channels</li>
                  <li>🔹 Have an emergency kit ready</li>
                  <li>🔹 Know your evacuation routes</li>
                  <li>🔹 Follow local authority instructions</li>
                  <li>🔹 Keep emergency contacts handy</li>
                </ul>
              </div>
            </motion.div>
          </div>
        </main>

        {}
        {showChatbot && (
          <Chatbot onClose={() => setShowChatbot(false)} />
        )}

        {}
        <AnimatePresence>
          {showSettings && (
            <SettingsModal
              onClose={() => setShowSettings(false)}
              onSave={() => {
                console.log('Settings saved, refreshing user location...');
                fetchUserLocation();
              }}
            />
          )}
        </AnimatePresence>

        {}
        <OfflineIndicator />
      </div>
    </>
  );
}
