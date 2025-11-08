/**
 * Notification Service
 * Manages browser notifications for alerts
 */

class NotificationService {
  private permission: NotificationPermission = 'default';

  constructor() {
    if ('Notification' in window) {
      this.permission = Notification.permission;
    }
  }

  /**
   * Request notification permission from user
   */
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

  /**
   * Check if notifications are supported and permitted
   */
  isSupported(): boolean {
    return 'Notification' in window && this.permission === 'granted';
  }

  /**
   * Show a notification (DISABLED - notifications removed)
   */
  async show(title: string, options?: NotificationOptions): Promise<void> {
    // Browser notifications disabled
    console.log('Browser notification disabled:', title);
    return;
  }

  /**
   * Show disaster alert notification
   */
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

  /**
   * Show earthquake alert notification
   */
  async showEarthquakeAlert(earthquake: any): Promise<void> {
    const magnitudeEmoji = earthquake.magnitude >= 6 ? '🚨' : earthquake.magnitude >= 4 ? '⚠️' : 'ℹ️';

    await this.show(`${magnitudeEmoji} Earthquake M${earthquake.magnitude}`, {
      body: `Location: ${earthquake.location}\nDepth: ${earthquake.depth}km\nTime: ${new Date(earthquake.occurred_at).toLocaleString()}`,
      tag: `earthquake-${earthquake.id}`,
      requireInteraction: earthquake.magnitude >= 6,
      vibrate: earthquake.magnitude >= 6 ? [200, 100, 200] : [100],
    });
  }

  /**
   * Show general alert notification
   */
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

  /**
   * Show offline mode notification
   */
  async showOfflineNotification(): Promise<void> {
    await this.show('📴 Offline Mode', {
      body: 'You are now offline. Showing cached data from last update.',
      tag: 'offline-mode',
    });
  }

  /**
   * Show online mode notification
   */
  async showOnlineNotification(): Promise<void> {
    await this.show('✅ Back Online', {
      body: 'Connection restored. Refreshing data...',
      tag: 'online-mode',
    });
  }
}

export const notificationService = new NotificationService();
