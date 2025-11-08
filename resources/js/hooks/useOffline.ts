import { useState, useEffect } from 'react';
import { offlineStorage } from '@/services/offlineStorage';

export function useOffline() {
  const [isOnline, setIsOnline] = useState(navigator.onLine);
  const [hasOfflineData, setHasOfflineData] = useState(false);

  useEffect(() => {

    setHasOfflineData(offlineStorage.hasOfflineData());


    const handleOnline = () => {
      setIsOnline(true);
    };

    const handleOffline = () => {
      setIsOnline(false);
    };

    window.addEventListener('online', handleOnline);
    window.addEventListener('offline', handleOffline);

    return () => {
      window.removeEventListener('online', handleOnline);
      window.removeEventListener('offline', handleOffline);
    };
  }, []);

  
  const saveOfflineData = (data: {
    earthquakes?: any[];
    disasters?: any[];
    alerts?: any[];
  }) => {
    if (data.earthquakes) {
      offlineStorage.saveEarthquakes(data.earthquakes);
    }
    if (data.disasters) {
      offlineStorage.saveDisasters(data.disasters);
    }
    if (data.alerts) {
      offlineStorage.saveAlerts(data.alerts);
    }
    setHasOfflineData(true);
  };

  
  const loadOfflineData = () => {
    return {
      earthquakes: offlineStorage.getEarthquakes(),
      disasters: offlineStorage.getDisasters(),
      alerts: offlineStorage.getAlerts(),
    };
  };

  
  const clearOfflineCache = () => {
    offlineStorage.clearCache();
    setHasOfflineData(false);
  };

  return {
    isOnline,
    hasOfflineData,
    saveOfflineData,
    loadOfflineData,
    clearOfflineCache,
  };
}
