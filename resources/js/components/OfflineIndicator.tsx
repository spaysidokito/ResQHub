import { useState, useEffect } from 'react';
import CloudOffIcon from '@mui/icons-material/CloudOff';
import CloudDoneIcon from '@mui/icons-material/CloudDone';
import { offlineStorage } from '@/services/offlineStorage';

export default function OfflineIndicator() {
  const [isOnline, setIsOnline] = useState(navigator.onLine);
  const [lastUpdate, setLastUpdate] = useState<string | null>(null);
  const [cacheSize, setCacheSize] = useState(0);

  useEffect(() => {
    const handleOnline = () => {
      setIsOnline(true);
    };

    const handleOffline = () => {
      setIsOnline(false);
    };

    window.addEventListener('online', handleOnline);
    window.addEventListener('offline', handleOffline);

    // Update cache info
    const updateCacheInfo = () => {
      setLastUpdate(offlineStorage.getLastUpdate());
      setCacheSize(offlineStorage.getCacheSize());
    };

    updateCacheInfo();
    const interval = setInterval(updateCacheInfo, 30000); // Update every 30 seconds

    return () => {
      window.removeEventListener('online', handleOnline);
      window.removeEventListener('offline', handleOffline);
      clearInterval(interval);
    };
  }, []);

  const formatLastUpdate = (timestamp: string | null) => {
    if (!timestamp) return 'Never';
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const minutes = Math.floor(diff / 60000);

    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours}h ago`;
    return date.toLocaleDateString();
  };

  if (isOnline) {
    return (
      <div className="fixed bottom-4 right-4 z-40 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 text-sm animate-fade-in">
        <CloudDoneIcon sx={{ fontSize: 18 }} />
        <span>Online</span>
      </div>
    );
  }

  return (
    <div className="fixed bottom-4 right-4 z-40 bg-orange-600 text-white px-4 py-3 rounded-lg shadow-lg max-w-xs">
      <div className="flex items-center gap-2 mb-2">
        <CloudOffIcon sx={{ fontSize: 20 }} />
        <span className="font-bold">Offline Mode</span>
      </div>
      <div className="text-xs space-y-1">
        <p>Showing cached data</p>
        {lastUpdate && (
          <p className="text-orange-200">
            Last update: {formatLastUpdate(lastUpdate)}
          </p>
        )}
        {cacheSize > 0 && (
          <p className="text-orange-200">
            Cache: {cacheSize}KB
          </p>
        )}
      </div>
    </div>
  );
}
