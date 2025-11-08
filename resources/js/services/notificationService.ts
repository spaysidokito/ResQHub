

class NotificationService {
  private permission: NotificationPermission = 'default';

  constructor() {
    if ('Notification' in window) {
      this.permission = Notification.permission;
    }
  }

  
  async requestPermission(): Promise<boolean> {
    if (!('Notification' in window)) {
      console.warn('This browser does not support notifications');
      return false;
    }

    if (this.permission === 'granted') {
      return true;
    }

    try {
      const permission = await Notification.requestPermission();
      this.permission = permission;
      return permission === 'granted';
    } catch (error) {
      console.error('Error requesting notification permission:', error);
      return false;
    }
  }

  
  isSupported(): boolean {
    return 'Notification' in window && this.permission === 'granted';
  }

  
  async show(title: string, options?: NotificationOptions): Promise<void> {

    console.log('Browser notification disabled:', title);
    return;
  }

  
  async showDisasterAlert(disaster: any): Promise<void> {
    const severityEmoji = {
      critical: '🚨',
      high: '⚠️',
      moderate: '⚡',
      low: 'ℹ️',
    };

    const emoji = severityEmoji[disaster.severity as keyof typeof severityEmoji] || '⚠️';

    await this.show(`${emoji} ${disaster.name}`, {
      body: `${disaster.description}\nLocation: ${disaster.location}\nSeverity: ${disaster.severity.toUpperCase()}`,
      tag: `disaster-${disaster.id}`,
      requireInteraction: disaster.severity === 'critical',
      vibrate: disaster.severity === 'critical' ? [200, 100, 200] : [100],
    });
  }

  
  async showEarthquakeAlert(earthquake: any): Promise<void> {
    const magnitudeEmoji = earthquake.magnitude >= 6 ? '🚨' : earthquake.magnitude >= 4 ? '⚠️' : 'ℹ️';

    await this.show(`${magnitudeEmoji} Earthquake M${earthquake.magnitude}`, {
      body: `Location: ${earthquake.location}\nDepth: ${earthquake.depth}km\nTime: ${new Date(earthquake.occurred_at).toLocaleString()}`,
      tag: `earthquake-${earthquake.id}`,
      requireInteraction: earthquake.magnitude >= 6,
      vibrate: earthquake.magnitude >= 6 ? [200, 100, 200] : [100],
    });
  }

  
  async showAlert(alert: any): Promise<void> {
    const typeEmoji = {
      earthquake: '🌍',
      typhoon: '🌀',
      flood: '🌊',
      fire: '🔥',
    };

    const emoji = typeEmoji[alert.type as keyof typeof typeEmoji] || '⚠️';

    await this.show(`${emoji} ${alert.title}`, {
      body: alert.message,
      tag: `alert-${alert.id}`,
      requireInteraction: alert.severity === 'critical',
    });
  }

  
  async showOfflineNotification(): Promise<void> {
    await this.show('📴 Offline Mode', {
      body: 'You are now offline. Showing cached data from last update.',
      tag: 'offline-mode',
    });
  }

  
  async showOnlineNotification(): Promise<void> {
    await this.show('✅ Back Online', {
      body: 'Connection restored. Refreshing data...',
      tag: 'online-mode',
    });
  }
}

export const notificationService = new NotificationService();
