/**
 * Offline Storage Service
 * Manages local storage for offline functionality
 */

interface CachedData {
  earthquakes: any[];
  disasters: any[];
  alerts: any[];
  lastUpdate: string;
}

interface SafetyTip {
  id: string;
  type: 'earthquake' | 'typhoon' | 'flood' | 'fire';
  title: string;
  content: string;
}

const STORAGE_KEYS = {
  DISASTERS: 'resqhub_disasters',
  EARTHQUAKES: 'resqhub_earthquakes',
  ALERTS: 'resqhub_alerts',
  SAFETY_TIPS: 'resqhub_safety_tips',
  LAST_UPDATE: 'resqhub_last_update',
  USER_PREFERENCES: 'resqhub_preferences',
};

class OfflineStorageService {
  /**
   * Save disasters to local storage
   */
  saveDisasters(disasters: any[]): void {
    try {
      localStorage.setItem(STORAGE_KEYS.DISASTERS, JSON.stringify(disasters));
      this.updateLastUpdate();
    } catch (error) {
      console.error('Error saving disasters to local storage:', error);
    }
  }

  /**
   * Get disasters from local storage
   */
  getDisasters(): any[] {
    try {
      const data = localStorage.getItem(STORAGE_KEYS.DISASTERS);
      return data ? JSON.parse(data) : [];
    } catch (error) {
      console.error('Error reading disasters from local storage:', error);
      return [];
    }
  }

  /**
   * Save earthquakes to local storage
   */
  saveEarthquakes(earthquakes: any[]): void {
    try {
      localStorage.setItem(STORAGE_KEYS.EARTHQUAKES, JSON.stringify(earthquakes));
      this.updateLastUpdate();
    } catch (error) {
      console.error('Error saving earthquakes to local storage:', error);
    }
  }

  /**
   * Get earthquakes from local storage
   */
  getEarthquakes(): any[] {
    try {
      const data = localStorage.getItem(STORAGE_KEYS.EARTHQUAKES);
      return data ? JSON.parse(data) : [];
    } catch (error) {
      console.error('Error reading earthquakes from local storage:', error);
      return [];
    }
  }

  /**
   * Save alerts to local storage
   */
  saveAlerts(alerts: any[]): void {
    try {
      localStorage.setItem(STORAGE_KEYS.ALERTS, JSON.stringify(alerts));
      this.updateLastUpdate();
    } catch (error) {
      console.error('Error saving alerts to local storage:', error);
    }
  }

  /**
   * Get alerts from local storage
   */
  getAlerts(): any[] {
    try {
      const data = localStorage.getItem(STORAGE_KEYS.ALERTS);
      return data ? JSON.parse(data) : [];
    } catch (error) {
      console.error('Error reading alerts from local storage:', error);
      return [];
    }
  }

  /**
   * Save safety tips to local storage
   */
  saveSafetyTips(): void {
    const safetyTips: SafetyTip[] = [
      {
        id: 'earthquake_1',
        type: 'earthquake',
        title: 'During an Earthquake',
        content: 'DROP, COVER, and HOLD ON. Drop to your hands and knees, cover your head and neck, and hold on to sturdy furniture.',
      },
      {
        id: 'earthquake_2',
        type: 'earthquake',
        title: 'After an Earthquake',
        content: 'Check for injuries and damage. Be prepared for aftershocks. Stay away from damaged buildings.',
      },
      {
        id: 'typhoon_1',
        type: 'typhoon',
        title: 'Before a Typhoon',
        content: 'Prepare emergency kit, secure loose objects, know evacuation routes, and monitor PAGASA updates.',
      },
      {
        id: 'typhoon_2',
        type: 'typhoon',
        title: 'During a Typhoon',
        content: 'Stay indoors, away from windows. Do not go outside. Listen to emergency broadcasts.',
      },
      {
        id: 'flood_1',
        type: 'flood',
        title: 'During a Flood',
        content: 'Move to higher ground immediately. Never walk or drive through flood waters. 6 inches can knock you down.',
      },
      {
        id: 'flood_2',
        type: 'flood',
        title: 'After a Flood',
        content: 'Avoid flood water as it may be contaminated. Check for structural damage before entering buildings.',
      },
      {
        id: 'fire_1',
        type: 'fire',
        title: 'During a Fire',
        content: 'Stay low to avoid smoke. Feel doors before opening. Exit quickly and call 911 or BFP.',
      },
      {
        id: 'fire_2',
        type: 'fire',
        title: 'Fire Prevention',
        content: 'Install smoke alarms, have fire extinguishers, and practice escape routes with your family.',
      },
    ];

    try {
      localStorage.setItem(STORAGE_KEYS.SAFETY_TIPS, JSON.stringify(safetyTips));
    } catch (error) {
      console.error('Error saving safety tips:', error);
    }
  }

  /**
   * Get safety tips from local storage
   */
  getSafetyTips(type?: string): SafetyTip[] {
    try {
      const data = localStorage.getItem(STORAGE_KEYS.SAFETY_TIPS);
      const tips = data ? JSON.parse(data) : [];

      if (type) {
        return tips.filter((tip: SafetyTip) => tip.type === type);
      }

      return tips;
    } catch (error) {
      console.error('Error reading safety tips:', error);
      return [];
    }
  }

  /**
   * Update last update timestamp
   */
  private updateLastUpdate(): void {
    try {
      localStorage.setItem(STORAGE_KEYS.LAST_UPDATE, new Date().toISOString());
    } catch (error) {
      console.error('Error updating last update timestamp:', error);
    }
  }

  /**
   * Get last update timestamp
   */
  getLastUpdate(): string | null {
    try {
      return localStorage.getItem(STORAGE_KEYS.LAST_UPDATE);
    } catch (error) {
      console.error('Error reading last update timestamp:', error);
      return null;
    }
  }

  /**
   * Check if data is available offline
   */
  hasOfflineData(): boolean {
    const disasters = this.getDisasters();
    const earthquakes = this.getEarthquakes();
    return disasters.length > 0 || earthquakes.length > 0;
  }

  /**
   * Clear all cached data
   */
  clearCache(): void {
    try {
      Object.values(STORAGE_KEYS).forEach(key => {
        localStorage.removeItem(key);
      });
    } catch (error) {
      console.error('Error clearing cache:', error);
    }
  }

  /**
   * Get cache size in KB
   */
  getCacheSize(): number {
    try {
      let total = 0;
      Object.values(STORAGE_KEYS).forEach(key => {
        const item = localStorage.getItem(key);
        if (item) {
          total += item.length;
        }
      });
      return Math.round(total / 1024); // Convert to KB
    } catch (error) {
      console.error('Error calculating cache size:', error);
      return 0;
    }
  }
}

export const offlineStorage = new OfflineStorageService();

